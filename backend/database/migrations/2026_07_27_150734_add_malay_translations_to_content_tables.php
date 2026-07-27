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
        Schema::table('products', function (Blueprint $table) {
            $table->string('name_ms')->nullable()->after('name');
            $table->text('description_ms')->nullable()->after('description');
            $table->text('ingredients_ms')->nullable()->after('ingredients');
            $table->text('allergens_ms')->nullable()->after('allergens');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('name_ms')->nullable()->after('name');
        });

        Schema::table('delivery_zones', function (Blueprint $table) {
            $table->string('name_ms')->nullable()->after('name');
        });

        Schema::table('store_settings', function (Blueprint $table) {
            $table->string('address_line1_ms')->nullable()->after('address_line1');
            $table->string('address_line2_ms')->nullable()->after('address_line2');
            $table->string('city_ms')->nullable()->after('city');
            $table->string('state_ms')->nullable()->after('state');
            $table->string('operating_hours_ms')->nullable()->after('operating_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['name_ms', 'description_ms', 'ingredients_ms', 'allergens_ms']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('name_ms');
        });

        Schema::table('delivery_zones', function (Blueprint $table) {
            $table->dropColumn('name_ms');
        });

        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn(['address_line1_ms', 'address_line2_ms', 'city_ms', 'state_ms', 'operating_hours_ms']);
        });
    }
};
