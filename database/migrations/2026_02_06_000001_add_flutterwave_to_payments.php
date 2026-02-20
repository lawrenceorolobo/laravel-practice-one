<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('flutterwave_reference', 255)->nullable()->after('paystack_reference');
            $table->string('flutterwave_tx_id', 255)->nullable()->after('flutterwave_reference');
            
            $table->index('flutterwave_reference');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['flutterwave_reference', 'flutterwave_tx_id']);
        });
    }
};
