<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\HasLocalizedFields;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin DeliveryZone */
class DeliveryZoneResource extends JsonResource
{
    use HasLocalizedFields;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->localized($request, 'name'),
            'name_ms' => $this->name_ms,
            'price' => (float) $this->price,
        ];
    }
}
