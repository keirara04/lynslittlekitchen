<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

/** @mixin \App\Models\Order */
final class AdminOrderResource extends OrderResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'customer' => [
                'type' => $this->user_id ? 'registered' : 'guest',
                'id' => $this->user_id,
                'name' => $this->user?->name ?? $this->guest_name,
                'phone' => $this->guest_phone,
                'email' => $this->user?->email ?? $this->guest_email,
            ],
        ]);
    }
}
