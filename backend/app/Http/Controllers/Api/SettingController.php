<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StoreSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $data = Cache::remember('store-settings.index', now()->addHours(6), function () {
            $settings = StoreSetting::query()->with(['heroProduct.images', 'bannerProduct.images'])->first()
                ?? StoreSetting::current();

            return [
                'settings' => $settings->toArray(),
                'hero_product' => $this->summarize($settings->heroProduct),
                'banner_product' => $this->summarize($settings->bannerProduct),
            ];
        });

        $isMalay = $request->header('X-Locale') === 'ms';
        $settings = $data['settings'];

        return response()->json([
            'store_name' => $settings['store_name'],
            'address_line1' => $this->localized($isMalay, $settings['address_line1'], $settings['address_line1_ms']),
            'address_line2' => $this->localized($isMalay, $settings['address_line2'], $settings['address_line2_ms']),
            'postcode' => $settings['postcode'],
            'city' => $this->localized($isMalay, $settings['city'], $settings['city_ms']),
            'state' => $this->localized($isMalay, $settings['state'], $settings['state_ms']),
            'contact_phone' => $settings['contact_phone'],
            'contact_email' => $settings['contact_email'],
            'operating_hours' => $this->localized($isMalay, $settings['operating_hours'], $settings['operating_hours_ms']),
            'min_order_amount' => $settings['min_order_amount'] === null ? null : (float) $settings['min_order_amount'],
            'allow_pickup' => $settings['allow_pickup'],
            'allow_delivery' => $settings['allow_delivery'],
            'featured_hero_product' => $this->localizeProduct($data['hero_product'], $isMalay),
            'featured_banner_product' => $this->localizeProduct($data['banner_product'], $isMalay),
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
            'name_ms' => $product->name_ms,
            'slug' => $product->slug,
            'description' => $product->description,
            'description_ms' => $product->description_ms,
            'image_url' => $product->images->first()?->url,
        ];
    }

    /**
     * @param  array<string, mixed>|null  $product
     * @return array<string, mixed>|null
     */
    private function localizeProduct(?array $product, bool $isMalay): ?array
    {
        if (! $product) {
            return null;
        }

        return [
            'name' => $this->localized($isMalay, $product['name'], $product['name_ms']),
            'slug' => $product['slug'],
            'description' => $this->localized($isMalay, $product['description'], $product['description_ms']),
            'image_url' => $product['image_url'],
        ];
    }

    private function localized(bool $isMalay, ?string $default, ?string $translated): ?string
    {
        return $isMalay && $translated ? $translated : $default;
    }
}
