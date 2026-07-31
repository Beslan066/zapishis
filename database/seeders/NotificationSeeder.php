<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $businesses = Business::all();

        if ($businesses->isEmpty()) {
            return;
        }

        $users = User::all();

        foreach ($businesses as $business) {
            // Create system notifications
            Notification::factory()
                ->count(5)
                ->state([
                    'business_id' => $business->id,
                    'channel' => 'system',
                    'status' => 'read',
                ])
                ->create();

            // Create pending notifications
            Notification::factory()
                ->count(3)
                ->pending()
                ->state([
                    'business_id' => $business->id,
                    'channel' => 'email',
                ])
                ->create();

            // Create sent notifications
            Notification::factory()
                ->count(4)
                ->sent()
                ->state([
                    'business_id' => $business->id,
                    'channel' => 'sms',
                ])
                ->create();

            // Create failed notifications
            Notification::factory()
                ->count(2)
                ->failed()
                ->state([
                    'business_id' => $business->id,
                    'channel' => 'sms',
                ])
                ->create();

            // Create urgent notifications
            Notification::factory()
                ->count(1)
                ->state([
                    'business_id' => $business->id,
                    'is_urgent' => true,
                    'requires_action' => true,
                    'title' => 'Срочное уведомление',
                    'message' => 'Требуется ваше внимание: клиент ожидает подтверждения записи.',
                ])
                ->create();

            // Create birthday greetings
            Notification::factory()
                ->count(2)
                ->state([
                    'business_id' => $business->id,
                    'type' => 'birthday_greeting',
                    'title' => 'День рождения!',
                    'message' => 'Поздравляем с днем рождения! Ждем вас с праздничной скидкой 20%.',
                ])
                ->create();

            // Create notifications for users
            if ($users->isNotEmpty()) {
                foreach ($users->take(3) as $user) {
                    Notification::factory()
                        ->forUser($user)
                        ->state([
                            'business_id' => $business->id,
                        ])
                        ->create();
                }
            }
        }
    }
}
