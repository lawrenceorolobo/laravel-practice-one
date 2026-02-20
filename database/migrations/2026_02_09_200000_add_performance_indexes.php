<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Invitees: email_status for delivery filtering
        Schema::table('invitees', function (Blueprint $table) {
            $table->index('email_status');
        });

        // Test sessions: composite for completion queries and recent submissions
        Schema::table('test_sessions', function (Blueprint $table) {
            $table->index(['assessment_id', 'status'], 'ts_assessment_status_idx');
            $table->index('submitted_at');
        });

        // Payments: composite for user payment history
        Schema::table('payments', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'pay_user_status_idx');
        });

        // Assessments: composite for user's draft/active lookups
        Schema::table('assessments', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'assess_user_status_idx');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('invitees', function (Blueprint $table) {
            $table->dropIndex(['email_status']);
        });

        Schema::table('test_sessions', function (Blueprint $table) {
            $table->dropIndex('ts_assessment_status_idx');
            $table->dropIndex(['submitted_at']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('pay_user_status_idx');
        });

        Schema::table('assessments', function (Blueprint $table) {
            $table->dropIndex('assess_user_status_idx');
            $table->dropIndex(['created_at']);
        });
    }
};
