<?php


namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'position' => $this->position,
            'avatar_url' => $this->avatar_url,
            'commission_percent' => (float) $this->commission_percent,
            'is_active' => $this->is_active,
            'settings' => $this->settings,
            'booking_buffer_minutes' => $this->booking_buffer_minutes,
            'working_hours' => WorkingHourResource::collection($this->whenLoaded('workingHours')),
            'services' => ServiceResource::collection($this->whenLoaded('services')),
            'appointments_count' => $this->appointments()->count(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
