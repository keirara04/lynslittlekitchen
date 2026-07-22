<?php
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/health', function (): JsonResponse {
    return response()->json([
        'status' => 'ok',
        'message' => 'Lyns Little Kitchen API is running',
    ]);
});