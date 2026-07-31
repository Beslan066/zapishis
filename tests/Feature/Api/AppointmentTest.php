<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Business;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $business;
    protected $client;
    protected $employee;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->business = Business::factory()->create(['user_id' => $this->user->id]);
        $this->user->update(['current_business_id' => $this->business->id]);

        $this->client = Client::factory()->create(['business_id' => $this->business->id]);
        $this->employee = Employee::factory()->create(['business_id' => $this->business->id]);
        $this->service = Service::factory()->create([
            'business_id' => $this->business->id,
            'duration_minutes' => 60,
            'price' => 1000,
        ]);
    }

    public function test_user_can_create_appointment()
    {
        $startTime = Carbon::tomorrow()->setHour(10)->setMinute(0);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/appointments', [
                'client_id' => $this->client->id,
                'employee_id' => $this->employee->id,
                'service_id' => $this->service->id,
                'start_time' => $startTime->toISOString(),
                'notes' => 'Test appointment',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'id',
                    'client',
                    'employee',
                    'service',
                    'start_time',
                    'end_time',
                    'status',
                ],
            ]);

        $this->assertDatabaseHas('appointments', [
            'client_id' => $this->client->id,
            'employee_id' => $this->employee->id,
            'service_id' => $this->service->id,
            'status' => 'pending',
        ]);
    }

    public function test_user_can_get_available_slots()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/appointments/available', [
                'employee_id' => $this->employee->id,
                'date' => Carbon::tomorrow()->toDateString(),
                'service_id' => $this->service->id,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'date',
                    'employee',
                    'service',
                    'duration_minutes',
                    'available_slots',
                ],
            ]);
    }

    public function test_user_can_confirm_appointment()
    {
        $appointment = Appointment::factory()->create([
            'business_id' => $this->business->id,
            'client_id' => $this->client->id,
            'employee_id' => $this->employee->id,
            'service_id' => $this->service->id,
            'start_time' => Carbon::tomorrow()->setHour(10),
            'end_time' => Carbon::tomorrow()->setHour(11),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/v1/appointments/{$appointment->id}/confirm");

        $response->assertStatus(200);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_user_can_complete_appointment()
    {
        $appointment = Appointment::factory()->create([
            'business_id' => $this->business->id,
            'client_id' => $this->client->id,
            'employee_id' => $this->employee->id,
            'service_id' => $this->service->id,
            'start_time' => Carbon::yesterday()->setHour(10),
            'end_time' => Carbon::yesterday()->setHour(11),
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/v1/appointments/{$appointment->id}/complete");

        $response->assertStatus(200);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'completed',
        ]);

        // Check client stats updated
        $this->client->refresh();
        $this->assertEquals(1, $this->client->total_visits);
        $this->assertEquals(1000, $this->client->total_spent);
    }

    public function test_user_can_cancel_appointment()
    {
        $appointment = Appointment::factory()->create([
            'business_id' => $this->business->id,
            'client_id' => $this->client->id,
            'employee_id' => $this->employee->id,
            'service_id' => $this->service->id,
            'start_time' => Carbon::tomorrow()->setHour(10),
            'end_time' => Carbon::tomorrow()->setHour(11),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/v1/appointments/{$appointment->id}/cancel", [
                'cancellation_reason' => 'Client requested cancellation',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'cancelled',
            'cancellation_reason' => 'Client requested cancellation',
        ]);
    }
}
