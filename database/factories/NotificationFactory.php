<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\Business;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        $types = [
            'appointment_confirmation',
            'appointment_reminder',
            'appointment_cancellation',
            'appointment_reschedule',
            'birthday_greeting',
            'promotion',
            'system',
            'payment_confirmation',
            'feedback_request',
        ];

        $channels = ['system', 'sms', 'email', 'telegram', 'push'];
        $statuses = ['pending', 'sent', 'delivered', 'read', 'failed', 'cancelled'];

        return [
            'business_id' => Business::factory(),
            'user_id' => null,
            'appointment_id' => null,
            'client_id' => null,
            'type' => $this->faker->randomElement($types),
            'channel' => $this->faker->randomElement($channels),
            'title' => $this->faker->sentence(3),
            'message' => $this->faker->text(200),
            'data' => null,
            'status' => $this->faker->randomElement($statuses),
            'sent_at' => $this->faker->optional()->dateTimeThisMonth(),
            'delivered_at' => null,
            'read_at' => null,
            'failed_at' => null,
            'recipient' => $this->faker->phoneNumber,
            'provider' => null,
            'provider_message_id' => null,
            'provider_response' => null,
            'error_message' => null,
            'retry_count' => 0,
            'next_retry_at' => null,
            'priority' => $this->faker->numberBetween(0, 2),
            'is_urgent' => $this->faker->boolean(20),
            'requires_action' => $this->faker->boolean(10),
            'metadata' => null,
        ];
    }

    public function pending(): self
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    public function sent(): self
    {
        return $this->state(fn () => [
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function delivered(): self
    {
        return $this->state(fn () => [
            'status' => 'delivered',
            'sent_at' => now()->subMinutes(5),
            'delivered_at' => now(),
        ]);
    }

    public function read(): self
    {
        return $this->state(fn () => [
            'status' => 'read',
            'sent_at' => now()->subMinutes(10),
            'delivered_at' => now()->subMinutes(5),
            'read_at' => now(),
        ]);
    }

    public function failed(): self
    {
        return $this->state(fn () => [
            'status' => 'failed',
            'failed_at' => now(),
            'error_message' => $this->faker->sentence(),
            'retry_count' => $this->faker->numberBetween(1, 3),
        ]);
    }

    public function forAppointment(Appointment $appointment): self
    {
        return $this->state(fn () => [
            'business_id' => $appointment->business_id,
            'appointment_id' => $appointment->id,
            'client_id' => $appointment->client_id,
            'recipient' => $appointment->client->phone,
        ]);
    }

    public function forUser(User $user): self
    {
        return $this->state(fn () => [
            'business_id' => $user->current_business_id,
            'user_id' => $user->id,
            'recipient' => $user->phone,
        ]);
    }
}
