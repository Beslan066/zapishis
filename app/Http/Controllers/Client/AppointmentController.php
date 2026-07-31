<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    protected $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    /**
     * Все записи клиента
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $appointments = $user->appointments()
            ->with(['business', 'service', 'employee'])
            ->when($request->has('status'), function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->orderBy('start_time', 'desc')
            ->paginate(20);

        return view('clients.appointments', compact('appointments'));
    }

    /**
     * История записей клиента
     */
    public function history(Request $request)
    {
        $user = $request->user();

        $appointments = $user->appointments()
            ->with(['business', 'service', 'employee'])
            ->whereIn('status', ['completed', 'cancelled', 'no_show'])
            ->orderBy('start_time', 'desc')
            ->paginate(20);

        return view('clients.history', compact('appointments'));
    }

    /**
     * Отмена записи клиентом
     */
    public function cancel(Request $request, Appointment $appointment)
    {
        $user = $request->user();

        // Проверка, что запись принадлежит пользователю
        if ($appointment->client_id !== $user->id && $appointment->created_by_user_id !== $user->id) {
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

    /**
     * Подтверждение записи клиентом (если нужно)
     */
    public function confirm(Request $request, Appointment $appointment)
    {
        $user = $request->user();

        if ($appointment->client_id !== $user->id && $appointment->created_by_user_id !== $user->id) {
            abort(403, 'Вы не можете подтвердить эту запись');
        }

        if (!$appointment->canBeConfirmed()) {
            return back()->with('error', 'Эту запись нельзя подтвердить');
        }

        $appointment->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);

        return back()->with('success', 'Запись подтверждена');
    }

    /**
     * Детали записи
     */
    public function show(Request $request, Appointment $appointment)
    {
        $user = $request->user();

        if ($appointment->client_id !== $user->id && $appointment->created_by_user_id !== $user->id) {
            abort(403, 'Вы не можете просмотреть эту запись');
        }

        $appointment->load(['business', 'service', 'employee', 'client']);

        return view('client.appointment-show', compact('appointment'));
    }
}
