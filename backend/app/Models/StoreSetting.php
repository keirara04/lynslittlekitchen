<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'store_name', 'business_registration_no', 'business_type', 'established_since',
    'address_line1', 'address_line2', 'postcode', 'city', 'state',
    'contact_phone', 'contact_email', 'operating_hours',
    'min_order_amount', 'lead_time_days', 'order_cutoff_time', 'allow_pickup', 'allow_delivery',
    'bank_name', 'bank_account_name', 'bank_account_number', 'duitnow_id',
    'alert_email', 'new_order_email_enabled', 'low_stock_threshold',
    'featured_hero_product_id', 'featured_banner_product_id',
])]
class StoreSetting extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'established_since' => 'date:Y-m-d',
            'min_order_amount' => 'decimal:2',
            'lead_time_days' => 'integer',
            'allow_pickup' => 'boolean',
            'allow_delivery' => 'boolean',
            'new_order_email_enabled' => 'boolean',
            'low_stock_threshold' => 'integer',
        ];
    }

    public static function current(): self
    {
        return static::query()->first() ?? static::create([
            'allow_pickup' => true,
            'allow_delivery' => true,
            'new_order_email_enabled' => true,
        ]);
    }

    /** @return BelongsTo<Product, $this> */
    public function heroProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'featured_hero_product_id');
    }

    /** @return BelongsTo<Product, $this> */
    public function bannerProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'featured_banner_product_id');
    }
}
