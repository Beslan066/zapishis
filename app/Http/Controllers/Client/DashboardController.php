<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Все записи пользователя
        $allAppointments = $user->appointments()->with(['business', 'service', 'employee'])->get();

        $stats = [
            'total' => $allAppointments->count(),
            'upcoming' => $allAppointments->where('start_time', '>=', now())
                ->whereIn('status', ['pending', 'confirmed'])->count(),
            'completed' => $allAppointments->where('status', 'completed')->count(),
            'companies' => $allAppointments->pluck('business_id')->unique()->count(),
        ];

        $upcomingAppointments = $user->appointments()
            ->with(['business', 'service', 'employee'])
            ->where('start_time', '>=', now())
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        $historyAppointments = $user->appointments()
            ->with(['business', 'service', 'employee'])
            ->whereIn('status', ['completed', 'cancelled'])
            ->orderBy('start_time', 'desc')
            ->limit(5)
            ->get();

        return view('clients.dashboard', compact('stats', 'upcomingAppointments', 'historyAppointments'));
    }
}
