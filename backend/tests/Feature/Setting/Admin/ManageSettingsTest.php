<?php

namespace Tests\Feature\Setting\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_fetch_settings_and_a_default_row_is_created(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/admin/settings');

        $response->assertOk()->assertJsonPath('data.id', 1);
        $this->assertDatabaseHas('store_settings', ['id' => 1]);
    }

    public function test_admin_can_update_business_profile_fields(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin, 'sanctum')->putJson('/api/admin/settings', [
            'store_name' => "Lyn's Little Kitchen",
            'business_type' => 'Enterprise',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.store_name', "Lyn's Little Kitchen")
            ->assertJsonPath('data.business_type', 'Enterprise');
        $this->assertDatabaseHas('store_settings', ['id' => 1, 'store_name' => "Lyn's Little Kitchen"]);
    }

    public function test_updating_one_tab_does_not_touch_other_tabs_fields(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin, 'sanctum')->putJson('/api/admin/settings', [
            'bank_name' => 'Maybank',
            'bank_account_number' => '1234567890',
        ]);

        $response = $this->actingAs($admin, 'sanctum')->putJson('/api/admin/settings', [
            'store_name' => "Lyn's Little Kitchen",
        ]);

        $response->assertOk()
            ->assertJsonPath('data.store_name', "Lyn's Little Kitchen")
            ->assertJsonPath('data.bank_name', 'Maybank')
            ->assertJsonPath('data.bank_account_number', '1234567890');
    }

    public function test_a_non_admin_cannot_view_or_update_settings(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer, 'sanctum')->getJson('/api/admin/settings')->assertForbidden();
        $this->actingAs($customer, 'sanctum')->putJson('/api/admin/settings', ['store_name' => 'x'])->assertForbidden();
    }

    public function test_invalid_fields_are_rejected(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin, 'sanctum')->putJson('/api/admin/settings', [
            'contact_email' => 'not-an-email',
            'min_order_amount' => -5,
            'order_cutoff_time' => '25:99',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['contact_email', 'min_order_amount', 'order_cutoff_time']);
    }
}
