<?php

namespace App\Http\Requests\Order;

use App\Enums\DeliveryMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
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
        $guestRequired = $this->user() === null ? 'required' : 'nullable';

        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.product_variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'guest_name' => [$guestRequired, 'string', 'max:255'],
            'guest_phone' => [$guestRequired, 'string', 'max:30'],
            'guest_email' => ['nullable', 'email', 'max:255'],
            'delivery_method' => ['required', Rule::enum(DeliveryMethod::class)],
            'delivery_zone_id' => [
                Rule::requiredIf(fn () => $this->input('delivery_method') === DeliveryMethod::Delivery->value),
                'nullable',
                'integer',
                'exists:delivery_zones,id',
            ],
            'delivery_address' => [
                Rule::requiredIf(fn () => $this->input('delivery_method') === DeliveryMethod::Delivery->value),
                'nullable',
                'string',
                'max:500',
            ],
            'delivery_date' => ['required', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
