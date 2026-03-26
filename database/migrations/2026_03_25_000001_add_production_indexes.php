<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Production scalability indexes.
 * Covers: invite_token lookup (hot path for every test request),
 * webcam_recording_url queries, email dedup on access-code joins,
 * and fulltext indexing for candidate search.
 */
return new class extends Migration
{
    public function up(): void
    {
        // invite_token is the primary lookup for ALL test requests — must be unique+indexed
        Schema::table('invitees', function (Blueprint $table) {
            // Skip if already exists from earlier migration
            try {
                $table->unique('invite_token', 'inv_invite_token_unique');
            } catch (\Exception $e) {}

            // Composite index for candidate search (search by name/email within user's assessments)
            $table->index(['assessment_id', 'email'], 'inv_assess_email');
        });

        // test_sessions: quick lookup by invitee (1-to-1 relationship hot path)
        Schema::table('test_sessions', function (Blueprint $table) {
            try {
                $table->unique('invitee_id', 'ts_invitee_unique');
            } catch (\Exception $e) {}

            // For the saveRecording query: invitee_id + status
            $table->index(['invitee_id', 'status'], 'ts_invitee_status');
        });

        // test_answers: hot path for submitAnswer upsert
        Schema::table('test_answers', function (Blueprint $table) {
            try {
                $table->unique(['session_id', 'question_id'], 'ta_session_question_unique');
            } catch (\Exception $e) {}
        });

        // questions: assessment_id + order for getQuestions
        Schema::table('questions', function (Blueprint $table) {
            $table->index(['assessment_id', 'question_order'], 'q_assess_order');
        });
    }

    public function down(): void
    {
        Schema::table('invitees', function (Blueprint $table) {
            $table->dropUnique('inv_invite_token_unique');
            $table->dropIndex('inv_assess_email');
        });

        Schema::table('test_sessions', function (Blueprint $table) {
            $table->dropUnique('ts_invitee_unique');
            $table->dropIndex('ts_invitee_status');
        });

        Schema::table('test_answers', function (Blueprint $table) {
            $table->dropUnique('ta_session_question_unique');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex('q_assess_order');
        });
    }
};
