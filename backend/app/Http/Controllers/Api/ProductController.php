<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Product::query()
            ->with(['category', 'images', 'variants'])
            ->where('status', ProductStatus::Active);

        if ($search = $request->string('search')->trim()->value()) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%'.mb_strtolower($search).'%']);
        }

        if ($category = $request->string('category')->trim()->value()) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $category));
        }

        match ($request->string('sort')->value()) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'name' => $query->orderBy('name'),
            default => $query->latest(),
        };

        return ProductResource::collection(
            $query->paginate($request->integer('per_page', 12))
        );
    }

    public function show(string $slug): ProductResource
    {
        $product = Product::with(['category', 'images', 'variants'])
            ->where('slug', $slug)
            ->where('status', ProductStatus::Active)
            ->firstOrFail();

        return new ProductResource($product);
    }
}
