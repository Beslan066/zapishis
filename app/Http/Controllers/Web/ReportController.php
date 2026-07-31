<?php


namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    protected function getBusiness(Request $request): Business
    {
        $business = $request->user()->currentBusiness;

        if (!$business) {
            abort(403, 'Please create a business first');
        }

        return $business;
    }

    public function index(Request $request)
    {
        $business = $this->getBusiness($request);

        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        // Current month stats
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

        // Monthly revenue chart data
        $monthlyRevenue = $business->appointments()
            ->where('status', 'completed')
            ->whereYear('start_time', now()->year)
            ->selectRaw('
                MONTH(start_time) as month,
                SUM(price) as total
            ')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Top services
        $topServices = $business->appointments()
            ->where('status', 'completed')
            ->whereBetween('start_time', [Carbon::now()->subMonths(3), Carbon::now()])
            ->selectRaw('
                service_id,
                services.name as service_name,
                COUNT(*) as count,
                SUM(price) as revenue
            ')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->groupBy('service_id', 'services.name')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Top employees
        $topEmployees = $business->appointments()
            ->where('status', 'completed')
            ->whereBetween('start_time', [Carbon::now()->subMonths(3), Carbon::now()])
            ->selectRaw('
                employee_id,
                employees.name as employee_name,
                COUNT(*) as count,
                SUM(price) as revenue
            ')
            ->join('employees', 'appointments.employee_id', '=', 'employees.id')
            ->groupBy('employee_id', 'employees.name')
            ->orderBy('revenue', 'desc')
            ->limit(10)
            ->get();

        // Client statistics
        $clientStats = $business->clients()
            ->selectRaw('
                COUNT(*) as total_clients,
                AVG(total_visits) as avg_visits,
                AVG(total_spent) as avg_spent,
                SUM(total_spent) as total_spent
            ')
            ->first();

        // Daily appointments for current week
        $weeklyData = $business->appointments()
            ->whereBetween('start_time', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->selectRaw('
                DATE(start_time) as date,
                COUNT(*) as count,
                SUM(price) as revenue
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('reports.index', compact(
            'monthStats',
            'monthlyRevenue',
            'topServices',
            'topEmployees',
            'clientStats',
            'weeklyData'
        ));
    }

    public function revenue(Request $request)
    {
        $business = $this->getBusiness($request);

        $period = $request->period ?? 'month';
        $dateRange = $this->getDateRange($period);

        $revenue = $business->appointments()
            ->where('status', 'completed')
            ->whereBetween('start_time', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('
                DATE(start_time) as date,
                SUM(price) as total_revenue,
                COUNT(*) as total_appointments,
                AVG(price) as average_check
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $summary = [
            'total_revenue' => $revenue->sum('total_revenue'),
            'total_appointments' => $revenue->sum('total_appointments'),
            'average_check' => $revenue->avg('average_check') ?? 0,
            'period' => $period,
        ];

        return view('reports.revenue', compact('revenue', 'summary', 'period'));
    }

    public function services(Request $request)
    {
        $business = $this->getBusiness($request);

        $period = $request->period ?? 'month';
        $dateRange = $this->getDateRange($period);

        $services = $business->appointments()
            ->where('status', 'completed')
            ->whereBetween('start_time', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('
                service_id,
                services.name as service_name,
                COUNT(*) as total_bookings,
                SUM(price) as total_revenue,
                AVG(price) as average_price
            ')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->groupBy('service_id', 'services.name')
            ->orderBy('total_revenue', 'desc')
            ->get();

        return view('reports.services', compact('services', 'period'));
    }

    public function employees(Request $request)
    {
        $business = $this->getBusiness($request);

        $period = $request->period ?? 'month';
        $dateRange = $this->getDateRange($period);

        $employees = $business->appointments()
            ->where('status', 'completed')
            ->whereBetween('start_time', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('
                employee_id,
                employees.name as employee_name,
                COUNT(*) as total_appointments,
                SUM(price) as total_revenue,
                AVG(price) as average_check,
                SUM(price) * (employees.commission_percent / 100) as commission_amount
            ')
            ->join('employees', 'appointments.employee_id', '=', 'employees.id')
            ->groupBy('employee_id', 'employees.name', 'employees.commission_percent')
            ->orderBy('total_revenue', 'desc')
            ->get();

        $totalRevenue = $employees->sum('total_revenue');

        return view('reports.employees', compact('employees', 'totalRevenue', 'period'));
    }

    public function clients(Request $request)
    {
        $business = $this->getBusiness($request);

        $period = $request->period ?? 'month';
        $dateRange = $this->getDateRange($period);

        $clients = $business->clients()
            ->withCount(['appointments' => function ($query) use ($dateRange) {
                $query->where('status', 'completed')
                    ->whereBetween('start_time', [$dateRange['start'], $dateRange['end']]);
            }])
            ->withSum(['appointments' => function ($query) use ($dateRange) {
                $query->where('status', 'completed')
                    ->whereBetween('start_time', [$dateRange['start'], $dateRange['end']]);
            }], 'price')
            ->orderBy('appointments_sum_price', 'desc')
            ->limit(50)
            ->get();

        return view('reports.clients', compact('clients', 'period'));
    }

    public function export(Request $request)
    {
        $business = $this->getBusiness($request);

        $request->validate([
            'type' => 'required|in:revenue,services,employees,clients',
            'format' => 'required|in:csv,excel',
            'period' => 'nullable|in:week,month,quarter,year',
        ]);

        $period = $request->period ?? 'month';
        $dateRange = $this->getDateRange($period);
        $type = $request->type;

        $data = $this->getExportData($business, $type, $dateRange);
        $filename = "{$type}_report_" . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data, $type) {
            $handle = fopen('php://output', 'w');

            // Headers
            $headers = $this->getExportHeaders($type);
            fputcsv($handle, $headers);

            // Data
            foreach ($data as $row) {
                fputcsv($handle, (array) $row);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function getDateRange(string $period): array
    {
        $now = Carbon::now();

        return match ($period) {
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

    protected function getExportData($business, string $type, array $dateRange)
    {
        return match ($type) {
            'revenue' => $business->appointments()
                ->where('status', 'completed')
                ->whereBetween('start_time', [$dateRange['start'], $dateRange['end']])
                ->with(['client', 'service', 'employee'])
                ->get(['start_time', 'price', 'status']),
            'services' => $business->appointments()
                ->where('status', 'completed')
                ->whereBetween('start_time', [$dateRange['start'], $dateRange['end']])
                ->selectRaw('
                    services.name as service_name,
                    COUNT(*) as total_bookings,
                    SUM(price) as total_revenue
                ')
                ->join('services', 'appointments.service_id', '=', 'services.id')
                ->groupBy('services.name')
                ->get(),
            'employees' => $business->appointments()
                ->where('status', 'completed')
                ->whereBetween('start_time', [$dateRange['start'], $dateRange['end']])
                ->selectRaw('
                    employees.name as employee_name,
                    COUNT(*) as total_appointments,
                    SUM(price) as total_revenue
                ')
                ->join('employees', 'appointments.employee_id', '=', 'employees.id')
                ->groupBy('employees.name')
                ->get(),
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
                ->get(['first_name', 'last_name', 'phone', 'email']),
        };
    }

    protected function getExportHeaders(string $type): array
    {
        return match ($type) {
            'revenue' => ['Date', 'Client', 'Service', 'Employee', 'Price', 'Status'],
            'services' => ['Service', 'Total Bookings', 'Total Revenue'],
            'employees' => ['Employee', 'Total Appointments', 'Total Revenue'],
            'clients' => ['Name', 'Phone', 'Email', 'Total Visits', 'Total Spent'],
        };
    }
}
