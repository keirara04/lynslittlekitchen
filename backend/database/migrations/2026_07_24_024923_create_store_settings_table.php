<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();

            $table->string('store_name')->nullable();
            $table->string('business_registration_no')->nullable();
            $table->string('business_type')->nullable();
            $table->date('established_since')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('postcode')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();

            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('operating_hours')->nullable();

            $table->decimal('min_order_amount', 10, 2)->nullable();
            $table->unsignedSmallInteger('lead_time_days')->nullable();
            $table->time('order_cutoff_time')->nullable();
            $table->boolean('allow_pickup')->default(true);
            $table->boolean('allow_delivery')->default(true);

            $table->string('bank_name')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('duitnow_id')->nullable();

            $table->string('alert_email')->nullable();
            $table->boolean('new_order_email_enabled')->default(true);
            $table->unsignedSmallInteger('low_stock_threshold')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
