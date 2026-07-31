<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    use ApiResponseTrait;

    protected function getBusiness(Request $request): Business
    {
        $businessId = $request->user()->current_business_id;
        $business = Business::findOrFail($businessId);

        if (!$request->user()->hasBusinessAccess($businessId)) {
            abort(403, 'You do not have access to this business');
        }

        return $business;
    }

    /**
     * Main reports dashboard
     */
    public function index(Request $request)
    {
        $business = $this->getBusiness($request);

        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();
        $yearStart = Carbon::now()->startOfYear();

        // Today's stats
        $todayStats = $business->appointments()
            ->whereDate('start_time', $today)
            ->where('status', 'completed')
            ->selectRaw('
                COUNT(*) as appointments,
                SUM(price) as revenue,
                AVG(price) as average_check
            ')
            ->first();

        // Month stats
        $monthStats = $business->appointments()
            ->whereBetween('start_time', [$monthStart, $monthEnd])
            ->where('status', 'completed')
            ->selectRaw('
                COUNT(*) as total_appointments,
                SUM(price) as total_revenue,
                AVG(price) as average_check,
                COUNT(DISTINCT client_id) as unique_clients
            ')
            ->first();

        // Year stats
        $yearStats = $business->appointments()
            ->whereYear('start_time', now()->year)
            ->where('status', 'completed')
            ->selectRaw('
                COUNT(*) as total_appointments,
                SUM(price) as total_revenue,
                AVG(price) as average_check
            ')
            ->first();

        // Monthly revenue chart (last 12 months)
        $monthlyRevenue = $business->appointments()
            ->where('status', 'completed')
            ->whereYear('start_time', now()->year)
            ->selectRaw('
                MONTH(start_time) as month,
                SUM(price) as revenue
            ')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('revenue', 'month')
            ->toArray();

        // Monthly appointments chart
        $monthlyAppointments = $business->appointments()
            ->whereYear('start_time', now()->year)
            ->selectRaw('
                MONTH(start_time) as month,
                COUNT(*) as count
            ')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Top 5 services
        $topServices = $business->appointments()
            ->where('status', 'completed')
            ->whereBetween('start_time', [$monthStart, $monthEnd])
            ->selectRaw('
                services.name as service_name,
                COUNT(*) as count,
                SUM(price) as revenue
            ')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->groupBy('services.name')
            ->orderBy('revenue', 'desc')
            ->limit(5)
            ->get();

        // Top 5 employees
        $topEmployees = $business->appointments()
            ->where('status', 'completed')
            ->whereBetween('start_time', [$monthStart, $monthEnd])
            ->selectRaw('
                employees.name as employee_name,
                COUNT(*) as count,
                SUM(price) as revenue
            ')
            ->join('employees', 'appointments.employee_id', '=', 'employees.id')
            ->groupBy('employees.name')
            ->orderBy('revenue', 'desc')
            ->limit(5)
            ->get();

        // Client retention (clients who came back)
        $clientRetention = $business->clients()
            ->selectRaw('
                COUNT(*) as total_clients,
                SUM(CASE WHEN total_visits > 1 THEN 1 ELSE 0 END) as returning_clients
            ')
            ->first();

        return $this->successResponse([
            'today' => [
                'appointments' => (int) ($todayStats->appointments ?? 0),
                'revenue' => (float) ($todayStats->revenue ?? 0),
                'average_check' => (float) ($todayStats->average_check ?? 0),
            ],
            'month' => [
                'total_appointments' => (int) ($monthStats->total_appointments ?? 0),
                'total_revenue' => (float) ($monthStats->total_revenue ?? 0),
                'average_check' => (float) ($monthStats->average_check ?? 0),
                'unique_clients' => (int) ($monthStats->unique_clients ?? 0),
            ],
            'year' => [
                'total_appointments' => (int) ($yearStats->total_appointments ?? 0),
                'total_revenue' => (float) ($yearStats->total_revenue ?? 0),
                'average_check' => (float) ($yearStats->average_check ?? 0),
            ],
            'charts' => [
                'monthly_revenue' => $this->fillMissingMonths($monthlyRevenue, 'revenue'),
                'monthly_appointments' => $this->fillMissingMonths($monthlyAppointments, 'count'),
            ],
            'top_services' => $topServices,
            'top_employees' => $topEmployees,
            'client_retention' => [
                'total_clients' => (int) ($clientRetention->total_clients ?? 0),
                'returning_clients' => (int) ($clientRetention->returning_clients ?? 0),
                'retention_rate' => $clientRetention->total_clients > 0
                    ? round(($clientRetention->returning_clients / $clientRetention->total_clients) * 100, 2)
                    : 0,
            ],
        ]);
    }

    /**
     * Revenue report with filters
     */
    public function revenue(Request $request)
    {
        $business = $this->getBusiness($request);

        $validator = Validator::make($request->all(), [
            'period' => 'nullable|in:day,week,month,quarter,year,custom',
            'from_date' => 'required_if:period,custom|date',
            'to_date' => 'required_if:period,custom|date|after_or_equal:from_date',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $period = $request->period ?? 'month';
        $dateRange = $this->getDateRange($period, $request->from_date, $request->to_date);

        // Revenue by day
        $dailyRevenue = $business->appointments()
            ->where('status', 'completed')
            ->whereBetween('start_time', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('
                DATE(start_time) as date,
                SUM(price) as revenue,
                COUNT(*) as appointments,
                AVG(price) as average_check,
                SUM(price) - SUM(discount_applied) as net_revenue
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Revenue by service category
        $revenueByService = $business->appointments()
            ->where('status', 'completed')
            ->whereBetween('start_time', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('
                services.name as service_name,
                SUM(price) as revenue,
                COUNT(*) as appointments
            ')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->groupBy('services.name')
            ->orderBy('revenue', 'desc')
            ->get();

        // Revenue by employee
        $revenueByEmployee = $business->appointments()
            ->where('status', 'completed')
            ->whereBetween('start_time', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('
                employees.name as employee_name,
                SUM(price) as revenue,
                COUNT(*) as appointments,
                SUM(price) * (employees.commission_percent / 100) as commission
            ')
            ->join('employees', 'appointments.employee_id', '=', 'employees.id')
            ->groupBy('employees.name', 'employees.commission_percent')
            ->orderBy('revenue', 'desc')
            ->get();

        // Summary
        $summary = $business->appointments()
            ->where('status', 'completed')
            ->whereBetween('start_time', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('
                COUNT(*) as total_appointments,
                SUM(price) as total_revenue,
                SUM(price) - SUM(discount_applied) as net_revenue,
                AVG(price) as average_check,
                SUM(discount_applied) as total_discounts
            ')
            ->first();

        return $this->successResponse([
            'period' => $period,
            'date_range' => [
                'start' => $dateRange['start']->toISOString(),
                'end' => $dateRange['end']->toISOString(),
            ],
            'summary' => [
                'total_appointments' => (int) ($summary->total_appointments ?? 0),
                'total_revenue' => (float) ($summary->total_revenue ?? 0),
                'net_revenue' => (float) ($summary->net_revenue ?? 0),
                'average_check' => (float) ($summary->average_check ?? 0),
                'total_discounts' => (float) ($summary->total_discounts ?? 0),
            ],
            'daily_breakdown' => $dailyRevenue,
            'by_service' => $revenueByService,
            'by_employee' => $revenueByEmployee,
        ]);
    }

    /**
     * Services report
     */
    public function services(Request $request)
    {
        $business = $this->getBusiness($request);

        $validator = Validator::make($request->all(), [
            'period' => 'nullable|in:week,month,quarter,year,custom',
            'from_date' => 'required_if:period,custom|date',
            'to_date' => 'required_if:period,custom|date|after_or_equal:from_date',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $period = $request->period ?? 'month';
        $dateRange = $this->getDateRange($period, $request->from_date, $request->to_date);

        $services = $business->services()
            ->withCount(['appointments' => function ($query) use ($dateRange) {
                $query->where('status', 'completed')
                    ->whereBetween('start_time', [$dateRange['start'], $dateRange['end']]);
            }])
            ->withSum(['appointments' => function ($query) use ($dateRange) {
                $query->where('status', 'completed')
                    ->whereBetween('start_time', [$dateRange['start'], $dateRange['end']]);
            }], 'price')
            ->withSum(['appointments' => function ($query) use ($dateRange) {
                $query->where('status', 'completed')
                    ->whereBetween('start_time', [$dateRange['start'], $dateRange['end']]);
            }], 'discount_applied')
            ->get();

        $totalRevenue = $services->sum('appointments_sum_price');
        $totalAppointments = $services->sum('appointments_count');

        $formattedServices = $services->map(function ($service) use ($totalRevenue) {
            $revenue = (float) ($service->appointments_sum_price ?? 0);
            $count = (int) ($service->appointments_count ?? 0);

            return [
                'id' => $service->id,
                'name' => $service->name,
                'price' => (float) $service->price,
                'appointments_count' => $count,
                'revenue' => $revenue,
                'discounts' => (float) ($service->appointments_sum_discount_applied ?? 0),
                'percentage_of_total' => $totalRevenue > 0
                    ? round(($revenue / $totalRevenue) * 100, 2)
                    : 0,
                'average_check' => $count > 0
                    ? round($revenue / $count, 2)
                    : 0,
                'is_active' => $service->is_active,
                'duration_minutes' => $service->duration_minutes,
            ];
        });

        return $this->successResponse([
            'period' => $period,
            'date_range' => [
                'start' => $dateRange['start']->toISOString(),
                'end' => $dateRange['end']->toISOString(),
            ],
            'summary' => [
                'total_revenue' => $totalRevenue,
                'total_appointments' => $totalAppointments,
                'average_check' => $totalAppointments > 0
                    ? round($totalRevenue / $totalAppointments, 2)
                    : 0,
            ],
            'services' => $formattedServices,
        ]);
    }

    /**
     * Employees report
     */
    public function employees(Request $request)
    {
        $business = $this->getBusiness($request);

        $validator = Validator::make($request->all(), [
            'period' => 'nullable|in:week,month,quarter,year,custom',
            'from_date' => 'required_if:period,custom|date',
            'to_date' => 'required_if:period,custom|date|after_or_equal:from_date',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $period = $request->period ?? 'month';
        $dateRange = $this->getDateRange($period, $request->from_date, $request->to_date);

        $employees = $business->employees()
            ->withCount(['appointments' => function ($query) use ($dateRange) {
                $query->where('status', 'completed')
                    ->whereBetween('start_time', [$dateRange['start'], $dateRange['end']]);
            }])
            ->withSum(['appointments' => function ($query) use ($dateRange) {
                $query->where('status', 'completed')
                    ->whereBetween('start_time', [$dateRange['start'], $dateRange['end']]);
            }], 'price')
            ->get();

        $totalRevenue = $employees->sum('appointments_sum_price');
        $totalAppointments = $employees->sum('appointments_count');

        $formattedEmployees = $employees->map(function ($employee) use ($totalRevenue) {
            $revenue = (float) ($employee->appointments_sum_price ?? 0);
            $count = (int) ($employee->appointments_count ?? 0);
            $commission = $employee->commission_percent
                ? ($revenue * $employee->commission_percent / 100)
                : 0;

            return [
                'id' => $employee->id,
                'name' => $employee->name,
                'position' => $employee->position,
                'commission_percent' => (float) $employee->commission_percent,
                'appointments_count' => $count,
                'revenue' => $revenue,
                'commission_amount' => round($commission, 2),
                'percentage_of_total' => $totalRevenue > 0
                    ? round(($revenue / $totalRevenue) * 100, 2)
                    : 0,
                'average_check' => $count > 0
                    ? round($revenue / $count, 2)
                    : 0,
                'efficiency' => $this->calculateEmployeeEfficiency($count, $dateRange),
                'is_active' => $employee->is_active,
            ];
        });

        return $this->successResponse([
            'period' => $period,
            'date_range' => [
                'start' => $dateRange['start']->toISOString(),
                'end' => $dateRange['end']->toISOString(),
            ],
            'summary' => [
                'total_revenue' => $totalRevenue,
                'total_appointments' => $totalAppointments,
                'average_check' => $totalAppointments > 0
                    ? round($totalRevenue / $totalAppointments, 2)
                    : 0,
            ],
            'employees' => $formattedEmployees,
        ]);
    }

    /**
     * Clients report
     */
    public function clients(Request $request)
    {
        $business = $this->getBusiness($request);

        $validator = Validator::make($request->all(), [
            'period' => 'nullable|in:week,month,quarter,year,custom',
            'from_date' => 'required_if:period,custom|date',
            'to_date' => 'required_if:period,custom|date|after_or_equal:from_date',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $period = $request->period ?? 'month';
        $dateRange = $this->getDateRange($period, $request->from_date, $request->to_date);

        // Top clients
        $topClients = $business->clients()
            ->withCount(['appointments' => function ($query) use ($dateRange) {
                $query->where('status', 'completed')
                    ->whereBetween('start_time', [$dateRange['start'], $dateRange['end']]);
            }])
            ->withSum(['appointments' => function ($query) use ($dateRange) {
                $query->where('status', 'completed')
                    ->whereBetween('start_time', [$dateRange['start'], $dateRange['end']]);
            }], 'price')
            ->orderBy('appointments_sum_price', 'desc')
            ->limit(20)
            ->get();

        // Client statistics
        $clientStats = $business->clients()
            ->selectRaw('
                COUNT(*) as total_clients,
                SUM(CASE WHEN total_visits > 0 THEN 1 ELSE 0 END) as active_clients,
                SUM(CASE WHEN total_visits > 1 THEN 1 ELSE 0 END) as returning_clients,
                AVG(total_visits) as avg_visits,
                AVG(total_spent) as avg_spent,
                MAX(total_spent) as max_spent,
                MIN(total_spent) as min_spent
            ')
            ->first();

        // Acquisition by source (if you track source)
        $acquisition = $business->clients()
            ->selectRaw('
                COALESCE(metadata->>"$.source", "other") as source,
                COUNT(*) as count
            ')
            ->groupBy('source')
            ->orderBy('count', 'desc')
            ->get();

        // New clients in period
        $newClients = $business->clients()
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();

        return $this->successResponse([
            'period' => $period,
            'date_range' => [
                'start' => $dateRange['start']->toISOString(),
                'end' => $dateRange['end']->toISOString(),
            ],
            'summary' => [
                'total_clients' => (int) ($clientStats->total_clients ?? 0),
                'active_clients' => (int) ($clientStats->active_clients ?? 0),
                'returning_clients' => (int) ($clientStats->returning_clients ?? 0),
                'new_clients' => $newClients,
                'retention_rate' => (int) ($clientStats->total_clients ?? 1) > 0
                    ? round((($clientStats->returning_clients ?? 0) / ($clientStats->total_clients ?? 1)) * 100, 2)
                    : 0,
                'avg_visits' => round($clientStats->avg_visits ?? 0, 2),
                'avg_spent' => (float) ($clientStats->avg_spent ?? 0),
                'max_spent' => (float) ($clientStats->max_spent ?? 0),
                'min_spent' => (float) ($clientStats->min_spent ?? 0),
            ],
            'top_clients' => $topClients,
            'acquisition' => $acquisition,
        ]);
    }

    /**
     * Export report
     */
    public function export(Request $request)
    {
        $business = $this->getBusiness($request);

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:revenue,services,employees,clients',
            'period' => 'nullable|in:week,month,quarter,year,custom',
            'from_date' => 'required_if:period,custom|date',
            'to_date' => 'required_if:period,custom|date|after_or_equal:from_date',
            'format' => 'nullable|in:csv,excel',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $period = $request->period ?? 'month';
        $dateRange = $this->getDateRange($period, $request->from_date, $request->to_date);
        $type = $request->type;

        $data = $this->getExportData($business, $type, $dateRange);
        $filename = "{$type}_report_" . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data, $type) {
            $handle = fopen('php://output', 'w');

            $headers = $this->getExportHeaders($type);
            fputcsv($handle, $headers);

            foreach ($data as $row) {
                fputcsv($handle, (array) $row);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Helper: Get date range
     */
    protected function getDateRange(string $period, ?string $fromDate = null, ?string $toDate = null): array
    {
        $now = Carbon::now();

        if ($period === 'custom' && $fromDate && $toDate) {
            return [
                'start' => Carbon::parse($fromDate)->startOfDay(),
                'end' => Carbon::parse($toDate)->endOfDay(),
            ];
        }

        return match ($period) {
            'day' => [
                'start' => $now->copy()->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
            'week' => [
                'start' => $now->copy()->startOfWeek(),
                'end' => $now->copy()->endOfWeek(),
            ],
            'month' => [
                'start' => $now->copy()->startOfMonth(),
                'end' => $now->copy()->endOfMonth(),
            ],
            'quarter' => [
                'start' => $now->copy()->startOfQuarter(),
                'end' => $now->copy()->endOfQuarter(),
            ],
            'year' => [
                'start' => $now->copy()->startOfYear(),
                'end' => $now->copy()->endOfYear(),
            ],
            default => [
                'start' => $now->copy()->startOfMonth(),
                'end' => $now->copy()->endOfMonth(),
            ],
        };
    }

    /**
     * Helper: Fill missing months in chart data
     */
    protected function fillMissingMonths(array $data, string $key): array
    {
        $result = [];
        for ($month = 1; $month <= 12; $month++) {
            $result[] = [
                'month' => $month,
                'label' => Carbon::create(null, $month)->format('M'),
                $key => $data[$month] ?? 0,
            ];
        }
        return $result;
    }

    /**
     * Helper: Calculate employee efficiency
     */
    protected function calculateEmployeeEfficiency(int $appointments, array $dateRange): float
    {
        $days = $dateRange['start']->diffInDays($dateRange['end']) + 1;
        $workingDays = $days; // Approximate
        return $workingDays > 0 ? round($appointments / $workingDays, 2) : 0;
    }

    /**
     * Helper: Get export data
     */
    protected function getExportData($business, string $type, array $dateRange)
    {
        return match ($type) {
            'revenue' => $business->appointments()
                ->where('status', 'completed')
                ->whereBetween('start_time', [$dateRange['start'], $dateRange['end']])
                ->with(['client', 'service', 'employee'])
                ->get([
                    'start_time',
                    'price',
                    'discount_applied',
                    'status',
                    'client_id',
                    'service_id',
                    'employee_id',
                ])
                ->map(function ($item) {
                    return [
                        'date' => $item->start_time->format('d.m.Y'),
                        'time' => $item->start_time->format('H:i'),
                        'client' => $item->client->full_name ?? 'N/A',
                        'service' => $item->service->name ?? 'N/A',
                        'employee' => $item->employee->name ?? 'N/A',
                        'price' => number_format($item->price, 2),
                        'discount' => number_format($item->discount_applied, 2),
                        'status' => $item->status_label,
                    ];
                }),
            'services' => $business->services()
                ->withCount(['appointments' => function ($query) use ($dateRange) {
                    $query->where('status', 'completed')
                        ->whereBetween('start_time', [$dateRange['start'], $dateRange['end']]);
                }])
                ->withSum(['appointments' => function ($query) use ($dateRange) {
                    $query->where('status', 'completed')
                        ->whereBetween('start_time', [$dateRange['start'], $dateRange['end']]);
                }], 'price')
                ->get()
                ->map(function ($item) {
                    return [
                        'service' => $item->name,
                        'bookings' => $item->appointments_count,
                        'revenue' => number_format($item->appointments_sum_price ?? 0, 2),
                        'price' => number_format($item->price, 2),
                    ];
                }),
            'employees' => $business->employees()
                ->withCount(['appointments' => function ($query) use ($dateRange) {
                    $query->where('status', 'completed')
                        ->whereBetween('start_time', [$dateRange['start'], $dateRange['end']]);
                }])
                ->withSum(['appointments' => function ($query) use ($dateRange) {
                    $query->where('status', 'completed')
                        ->whereBetween('start_time', [$dateRange['start'], $dateRange['end']]);
                }], 'price')
                ->get()
                ->map(function ($item) {
                    $revenue = $item->appointments_sum_price ?? 0;
                    return [
                        'employee' => $item->name,
                        'position' => $item->position ?? 'N/A',
                        'appointments' => $item->appointments_count,
                        'revenue' => number_format($revenue, 2),
                        'commission' => $item->commission_percent
                            ? number_format($revenue * $item->commission_percent / 100, 2)
                            : '0.00',
                    ];
                }),
            'clients' => $business->clients()
                ->withCount(['appointments' => function ($query) use ($dateRange) {
                    $query->where('status', 'completed')
                        ->whereBetween('start_time', [$dateRange['start'], $dateRange['end']]);
                }])
                ->withSum(['appointments' => function ($query) use ($dateRange) {
                    $query->where('status', 'completed')
                        ->whereBetween('start_time', [$dateRange['start'], $dateRange['end']]);
                }], 'price')
                ->orderBy('appointments_sum_price', 'desc')
                ->limit(100)
                ->get()
                ->map(function ($item) {
                    return [
                        'name' => $item->full_name,
                        'phone' => $item->phone,
                        'email' => $item->email ?? 'N/A',
                        'visits' => $item->appointments_count,
                        'total_spent' => number_format($item->appointments_sum_price ?? 0, 2),
                        'last_visit' => $item->last_visit_at?->format('d.m.Y') ?? 'Never',
                    ];
                }),
        };
    }

    /**
     * Helper: Get export headers
     */
    protected function getExportHeaders(string $type): array
    {
        return match ($type) {
            'revenue' => ['Date', 'Time', 'Client', 'Service', 'Employee', 'Price', 'Discount', 'Status'],
            'services' => ['Service', 'Bookings', 'Revenue', 'Price'],
            'employees' => ['Employee', 'Position', 'Appointments', 'Revenue', 'Commission'],
            'clients' => ['Name', 'Phone', 'Email', 'Visits', 'Total Spent', 'Last Visit'],
            default => [],
        };
    }
}
