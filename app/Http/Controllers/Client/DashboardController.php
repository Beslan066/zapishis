<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $client = Client::where('user_id', $user->id)->first();

        if (!$client) {
            $client = Client::where('phone', $user->phone)->first();
            if ($client) {
                $client->update(['user_id' => $user->id]);
            }
        }

        $allAppointments = collect();

        if ($client) {
            $allAppointments = $client->appointments()
                ->with(['business', 'service', 'employee'])
                ->get();
        }

        $stats = [
            'total' => $allAppointments->count(),
            'upcoming' => $allAppointments->where('start_time', '>=', now())
                ->whereIn('status', ['pending', 'confirmed'])->count(),
            'completed' => $allAppointments->where('status', 'completed')->count(),
            'companies' => $allAppointments->pluck('business_id')->unique()->count(),
        ];

        $upcomingAppointments = $allAppointments
            ->where('start_time', '>=', now())
            ->whereIn('status', ['pending', 'confirmed'])
            ->sortBy('start_time')
            ->take(5);

        $historyAppointments = $allAppointments
            ->whereIn('status', ['completed', 'cancelled'])
            ->sortByDesc('start_time')
            ->take(5);

        return view('clients.dashboard', compact('stats', 'upcomingAppointments', 'historyAppointments'));
    }
}
