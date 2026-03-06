<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Speed indexes for API performance optimization.
     * Targets the most frequent query patterns across all controllers.
     */
    public function up(): void
    {
        // invite_token lookups happen on every test-taker request
        Schema::table('invitees', function (Blueprint $table) {
            $table->index('invite_token', 'idx_invitees_invite_token');
        });

        // Payment queries: user payment history + revenue aggregates
        Schema::table('payments', function (Blueprint $table) {
            $table->index(['user_id', 'status', 'paid_at'], 'idx_payments_user_status_paid');
        });

        // User subscription status grouping in admin revenue/dashboard
        Schema::table('users', function (Blueprint $table) {
            $table->index('subscription_status', 'idx_users_subscription_status');
        });

        // Answer upsert + counting per session (updateOrCreate pattern)
        Schema::table('test_answers', function (Blueprint $table) {
            $table->unique(['session_id', 'question_id'], 'uniq_test_answers_session_question');
        });

        // Test session lookups by assessment for analytics
        Schema::table('test_sessions', function (Blueprint $table) {
            $table->index(['assessment_id', 'status'], 'idx_test_sessions_assessment_status');
        });
    }

    public function down(): void
    {
        Schema::table('invitees', function (Blueprint $table) {
            $table->dropIndex('idx_invitees_invite_token');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('idx_payments_user_status_paid');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_subscription_status');
        });
        Schema::table('test_answers', function (Blueprint $table) {
            $table->dropUnique('uniq_test_answers_session_question');
        });
        Schema::table('test_sessions', function (Blueprint $table) {
            $table->dropIndex('idx_test_sessions_assessment_status');
        });
    }
};
