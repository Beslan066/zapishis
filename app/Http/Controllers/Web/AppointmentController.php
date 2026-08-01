<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Appointment;
use App\Models\Service;
use App\Services\AppointmentService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    protected $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    public function index(Request $request)
    {
        $business = $this->getBusiness($request);

        $appointments = $business->appointments()
            ->with(['client', 'service', 'employee'])
            ->when($request->has('status'), function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->when($request->has('date'), function ($query) use ($request) {
                return $query->whereDate('start_time', $request->date);
            })
            ->orderBy('start_time', 'desc')
            ->paginate(20);

        return view('appointments.index', compact('appointments'));
    }


    public function create(Request $request)
    {
        $business = $request->user()->currentBusiness;
        $clients = $business->clients()->orderBy('first_name')->get();
        $employees = $business->employees()->active()->orderBy('name')->get();
        $services = $business->services()->active()->orderBy('name')->get();

        return view('appointments.create', compact('clients', 'employees', 'services'));
    }

    protected function getBusiness(Request $request): Business
    {
        $businessId = $request->user()->current_business_id;

        if (!$businessId) {
            abort(400, 'Business ID is required');
        }

        $business = Business::findOrFail($businessId);

        if (!$request->user()->hasBusinessAccess($businessId)) {
            abort(403, 'У вас нет доступа к этому бизнесу');
        }

        return $business;
    }


    public function store(Request $request)
    {
        Log::info('Store method called', $request->all());

        $business = $this->getBusiness($request);

        $validator = Validator::make($request->all(), [
            'client_id' => 'nullable|exists:clients,id',
            'client_phone' => 'nullable|string|max:20',
            'client_name' => 'nullable|string|max:255',
            'employee_id' => 'nullable|exists:employees,id',
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', $validator->errors()->toArray());
            return back()->withErrors($validator)->withInput();
        }

        $service = $business->services()->findOrFail($request->service_id);

        $startTime = Carbon::parse($request->date . ' ' . $request->time);
        $endTime = $startTime->copy()->addMinutes($service->duration_minutes);

        $employee = null;
        if ($request->employee_id) {
            $employee = $business->employees()->find($request->employee_id);

            if (!$employee) {
                return back()->with('error', 'Сотрудник не найден')->withInput();
            }

            $isAvailable = $this->appointmentService->isSlotAvailable(
                $employee->id,
                $startTime,
                $endTime
            );

            if (!$isAvailable) {
                return back()->with('error', 'Выбранное время занято')->withInput();
            }
        }

        // ============================================
        // ЛОГИКА РАБОТЫ С КЛИЕНТОМ
        // ============================================

        $clientId = null;
        $clientPhone = null;
        $clientName = null;

        if ($request->client_id) {
            // 1. Если выбран существующий клиент из базы
            $client = $business->clients()->find($request->client_id);
            if ($client) {
                $clientId = $client->id;
            }
        } elseif ($request->client_phone) {
            // 2. Если введен телефон - ищем клиента в базе или сохраняем телефон для привязки
            $clientPhone = $request->client_phone;
            $clientName = $request->client_name ?? 'Гость';

            // Проверяем, есть ли уже клиент с таким телефоном
            $existingClient = $business->clients()
                ->where('phone', $clientPhone)
                ->first();

            if ($existingClient) {
                // Нашли клиента - привязываем
                $clientId = $existingClient->id;
                $clientPhone = null; // не храним телефон, т.к. клиент уже в базе
            } else {
                // Клиента нет - сохраняем телефон для будущей привязки
                // Также проверяем, есть ли зарегистрированный пользователь с таким телефоном
                $user = \App\Models\User::where('phone', $clientPhone)->first();

                if ($user) {
                    // Пользователь зарегистрирован, но не является клиентом этого бизнеса
                    // Создаем клиента в этом бизнесе
                    $newClient = $business->clients()->create([
                        'first_name' => $clientName,
                        'phone' => $clientPhone,
                        'email' => $user->email ?? null,
                    ]);
                    $clientId = $newClient->id;
                    $clientPhone = null;
                }
            }
        }

        // Если клиент не найден и не создан - сохраняем телефон для привязки позже
        if (!$clientId && $clientPhone) {
            // Проверяем, есть ли клиент с таким телефоном в этом бизнесе
            $existingClient = $business->clients()
                ->where('phone', $clientPhone)
                ->first();

            if ($existingClient) {
                $clientId = $existingClient->id;
                $clientPhone = null;
            }
        }

        try {
            $appointmentData = [
                'business_id' => $business->id,
                'client_id' => $clientId,
                'employee_id' => $employee?->id,
                'service_id' => $service->id,
                'created_by_user_id' => auth()->id(),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'price' => $service->price,
                'notes' => $request->notes,
                'status' => 'pending',
                'client_phone' => $clientPhone,
                'client_name' => $clientName,
            ];

            $appointment = Appointment::create($appointmentData);

            Log::info('Appointment created successfully', ['id' => $appointment->id]);

            $message = 'Запись создана!';
            if ($clientPhone && !$clientId) {
                $message .= ' Клиент не зарегистрирован на платформе. При регистрации записи автоматически привяжутся.';
            }

            return redirect()->route('appointments.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Appointment creation failed: ' . $e->getMessage());
            return back()->with('error', 'Ошибка создания записи: ' . $e->getMessage());
        }
    }

    public function show(Request $request, Appointment $appointment)
    {
        $business = $request->user()->currentBusiness;

        if ($appointment->business_id !== $business->id) {
            abort(404);
        }

        return view('appointments.show', compact('appointment'));
    }

    public function edit(Request $request, Appointment $appointment)
    {
        $business = $request->user()->currentBusiness;

        if ($appointment->business_id !== $business->id) {
            abort(404);
        }

        $clients = $business->clients()->orderBy('first_name')->get();
        $employees = $business->employees()->active()->orderBy('name')->get();
        $services = $business->services()->active()->orderBy('name')->get();

        return view('appointments.edit', compact('appointment', 'clients', 'employees', 'services'));
    }

    public function confirm(Request $request, Appointment $appointment)
    {
        $business = $this->getBusiness($request);

        if ($appointment->business_id !== $business->id) {
            abort(404);
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
     * Отмена записи
     */
    public function cancel(Request $request, Appointment $appointment)
    {
        $business = $this->getBusiness($request);

        if ($appointment->business_id !== $business->id) {
            abort(404);
        }

        if (!$appointment->canBeCancelled()) {
            return back()->with('error', 'Эту запись нельзя отменить');
        }

        $appointment->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->reason ?? 'Отменено администратором',
        ]);

        return back()->with('success', 'Запись отменена');
    }

    /**
     * Завершение записи
     */
    public function complete(Request $request, Appointment $appointment)
    {
        $business = $this->getBusiness($request);

        if ($appointment->business_id !== $business->id) {
            abort(404);
        }

        if (!$appointment->canBeCompleted()) {
            return back()->with('error', 'Эту запись нельзя завершить');
        }

        $appointment->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Обновляем статистику клиента
        $client = $appointment->client;
        if ($client) {
            $client->increment('total_visits');
            $client->increment('total_spent', $appointment->price);
            $client->update(['last_visit_at' => now()]);
        }

        return back()->with('success', 'Запись завершена');
    }

    /**
     * Перенос записи
     */
    public function reschedule(Request $request, Appointment $appointment)
    {
        $business = $this->getBusiness($request);

        if ($appointment->business_id !== $business->id) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $startTime = Carbon::parse($request->date . ' ' . $request->time);
        $endTime = $startTime->copy()->addMinutes($appointment->service->duration_minutes);

        // Проверка доступности
        if ($request->employee_id) {
            $isAvailable = $this->appointmentService->isSlotAvailable(
                $request->employee_id,
                $startTime,
                $endTime,
                $appointment->id
            );

            if (!$isAvailable) {
                return back()->with('error', 'Выбранное время занято')->withInput();
            }
        }

        $appointment->update([
            'start_time' => $startTime,
            'end_time' => $endTime,
            'employee_id' => $request->employee_id ?? $appointment->employee_id,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Запись перенесена');
    }

    /**
     * Отправить напоминание
     */
    public function sendReminder(Request $request, Appointment $appointment)
    {
        $business = $this->getBusiness($request);

        if ($appointment->business_id !== $business->id) {
            abort(404);
        }

        // Отправка напоминания через сервис
        $this->appointmentService->sendReminder($appointment);

        return back()->with('success', 'Напоминание отправлено');
    }

    /**
     * Получение доступных слотов (AJAX)
     */
    public function getAvailableSlots(Request $request)
    {
        $business = $this->getBusiness($request);

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date|after_or_equal:today',
            'service_id' => 'required|exists:services,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $slots = $this->appointmentService->getAvailableSlots(
            $request->employee_id,
            $request->date,
            Service::find($request->service_id)->duration_minutes
        );

        return response()->json([
            'status' => 'success',
            'data' => [
                'available_slots' => $slots
            ]
        ]);
    }

    public function calendar(Request $request)
    {
        $business = $request->user()->currentBusiness;
        $employees = $business->employees()->active()->orderBy('name')->get();

        return view('appointments.calendar', compact('employees'));
    }

    public function calendarData(Request $request)
    {
        try {
            $business = $request->user()->currentBusiness;

            if (!$business) {
                return response()->json([]);
            }

            $start = $request->start ? Carbon::parse($request->start) : now()->startOfMonth();
            $end = $request->end ? Carbon::parse($request->end) : now()->endOfMonth();

            $appointments = $business->appointments()
                ->with(['client', 'service', 'employee'])
                ->whereBetween('start_time', [$start, $end])
                ->where('status', '!=', 'cancelled')
                ->get();

            $colors = [
                'pending' => '#F59E0B',
                'confirmed' => '#10B981',
                'in_progress' => '#3B82F6',
                'completed' => '#8B5CF6',
                'cancelled' => '#EF4444',
                'no_show' => '#EF4444',
            ];

            $statusLabels = [
                'pending' => 'В ожидании',
                'confirmed' => 'Подтверждена',
                'in_progress' => 'В процессе',
                'completed' => 'Завершена',
                'cancelled' => 'Отменена',
                'no_show' => 'Не явился',
            ];

            $result = $appointments->map(function ($appointment) use ($colors, $statusLabels) {
                return [
                    'id' => $appointment->id,
                    'title' => $appointment->client->first_name . ' - ' . $appointment->service->name,
                    'start' => $appointment->start_time->toISOString(),
                    'end' => $appointment->end_time->toISOString(),
                    'backgroundColor' => $colors[$appointment->status] ?? '#6B7280',
                    'borderColor' => $colors[$appointment->status] ?? '#6B7280',
                    'textColor' => '#FFFFFF',
                    'url' => route('appointments.show', $appointment->id),
                    'extendedProps' => [
                        'status' => $statusLabels[$appointment->status] ?? $appointment->status,
                        'client' => $appointment->client->full_name ?? $appointment->client->first_name,
                        'service' => $appointment->service->name,
                        'employee' => $appointment->employee->name,
                        'price' => number_format($appointment->price, 0, '.', ' ') . ' ₽',
                    ]
                ];
            });

            return response()->json($result);

        } catch (\Exception $e) {
            \Log::error('Calendar data error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
