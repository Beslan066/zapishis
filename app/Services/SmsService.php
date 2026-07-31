<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $apiKey;
    protected $sender;

    public function __construct()
    {
        $this->apiKey = env('SMSRU_API_KEY');
        $this->sender = env('SMSRU_SENDER', 'ZAPISHIS');
    }

    public function send(string $phone, string $message): bool
    {
        try {
            $phone = $this->formatPhone($phone);

            // Логируем параметры запроса
            Log::info('SMS Request params:', [
                'phone' => $phone,
                'message' => $message,
                'sender' => $this->sender,
                'api_key_exists' => !empty($this->apiKey)
            ]);

            // Проверяем наличие API ключа
            if (empty($this->apiKey)) {
                Log::error('SMS API key is empty');
                return false;
            }

            $response = Http::timeout(30)->post('https://sms.ru/sms/send', [
                'api_id' => $this->apiKey,
                'to' => $phone,
                'msg' => $message,
                'from' => $this->sender,
                'json' => 1,
            ]);

            $data = $response->json();

            // Логируем полный ответ
            Log::info('SMS Response:', [
                'status' => $response->status(),
                'body' => $data
            ]);

            // Проверяем ответ
            if (!$data) {
                Log::error('SMS response is empty');
                return false;
            }

            // Ошибки SMS.ru
            if (isset($data['status_code']) && $data['status_code'] != 100) {
                $errorMessages = [
                    101 => 'Неверный API ключ',
                    102 => 'Недостаточно средств',
                    103 => 'Неверный номер телефона',
                    104 => 'Неверный отправитель',
                    105 => 'Превышен лимит сообщений',
                    106 => 'Сообщение заблокировано',
                    107 => 'Неверный формат сообщения',
                    108 => 'Превышен лимит по времени',
                ];
                $error = $errorMessages[$data['status_code']] ?? 'Неизвестная ошибка';
                Log::error("SMS error [{$data['status_code']}]: {$error}");
                return false;
            }

            if (isset($data['status']) && $data['status'] === 'OK') {
                Log::info("SMS sent successfully to {$phone}");
                return true;
            }

            Log::error("SMS failed: " . json_encode($data));
            return false;

        } catch (\Exception $e) {
            Log::error("SMS exception: " . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }

    protected function formatPhone(string $phone): string
    {
        $original = $phone;
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Для теста - если номер начинается с 8 или 7, приводим к формату 7...
        if (strlen($phone) === 11 && ($phone[0] === '8' || $phone[0] === '7')) {
            $phone = '7' . substr($phone, 1);
        } elseif (strlen($phone) === 10) {
            $phone = '7' . $phone;
        } elseif (strlen($phone) === 12 && $phone[0] === '7') {
            // Номер уже в правильном формате
        }

        Log::info("Phone formatted: {$original} -> {$phone}");
        return $phone;
    }
}
