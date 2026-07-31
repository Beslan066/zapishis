<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $business = $user->currentBusiness;

        if (!$business) {
            return redirect()->route('businesses.create')
                ->with('info', 'Please create your first business');
        }

        $stats = [
            'total_appointments' => $business->appointments()->count(),
            'revenue' => $business->appointments()->completed()->sum('price'),
            'total_clients' => $business->clients()->count(),
            'total_employees' => $business->employees()->count(),
        ];

        $todayAppointments = $business->appointments()
            ->forToday()
            ->with(['client', 'service', 'employee'])
            ->orderBy('start_time')
            ->get();

        return view('dashboard.index', compact('stats', 'todayAppointments'));
    }
}
