<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Business;
use App\Models\Client;
use App\Models\Service;
use App\Services\AppointmentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    protected $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    public function index(Request $request, string $slug)
    {
        $business = Business::where('slug', $slug)
            ->where('status', 'active')
            ->with(['services' => function ($query) {
                $query->where('is_active', true);
            }, 'employees' => function ($query) {
                $query->where('is_active', true);
            }])
            ->firstOrFail();

        return view('public.booking', compact('business'));
    }

    public function store(Request $request, string $slug)
    {
        $business = Business::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $request->validate([
            'service_id' => 'required|exists:services,id',
            'employee_id' => 'nullable|exists:employees,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $startTime = Carbon::parse($request->date . ' ' . $request->time);
        $service = $business->services()->find($request->service_id);
        $endTime = $startTime->copy()->addMinutes($service->duration_minutes);

        // Проверка доступности если выбран сотрудник
        if ($request->employee_id) {
            $isAvailable = $this->appointmentService->isSlotAvailable(
                $request->employee_id,
                $startTime,
                $endTime
            );

            if (!$isAvailable) {
                return back()->with('error', 'Выбранное время занято')->withInput();
            }
        }

        // Ищем клиента в базе по телефону
        $client = Client::where('phone', $request->phone)
            ->where('business_id', $business->id)
            ->first();

        $clientId = $client?->id;
        $guestName = null;
        $guestPhone = null;

        // Если клиент не найден - создаем гостевую запись
        if (!$client) {
            $guestName = $request->first_name . ($request->last_name ? ' ' . $request->last_name : '');
            $guestPhone = $request->phone;
        }

        // Генерируем номер заявки
        $bookingNumber = Appointment::generateBookingNumber();

        $appointmentData = [
            'business_id' => $business->id,
            'client_id' => $clientId,
            'employee_id' => $request->employee_id,
            'service_id' => $request->service_id,
            'created_by_user_id' => Auth::id() ?? null,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'price' => $service->price,
            'status' => 'pending',
            'booking_number' => $bookingNumber,
            'guest_name' => $guestName,
            'guest_phone' => $guestPhone,
            'notes' => $request->notes ?? null,
        ];

        // Если есть email, добавляем в notes для гостя
        if ($request->email && !$client) {
            $appointmentData['notes'] = ($appointmentData['notes'] ?? '') . "\nEmail: " . $request->email;
        }

        $appointment = Appointment::create($appointmentData);

        return redirect()->route('public.booking', $slug)
            ->with('success', "Запись создана! Номер заявки: {$bookingNumber}. Мы отправили подтверждение на ваш телефон.");
    }

    public function availableSlots(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'service_id' => 'required|exists:services,id',
        ]);

        $service = \App\Models\Service::find($request->service_id);

        $slots = $this->appointmentService->getAvailableSlots(
            $request->employee_id,
            $request->date,
            $service->duration_minutes
        );

        return response()->json($slots);
    }


    public function calendarSlots(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $service = Service::find($request->service_id);
        $employeeId = $request->employee_id;

        $startDate = now()->startOfMonth();
        $endDate = now()->addMonth()->endOfMonth();

        $slots = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $slots[$dateStr] = [];

            // Генерируем слоты с 9:00 до 21:00 с шагом 30 минут
            for ($hour = 9; $hour <= 21; $hour++) {
                for ($minute = 0; $minute < 60; $minute += 30) {
                    // Пропускаем обед 13:00-14:00
                    if ($hour == 13) continue;

                    $time = sprintf('%02d:%02d', $hour, $minute);
                    $slots[$dateStr][] = [
                        'start_time' => $time,
                        'is_available' => true,
                    ];
                }
            }

            $currentDate->addDay();
        }

        // Если есть сотрудник, проверяем его занятость
        if ($employeeId) {
            $appointments = Appointment::where('employee_id', $employeeId)
                ->whereBetween('start_time', [$startDate, $endDate])
                ->where('status', '!=', 'cancelled')
                ->get();

            foreach ($appointments as $appointment) {
                $dateStr = $appointment->start_time->format('Y-m-d');
                $timeStr = $appointment->start_time->format('H:i');

                if (isset($slots[$dateStr])) {
                    foreach ($slots[$dateStr] as &$slot) {
                        if ($slot['start_time'] === $timeStr) {
                            $slot['is_available'] = false;
                        }
                    }
                }
            }
        }

        return response()->json($slots);
    }
}
