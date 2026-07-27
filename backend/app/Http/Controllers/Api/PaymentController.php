<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function info(): JsonResponse
    {
        $settings = StoreSetting::current();

        return response()->json([
            'method' => 'manual_bank_transfer',
            'bank_name' => $settings->bank_name ?? config('app.bank_name'),
            'bank_account_name' => $settings->bank_account_name ?? config('app.bank_account_name'),
            'bank_account_number' => $settings->bank_account_number ?? config('app.bank_account_number'),
            'duitnow_id' => $settings->duitnow_id ?? config('app.duitnow_id'),
            'instructions' => 'Transfer the order total to the account above, then upload your receipt so we can confirm payment.',
        ]);
    }
}
