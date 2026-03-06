<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Compound indexes for frequently queried patterns
        Schema::table('test_sessions', function (Blueprint $table) {
            $table->index(['assessment_id', 'status', 'created_at'], 'ts_assess_status_created');
        });

        Schema::table('invitees', function (Blueprint $table) {
            $table->index(['assessment_id', 'status', 'created_at'], 'inv_assess_status_created');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index(['status', 'paid_at'], 'pay_status_paid');
        });

        Schema::table('feature_flags', function (Blueprint $table) {
            $table->unique('key', 'ff_key_unique');
        });
    }

    public function down(): void
    {
        Schema::table('test_sessions', function (Blueprint $table) {
            $table->dropIndex('ts_assess_status_created');
        });
        Schema::table('invitees', function (Blueprint $table) {
            $table->dropIndex('inv_assess_status_created');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('pay_status_paid');
        });
        Schema::table('feature_flags', function (Blueprint $table) {
            $table->dropUnique('ff_key_unique');
        });
    }
};
