<?php

namespace Tests\Feature\Payment;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManualPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_info_is_public(): void
    {
        $response = $this->getJson('/api/payment-info');

        $response->assertOk()->assertJsonStructure([
            'method', 'bank_name', 'bank_account_name', 'bank_account_number', 'duitnow_id', 'instructions',
        ]);
    }

    public function test_a_guest_can_submit_a_payment_proof_for_their_order(): void
    {
        $order = Order::factory()->create([
            'guest_phone' => '0123456789',
            'payment_status' => 'unpaid',
        ]);

        $response = $this->postJson("/api/orders/{$order->order_reference}/payment-proof", [
            'phone' => '0123456789',
            'proof_url' => 'https://res.cloudinary.com/demo/image/upload/receipt.jpg',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.payment_proof_url', 'https://res.cloudinary.com/demo/image/upload/receipt.jpg');
        $this->assertNotNull($order->fresh()->payment_proof_submitted_at);
    }

    public function test_submitting_proof_with_the_wrong_phone_fails(): void
    {
        $order = Order::factory()->create(['guest_phone' => '0123456789']);

        $response = $this->postJson("/api/orders/{$order->order_reference}/payment-proof", [
            'phone' => '0000000000',
            'proof_url' => 'https://res.cloudinary.com/demo/image/upload/receipt.jpg',
        ]);

        $response->assertNotFound();
    }

    public function test_cannot_resubmit_proof_for_an_already_paid_order(): void
    {
        $order = Order::factory()->create([
            'guest_phone' => '0123456789',
            'payment_status' => 'paid',
        ]);

        $response = $this->postJson("/api/orders/{$order->order_reference}/payment-proof", [
            'phone' => '0123456789',
            'proof_url' => 'https://res.cloudinary.com/demo/image/upload/receipt.jpg',
        ]);

        $response->assertUnprocessable();
    }

    public function test_admin_can_verify_payment(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create([
            'payment_status' => 'unpaid',
            'payment_proof_url' => 'https://res.cloudinary.com/demo/image/upload/receipt.jpg',
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson("/api/admin/orders/{$order->id}/verify-payment");

        $response->assertOk()->assertJsonPath('data.payment_status', 'paid');
        $this->assertNotNull($order->fresh()->paid_at);
    }

    public function test_a_customer_cannot_verify_payment(): void
    {
        $customer = User::factory()->create();
        $order = Order::factory()->create(['payment_status' => 'unpaid']);

        $response = $this->actingAs($customer, 'sanctum')
            ->postJson("/api/admin/orders/{$order->id}/verify-payment");

        $response->assertForbidden();
    }

    public function test_verifying_an_already_paid_order_fails(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create(['payment_status' => 'paid']);

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson("/api/admin/orders/{$order->id}/verify-payment");

        $response->assertUnprocessable();
    }
}
