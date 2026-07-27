<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\HasLocalizedFields;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Product */
class ProductResource extends JsonResource
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
            'slug' => $this->slug,
            'description' => $this->localized($request, 'description'),
            'description_ms' => $this->description_ms,
            'ingredients' => $this->localized($request, 'ingredients'),
            'ingredients_ms' => $this->ingredients_ms,
            'allergens' => $this->localized($request, 'allergens'),
            'allergens_ms' => $this->allergens_ms,
            'price' => (float) $this->price,
            'stock' => $this->stock,
            'in_stock' => $this->stock > 0,
            'status' => $this->status->value,
            'is_signature' => $this->is_signature,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
        ];
    }
}
