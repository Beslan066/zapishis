<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class BusinessResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'city' => $this->city,
            'region' => $this->region,
            'description' => $this->description,
            'logo_url' => $this->logo_url,
            'timezone' => $this->timezone,
            'settings' => $this->settings,
            'status' => $this->status,
            'trial_ends_at' => $this->trial_ends_at?->toISOString(),
            'is_on_trial' => $this->isOnTrial(),
            'is_active' => $this->isActive(),
            'stats' => [
                'services_count' => $this->services_count ?? $this->services()->count(),
                'employees_count' => $this->employees_count ?? $this->employees()->count(),
                'clients_count' => $this->clients_count ?? $this->clients()->count(),
                'appointments_count' => $this->appointments_count ?? $this->appointments()->count(),
            ],
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
