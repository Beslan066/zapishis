<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'client' => new ClientResource($this->whenLoaded('client')),
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'service' => new ServiceResource($this->whenLoaded('service')),
            'start_time' => $this->start_time->toISOString(),
            'end_time' => $this->end_time->toISOString(),
            'start_time_formatted' => $this->start_time->format('d.m.Y H:i'),
            'date' => $this->start_time->format('d.m.Y'),
            'time' => $this->start_time->format('H:i'),
            'price' => (float) $this->price,
            'discount_applied' => (float) $this->discount_applied,
            'deposit_paid' => (float) $this->deposit_paid,
            'remaining_amount' => (float) ($this->price - $this->deposit_paid),
            'status' => $this->status,
            'status_label' => $this->status_label,
            'status_color' => $this->status_color,
            'cancellation_reason' => $this->cancellation_reason,
            'notes' => $this->notes,
            'metadata' => $this->metadata,
            'duration_minutes' => $this->getDurationInMinutes(),
            'is_past' => $this->isPast(),
            'is_future' => $this->isFuture(),
            'is_in_progress' => $this->isInProgress(),
            'can_be_cancelled' => $this->canBeCancelled(),
            'can_be_confirmed' => $this->canBeConfirmed(),
            'can_be_completed' => $this->canBeCompleted(),
            'confirmed_at' => $this->confirmed_at?->toISOString(),
            'completed_at' => $this->completed_at?->toISOString(),
            'cancelled_at' => $this->cancelled_at?->toISOString(),
            'reminder_sent_at' => $this->reminder_sent_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
