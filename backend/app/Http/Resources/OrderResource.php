<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Order */
class OrderResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_reference' => $this->order_reference,
            'total' => (float) $this->total,
            'delivery_fee' => (float) $this->delivery_fee,
            'payment_status' => $this->payment_status->value,
            'payment_proof_url' => $this->payment_proof_url,
            'payment_proof_submitted_at' => $this->payment_proof_submitted_at?->toIso8601String(),
            'paid_at' => $this->paid_at?->toIso8601String(),
            'order_status' => $this->order_status->value,
            'allowed_next_statuses' => array_map(
                fn ($status) => $status->value,
                $this->order_status->allowedNextStatuses()
            ),
            'delivery_method' => $this->delivery_method->value,
            'delivery_address' => $this->delivery_address,
            'delivery_date' => $this->delivery_date?->toDateString(),
            'notes' => $this->notes,
            'delivery_zone' => new DeliveryZoneResource($this->whenLoaded('deliveryZone')),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
