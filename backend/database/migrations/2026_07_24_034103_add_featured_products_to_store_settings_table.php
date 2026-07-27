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
        Schema::table('store_settings', function (Blueprint $table) {
            $table->foreignId('featured_hero_product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('featured_banner_product_id')->nullable()->constrained('products')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('featured_hero_product_id');
            $table->dropConstrainedForeignId('featured_banner_product_id');
        });
    }
};
