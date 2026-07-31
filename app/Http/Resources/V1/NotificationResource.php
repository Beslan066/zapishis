<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'type_label' => $this->type_label,
            'channel' => $this->channel,
            'channel_label' => $this->channel_label,
            'title' => $this->title,
            'message' => $this->message,
            'short_message' => $this->short_message,
            'data' => $this->data,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'is_read' => $this->isRead(),
            'is_urgent' => $this->is_urgent,
            'requires_action' => $this->requires_action,
            'recipient' => $this->recipient,
            'sent_at' => $this->sent_at?->toISOString(),
            'delivered_at' => $this->delivered_at?->toISOString(),
            'read_at' => $this->read_at?->toISOString(),
            'failed_at' => $this->failed_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'time_ago' => $this->time_ago,

            // Related resources
            'business' => new BusinessResource($this->whenLoaded('business')),
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'phone' => $this->user->phone,
                    'email' => $this->user->email,
                ];
            }),
            'client' => new ClientResource($this->whenLoaded('client')),
            'appointment' => new AppointmentResource($this->whenLoaded('appointment')),
        ];
    }
}
