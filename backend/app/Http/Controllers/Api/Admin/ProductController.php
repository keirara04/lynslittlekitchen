<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Product::query()->with(['category', 'images', 'variants']);

        if ($search = $request->string('search')->trim()->value()) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%'.mb_strtolower($search).'%']);
        }

        if ($category = $request->string('category')->trim()->value()) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $category));
        }

        if ($status = $request->string('status')->value()) {
            $query->where('status', $status);
        }

        $query->latest();

        return ProductResource::collection(
            $query->paginate($request->integer('per_page', 20))
        );
    }

    public function show(Product $product): ProductResource
    {
        return new ProductResource($product->load(['category', 'images', 'variants']));
    }

    public function store(StoreProductRequest $request): ProductResource
    {
        $data = $request->validated();
        $images = $data['images'] ?? [];
        $variants = $data['variants'] ?? [];
        unset($data['images'], $data['variants']);

        $data['slug'] = $this->uniqueSlug($data['name']);

        $product = Product::create($data);

        $this->syncImages($product, $images);
        $this->syncVariants($product, $variants);

        return new ProductResource($product->load(['category', 'images', 'variants']));
    }

    public function update(UpdateProductRequest $request, Product $product): ProductResource
    {
        $data = $request->validated();
        $images = $data['images'] ?? null;
        $variants = $data['variants'] ?? null;
        unset($data['images'], $data['variants']);

        if (isset($data['name']) && $data['name'] !== $product->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $product->id);
        }

        $product->update($data);

        if ($images !== null) {
            $product->images()->delete();
            $this->syncImages($product, $images);
        }

        if ($variants !== null) {
            $product->variants()->delete();
            $this->syncVariants($product, $variants);
        }

        return new ProductResource($product->load(['category', 'images', 'variants']));
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json(['message' => 'Product deleted.']);
    }

    /**
     * @param  array<int, string>  $urls
     */
    private function syncImages(Product $product, array $urls): void
    {
        foreach ($urls as $index => $url) {
            $product->images()->create(['url' => $url, 'sort_order' => $index]);
        }
    }

    /**
     * @param  array<int, array{label: string, price: float, stock: int}>  $variants
     */
    private function syncVariants(Product $product, array $variants): void
    {
        foreach ($variants as $index => $variant) {
            $product->variants()->create([
                'label' => $variant['label'],
                'price' => $variant['price'],
                'stock' => $variant['stock'],
                'sort_order' => $index,
            ]);
        }
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $i = 1;

        while (
            Product::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereNot('id', $ignoreId))->exists()
        ) {
            $slug = "{$original}-{$i}";
            $i++;
        }

        return $slug;
    }
}
