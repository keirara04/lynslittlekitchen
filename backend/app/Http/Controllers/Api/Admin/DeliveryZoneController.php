<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeliveryZone\StoreDeliveryZoneRequest;
use App\Http\Requests\DeliveryZone\UpdateDeliveryZoneRequest;
use App\Http\Resources\DeliveryZoneResource;
use App\Models\DeliveryZone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DeliveryZoneController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return DeliveryZoneResource::collection(DeliveryZone::orderBy('name')->get());
    }

    public function store(StoreDeliveryZoneRequest $request): DeliveryZoneResource
    {
        $zone = DeliveryZone::create($request->validated());

        return new DeliveryZoneResource($zone);
    }

    public function update(UpdateDeliveryZoneRequest $request, DeliveryZone $deliveryZone): DeliveryZoneResource
    {
        $deliveryZone->update($request->validated());

        return new DeliveryZoneResource($deliveryZone);
    }

    public function destroy(DeliveryZone $deliveryZone): JsonResponse
    {
        $deliveryZone->delete();

        return response()->json(['message' => 'Delivery zone deleted.']);
    }
}
