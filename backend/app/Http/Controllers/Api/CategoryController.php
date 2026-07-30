<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $rows = Cache::remember('categories.public.index', now()->addHours(6), function () {
            return Category::orderBy('name')->get()->toArray();
        });

        return CategoryResource::collection(Category::hydrate($rows));
    }
}
