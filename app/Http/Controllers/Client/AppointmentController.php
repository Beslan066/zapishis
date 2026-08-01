<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Client;
use App\Services\AppointmentService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    protected $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

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

        $appointments = collect();

        if ($client) {
            $appointments = $client->appointments()
                ->with(['business', 'service', 'employee'])
                ->when($request->has('status'), function ($query) use ($request) {
                    return $query->where('status', $request->status);
                })
                ->orderBy('start_time', 'desc')
                ->paginate(20);
        }

        return view('clients.appointments', compact('appointments'));
    }

    public function history(Request $request)
    {
        $user = $request->user();

        $client = Client::where('user_id', $user->id)->first();

        if (!$client) {
            $client = Client::where('phone', $user->phone)->first();
            if ($client) {
                $client->update(['user_id' => $user->id]);
            }
        }

        $appointments = collect();

        if ($client) {
            $appointments = $client->appointments()
                ->with(['business', 'service', 'employee'])
                ->whereIn('status', ['completed', 'cancelled', 'no_show'])
                ->orderBy('start_time', 'desc')
                ->paginate(20);
        }

        return view('clients.history', compact('appointments'));
    }

    public function show(Request $request, Appointment $appointment)
    {
        $user = $request->user();

        $client = Client::where('user_id', $user->id)->first();

        if (!$client) {
            $client = Client::where('phone', $user->phone)->first();
            if ($client) {
                $client->update(['user_id' => $user->id]);
            }
        }

        $hasAccess = false;

        if ($client && $appointment->client_id == $client->id) {
            $hasAccess = true;
        } elseif ($appointment->client_id == $user->id) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            abort(403, 'Вы не можете просмотреть эту запись');
        }

        $appointment->load(['business', 'service', 'employee', 'client']);

        return view('clients.appointment-show', compact('appointment'));
    }

    public function cancel(Request $request, Appointment $appointment)
    {
        $user = $request->user();

        $client = Client::where('user_id', $user->id)->first();

        if (!$client) {
            $client = Client::where('phone', $user->phone)->first();
            if ($client) {
                $client->update(['user_id' => $user->id]);
            }
        }

        $hasAccess = false;

        if ($client && $appointment->client_id == $client->id) {
            $hasAccess = true;
        } elseif ($appointment->client_id == $user->id) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            abort(403, 'Вы не можете отменить эту запись');
        }

        if (!$appointment->canBeCancelled()) {
            return back()->with('error', 'Эту запись нельзя отменить');
        }

        try {
            $this->appointmentService->cancelAppointment(
                $appointment,
                'Отменено клиентом'
            );

            return back()->with('success', 'Запись успешно отменена');
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при отмене записи: ' . $e->getMessage());
        }
    }
}
