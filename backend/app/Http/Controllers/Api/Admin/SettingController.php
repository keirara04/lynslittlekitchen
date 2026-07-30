<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\UpdateSettingRequest;
use App\Models\StoreSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json(['data' => StoreSetting::current()]);
    }

    public function update(UpdateSettingRequest $request): JsonResponse
    {
        $settings = StoreSetting::current();
        $settings->update($request->validated());

        Cache::forget('store-settings.index');

        return response()->json(['data' => $settings]);
    }
}
