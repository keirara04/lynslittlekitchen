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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_proof_url')->nullable()->after('payment_status');
            $table->timestamp('payment_proof_submitted_at')->nullable()->after('payment_proof_url');
            $table->timestamp('paid_at')->nullable()->after('payment_proof_submitted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_proof_url', 'payment_proof_submitted_at', 'paid_at']);
        });
    }
};
