<?php


namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'discount_price' => (float) $this->discount_price,
            'final_price' => (float) $this->getFinalPrice(),
            'duration_minutes' => $this->duration_minutes,
            'duration_label' => $this->duration_minutes . ' мин',
            'buffer_before_minutes' => $this->buffer_before_minutes,
            'buffer_after_minutes' => $this->buffer_after_minutes,
            'color' => $this->color,
            'icon' => $this->icon,
            'is_active' => $this->is_active,
            'metadata' => $this->metadata,
            'employees' => EmployeeResource::collection($this->whenLoaded('employees')),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
