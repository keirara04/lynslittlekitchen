<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
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
            'store_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'business_registration_no' => ['sometimes', 'nullable', 'string', 'max:255'],
            'business_type' => ['sometimes', 'nullable', 'string', 'max:255'],
            'established_since' => ['sometimes', 'nullable', 'date'],
            'address_line1' => ['sometimes', 'nullable', 'string', 'max:255'],
            'address_line2' => ['sometimes', 'nullable', 'string', 'max:255'],
            'postcode' => ['sometimes', 'nullable', 'string', 'max:20'],
            'city' => ['sometimes', 'nullable', 'string', 'max:255'],
            'state' => ['sometimes', 'nullable', 'string', 'max:255'],

            'contact_phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'contact_email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'operating_hours' => ['sometimes', 'nullable', 'string', 'max:255'],

            'min_order_amount' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'lead_time_days' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'order_cutoff_time' => ['sometimes', 'nullable', 'date_format:H:i'],
            'allow_pickup' => ['sometimes', 'boolean'],
            'allow_delivery' => ['sometimes', 'boolean'],

            'bank_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'bank_account_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'bank_account_number' => ['sometimes', 'nullable', 'string', 'max:50'],
            'duitnow_id' => ['sometimes', 'nullable', 'string', 'max:255'],

            'alert_email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'new_order_email_enabled' => ['sometimes', 'boolean'],
            'low_stock_threshold' => ['sometimes', 'nullable', 'integer', 'min:0'],

            'featured_hero_product_id' => ['sometimes', 'nullable', 'integer', 'exists:products,id'],
            'featured_banner_product_id' => ['sometimes', 'nullable', 'integer', 'exists:products,id'],
        ];
    }
}
