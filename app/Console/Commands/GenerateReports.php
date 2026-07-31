<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateReports extends Command
{
    protected $signature = 'reports:generate {--business-id= : Specific business ID}';
    protected $description = 'Generate monthly reports for businesses';

    public function handle(): void
    {
        $businesses = $this->option('business-id')
            ? Business::where('id', $this->option('business-id'))->get()
            : Business::all();

        foreach ($businesses as $business) {
            $this->info("Generating report for business: {$business->name}");

            $monthStart = Carbon::now()->startOfMonth();
            $monthEnd = Carbon::now()->endOfMonth();

            // Calculate stats
            $stats = $business->appointments()
                ->where('status', 'completed')
                ->whereBetween('start_time', [$monthStart, $monthEnd])
                ->selectRaw('
                    COUNT(*) as total_appointments,
                    SUM(price) as total_revenue,
                    AVG(price) as average_check,
                    COUNT(DISTINCT client_id) as unique_clients
                ')
                ->first();

            // Top services
            $topServices = $business->appointments()
                ->where('status', 'completed')
                ->whereBetween('start_time', [$monthStart, $monthEnd])
                ->selectRaw('service_id, COUNT(*) as count')
                ->groupBy('service_id')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get()
                ->pluck('service_id')
                ->toArray();

            // Create report
            Report::create([
                'business_id' => $business->id,
                'period' => 'monthly',
                'period_start' => $monthStart,
                'period_end' => $monthEnd,
                'data' => [
                    'total_appointments' => $stats->total_appointments ?? 0,
                    'total_revenue' => $stats->total_revenue ?? 0,
                    'average_check' => $stats->average_check ?? 0,
                    'unique_clients' => $stats->unique_clients ?? 0,
                    'top_services' => $topServices,
                ],
            ]);

            $this->info("Report generated for {$business->name}");
        }

        $this->info('All reports generated successfully');
    }
}
