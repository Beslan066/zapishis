<?php

namespace App\Services;

use App\Models\Appointment;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected $whatsappService;
    protected $smsService;

    public function __construct(
        WhatsAppService $whatsappService,
        SmsService $smsService
    ) {
        $this->whatsappService = $whatsappService;
        $this->smsService = $smsService;
    }

    /**
     * Отправить код верификации (WhatsApp + SMS)
     */
    public function sendVerificationCode(string $phone, string $code): bool
    {
        $message = "🔐 Ваш код подтверждения: {$code}\n\nНе сообщайте его никому!";

        Log::info("Verification code for {$phone}: {$code}");

        // Отправляем в WhatsApp (основной канал)
        $whatsappResult = $this->whatsappService->send($phone, $message);

        // Отправляем SMS (запасной канал)
        $smsResult = $this->smsService->send($phone, $message);

        Log::info("Verification code results - WhatsApp: " . ($whatsappResult ? '✅' : '❌') . ", SMS: " . ($smsResult ? '✅' : '❌'));

        return $whatsappResult || $smsResult;
    }

    /**
     * Уведомление о создании записи
     */
    public function sendAppointmentCreated(Appointment $appointment): bool
    {
        $phone = $appointment->client_phone ?? $appointment->client?->phone;

        if (!$phone) {
            Log::warning('No phone for appointment notification', ['appointment_id' => $appointment->id]);
            return false;
        }

        $message = "✅ Новая запись!\n\n" .
            "Услуга: {$appointment->service->name}\n" .
            "Дата: {$appointment->start_time->format('d.m.Y')}\n" .
            "Время: {$appointment->start_time->format('H:i')}\n" .
            "Стоимость: " . number_format($appointment->price, 0) . " ₽\n\n" .
            "Номер заявки: #{$appointment->id}";

        $whatsappResult = $this->whatsappService->send($phone, $message);
        $this->smsService->send($phone, $message);

        Log::info("Appointment created notification sent for #{$appointment->id}");

        return $whatsappResult;
    }

    /**
     * Напоминание о записи
     */
    public function sendAppointmentReminder(Appointment $appointment): bool
    {
        $phone = $appointment->client_phone ?? $appointment->client?->phone;

        if (!$phone) {
            Log::warning('No phone for reminder', ['appointment_id' => $appointment->id]);
            return false;
        }

        $message = "🔔 Напоминание о записи!\n\n" .
            "Услуга: {$appointment->service->name}\n" .
            "Дата: {$appointment->start_time->format('d.m.Y')}\n" .
            "Время: {$appointment->start_time->format('H:i')}\n" .
            "Мастер: {$appointment->employee->name ?? 'Не указан'}";

        $whatsappResult = $this->whatsappService->send($phone, $message);
        $this->smsService->send($phone, $message);

        return $whatsappResult;
    }

    /**
     * Уведомление об отмене записи
     */
    public function sendAppointmentCancelled(Appointment $appointment): bool
    {
        $phone = $appointment->client_phone ?? $appointment->client?->phone;

        if (!$phone) {
            Log::warning('No phone for cancellation', ['appointment_id' => $appointment->id]);
            return false;
        }

        $message = "❌ Запись отменена!\n\n" .
            "Услуга: {$appointment->service->name}\n" .
            "Дата: {$appointment->start_time->format('d.m.Y')}\n" .
            "Время: {$appointment->start_time->format('H:i')}";

        $whatsappResult = $this->whatsappService->send($phone, $message);
        $this->smsService->send($phone, $message);

        return $whatsappResult;
    }

    /**
     * Уведомление о подтверждении записи
     */
    public function sendAppointmentConfirmed(Appointment $appointment): bool
    {
        $phone = $appointment->client_phone ?? $appointment->client?->phone;

        if (!$phone) {
            Log::warning('No phone for confirmation', ['appointment_id' => $appointment->id]);
            return false;
        }

        $message = "✅ Запись подтверждена!\n\n" .
            "Услуга: {$appointment->service->name}\n" .
            "Дата: {$appointment->start_time->format('d.m.Y')}\n" .
            "Время: {$appointment->start_time->format('H:i')}\n" .
            "Стоимость: " . number_format($appointment->price, 0) . " ₽";

        $whatsappResult = $this->whatsappService->send($phone, $message);
        $this->smsService->send($phone, $message);

        return $whatsappResult;
    }
}
