<?php

namespace App\Http\Requests\Order;

use App\Enums\DeliveryMethod;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'order_status' => ['required', Rule::enum(OrderStatus::class)],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            /** @var Order $order */
            $order = $this->route('order');
            $next = $this->input('order_status');

            if (! $next || ! $order) {
                return;
            }

            $next = OrderStatus::from($next);

            if (! $order->order_status->canTransitionTo($next)) {
                $allowed = array_map(fn (OrderStatus $s) => $s->value, $order->order_status->allowedNextStatuses());

                $validator->errors()->add(
                    'order_status',
                    $allowed === []
                        ? "Order is already in a terminal status ({$order->order_status->value}) and cannot be changed."
                        : "Cannot move from \"{$order->order_status->value}\" to \"{$next->value}\". Allowed next: ".implode(', ', $allowed).'.',
                );

                return;
            }

            $requiresPayment = $next === OrderStatus::Preparing
                && $order->delivery_method !== DeliveryMethod::Pickup
                && $order->payment_status !== PaymentStatus::Paid;

            if ($requiresPayment) {
                $validator->errors()->add(
                    'order_status',
                    'Payment must be verified before this order can move to preparing. Verify the payment receipt first (pickup orders are exempt).',
                );
            }
        });
    }
}
