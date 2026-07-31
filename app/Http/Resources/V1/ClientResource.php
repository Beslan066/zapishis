<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'initials' => $this->initials,
            'phone' => $this->phone,
            'email' => $this->email,
            'birthday' => $this->birthday?->toISOString(),
            'instagram' => $this->instagram,
            'notes' => $this->notes,
            'metadata' => $this->metadata,
            'total_visits' => $this->total_visits,
            'total_spent' => (float) $this->total_spent,
            'last_visit_at' => $this->last_visit_at?->toISOString(),
            'appointments_count' => $this->appointments_count ?? $this->appointments()->count(),
            'recent_appointments' => AppointmentResource::collection(
                $this->whenLoaded('appointments')
            ),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
