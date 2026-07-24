<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeliveryZoneResource;
use App\Models\DeliveryZone;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DeliveryZoneController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return DeliveryZoneResource::collection(DeliveryZone::orderBy('name')->get());
    }
}
