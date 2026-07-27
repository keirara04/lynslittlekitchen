<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StoreSetting;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    public function show(): JsonResponse
    {
        $settings = StoreSetting::current();

        return response()->json([
            'store_name' => $settings->store_name,
            'address_line1' => $settings->address_line1,
            'address_line2' => $settings->address_line2,
            'postcode' => $settings->postcode,
            'city' => $settings->city,
            'state' => $settings->state,
            'contact_phone' => $settings->contact_phone,
            'contact_email' => $settings->contact_email,
            'operating_hours' => $settings->operating_hours,
            'min_order_amount' => $settings->min_order_amount === null ? null : (float) $settings->min_order_amount,
            'allow_pickup' => $settings->allow_pickup,
            'allow_delivery' => $settings->allow_delivery,
            'featured_hero_product' => $this->summarize($settings->heroProduct),
            'featured_banner_product' => $this->summarize($settings->bannerProduct),
        ]);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function summarize(?Product $product): ?array
    {
        if (! $product) {
            return null;
        }

        return [
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'image_url' => $product->images->first()?->url,
        ];
    }
}
