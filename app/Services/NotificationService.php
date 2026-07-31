<?php

namespace App\Services;

use App\Models\User;
use App\Models\Appointment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Отправить SMS код верификации
     */
    public function sendVerificationCode(string $phone, string $code): bool
    {
        $message = "Ваш код подтверждения: {$code}\n\nНе сообщайте его никому!";

        // Отправляем SMS
        $result = $this->smsService->send($phone, $message);

        // Дублируем в лог для отладки
        Log::info("Verification code for {$phone}: {$code}");

        return $result;
    }

    /**
     * Отправить уведомление о записи
     */
    public function sendAppointmentConfirmation(Appointment $appointment): void
    {
        $client = $appointment->client;
        $message = "✅ Запись подтверждена!\n\n" .
            "Услуга: {$appointment->service->name}\n" .
            "Дата: {$appointment->start_time->format('d.m.Y')}\n" .
            "Время: {$appointment->start_time->format('H:i')}\n" .
            "Мастер: {$appointment->employee->name}\n" .
            "Адрес: {$appointment->business->address}";

        // SMS
        if ($client->phone) {
            $this->smsService->send($client->phone, $message);
        }

        // Email
        if ($client->email) {
            $this->sendEmail($client->email, 'Подтверждение записи', 'emails.appointment-confirmation', [
                'client' => $client,
                'appointment' => $appointment
            ]);
        }
    }

    /**
     * Отправить Email
     */
    public function sendEmail(string $to, string $subject, string $view, array $data = []): void
    {
        try {
            Mail::send($view, $data, function ($message) use ($to, $subject) {
                $message->to($to)
                    ->subject($subject);
            });
        } catch (\Exception $e) {
            Log::error("Email error: " . $e->getMessage());
        }
    }
}
