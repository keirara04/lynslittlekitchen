<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\HasLocalizedFields;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Category */
class CategoryResource extends JsonResource
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
            'slug' => $this->slug,
        ];
    }
}
