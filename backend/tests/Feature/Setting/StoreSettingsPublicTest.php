<?php

namespace Tests\Feature\Setting;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\StoreSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreSettingsPublicTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_settings_is_public_and_excludes_sensitive_fields(): void
    {
        StoreSetting::current()->update([
            'store_name' => "Lyn's Little Kitchen",
            'bank_account_number' => '1234567890',
            'alert_email' => 'owner@example.com',
        ]);

        $response = $this->getJson('/api/store-settings');

        $response->assertOk()
            ->assertJsonPath('store_name', "Lyn's Little Kitchen")
            ->assertJsonMissingPath('bank_account_number')
            ->assertJsonMissingPath('alert_email');
    }

    public function test_store_settings_includes_configured_featured_products(): void
    {
        $hero = Product::factory()->create(['name' => 'Choc Chip Crunch']);
        ProductImage::create(['product_id' => $hero->id, 'url' => 'https://example.com/hero.jpg', 'sort_order' => 0]);

        StoreSetting::current()->update(['featured_hero_product_id' => $hero->id]);

        $response = $this->getJson('/api/store-settings');

        $response->assertOk()
            ->assertJsonPath('featured_hero_product.name', 'Choc Chip Crunch')
            ->assertJsonPath('featured_hero_product.image_url', 'https://example.com/hero.jpg')
            ->assertJsonPath('featured_banner_product', null);
    }
}
