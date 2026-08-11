<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $instance;
    protected $token;
    protected $senderPhone;

    public function __construct()
    {
        $this->instance = env('GREEN_API_INSTANCE');
        $this->token = env('GREEN_API_TOKEN');
        $this->senderPhone = env('GREEN_API_PHONE');
    }

    public function send(string $phone, string $message): bool
    {
        try {
            $phone = $this->formatPhone($phone);
            $chatId = $phone . '@c.us';

            $url = "https://api.green-api.com/waInstance{$this->instance}/sendMessage/{$this->token}";

            Log::info('WhatsApp send attempt:', [
                'url' => $url,
                'chatId' => $chatId,
            ]);

            $response = Http::timeout(30)->post($url, [
                'chatId' => $chatId,
                'message' => $message,
            ]);

            $data = $response->json();

            if ($response->successful() && isset($data['idMessage'])) {
                Log::info("WhatsApp message sent to {$phone}");
                return true;
            }

            Log::error("WhatsApp failed: " . json_encode($data));
            return false;

        } catch (\Exception $e) {
            Log::error("WhatsApp exception: " . $e->getMessage());
            return false;
        }
    }

    public function sendTemplate(string $phone, string $templateName, array $params = []): bool
    {
        try {
            $phone = $this->formatPhone($phone);
            $message = $this->buildTemplateMessage($templateName, $params);

            $url = "https://api.green-api.com/waInstance{$this->instance}/sendMessage/{$this->token}";

            $response = Http::post($url, [
                'chatId' => $phone . '@c.us',
                'message' => $message,
            ]);

            $data = $response->json();

            if ($response->successful() && isset($data['idMessage'])) {
                Log::info("WhatsApp template sent: {$templateName}");
                return true;
            }

            Log::error("WhatsApp template failed: " . json_encode($data));
            return false;

        } catch (\Exception $e) {
            Log::error("WhatsApp template error: " . $e->getMessage());
            return false;
        }
    }

    public function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) === 11 && $phone[0] === '8') {
            $phone = '7' . substr($phone, 1);
        }

        if (strlen($phone) === 10) {
            $phone = '7' . $phone;
        }

        return $phone;
    }

    protected function buildTemplateMessage(string $templateName, array $params): string
    {
        $templates = [
            'verification' => "🔐 Ваш код подтверждения: {code}\n\nНе сообщайте его никому!",
            'appointment_created' => "✅ Новая запись!\n\nУслуга: {service}\nДата: {date}\nВремя: {time}\nСтоимость: {price}\n\nНомер заявки: #{id}",
            'appointment_reminder' => "🔔 Напоминание о записи!\n\nУслуга: {service}\nДата: {date}\nВремя: {time}",
            'appointment_cancelled' => "❌ Запись отменена!\n\nУслуга: {service}\nДата: {date}\nВремя: {time}",
            'appointment_confirmed' => "✅ Запись подтверждена!\n\nУслуга: {service}\nДата: {date}\nВремя: {time}\nСтоимость: {price}",
        ];

        $template = $templates[$templateName] ?? $params['message'] ?? '';

        foreach ($params as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }

        return $template;
    }

    public function getState(): array
    {
        try {
            $url = "https://api.green-api.com/waInstance{$this->instance}/getStateInstance/{$this->token}";
            $response = Http::get($url);

            if ($response->successful()) {
                return $response->json();
            }

            return ['error' => 'Failed to get state', 'status' => $response->status()];

        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
