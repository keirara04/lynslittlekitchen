<?php

namespace App\Http\Requests\DeliveryZone;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeliveryZoneRequest extends FormRequest
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
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
        ];
    }
}
