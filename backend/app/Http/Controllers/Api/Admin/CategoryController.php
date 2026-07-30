<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $rows = Cache::remember('categories.admin.index', now()->addHours(6), function () {
            return Category::orderBy('name')->get()->toArray();
        });

        return CategoryResource::collection(Category::hydrate($rows));
    }

    public function store(StoreCategoryRequest $request): CategoryResource
    {
        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $data['name']);

        $category = Category::create($data);

        $this->flushCategoryCache();

        return new CategoryResource($category);
    }

    public function update(UpdateCategoryRequest $request, Category $category): CategoryResource
    {
        $data = $request->validated();

        if (array_key_exists('slug', $data) && $data['slug'] !== null) {
            $data['slug'] = $this->uniqueSlug($data['slug'], $category->id);
        } elseif (isset($data['name']) && $data['name'] !== $category->name && ! array_key_exists('slug', $data)) {
            $data['slug'] = $this->uniqueSlug($data['name'], $category->id);
        }

        $category->update($data);

        $this->flushCategoryCache();

        return new CategoryResource($category);
    }

    public function destroy(Category $category): JsonResponse
    {
        if ($category->products()->exists()) {
            return response()->json([
                'message' => 'Cannot delete a category that still has products.',
            ], 422);
        }

        $category->delete();

        $this->flushCategoryCache();

        return response()->json(['message' => 'Category deleted.']);
    }

    private function flushCategoryCache(): void
    {
        Cache::forget('categories.public.index');
        Cache::forget('categories.admin.index');
    }

    private function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $slug = Str::slug($value);
        $original = $slug;
        $i = 1;

        while (
            Category::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereNot('id', $ignoreId))->exists()
        ) {
            $slug = "{$original}-{$i}";
            $i++;
        }

        return $slug;
    }
}
