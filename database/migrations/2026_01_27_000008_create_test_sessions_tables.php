<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('invitee_id');
            $table->uuid('assessment_id');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 255);
            
            // Anti-fraud fingerprinting
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_fingerprint', 255)->nullable();
            $table->string('timezone', 100)->nullable();
            $table->string('canvas_fingerprint', 255)->nullable();
            $table->string('webgl_fingerprint', 255)->nullable();
            $table->string('screen_resolution', 50)->nullable();
            
            // Proctoring data
            $table->integer('fullscreen_exits')->default(0);
            $table->integer('tab_switches')->default(0);
            
            // Timing
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->integer('time_spent_seconds')->nullable();
            
            // Scoring
            $table->decimal('total_score', 5, 2)->default(0);
            $table->decimal('max_score', 5, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->boolean('passed')->nullable();
            
            $table->enum('status', ['in_progress', 'submitted', 'timed_out', 'flagged'])->default('in_progress');
            $table->timestamps();

            $table->foreign('invitee_id')->references('id')->on('invitees')->onDelete('cascade');
            $table->foreign('assessment_id')->references('id')->on('assessments')->onDelete('cascade');
            
            $table->index('invitee_id');
            $table->index('assessment_id');
            $table->index('email');
            $table->index('ip_address');
            $table->index('device_fingerprint');
        });

        Schema::create('test_answers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('session_id');
            $table->uuid('question_id');
            $table->json('selected_options')->nullable();
            $table->text('text_answer')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->decimal('points_earned', 5, 2)->default(0);
            $table->timestamp('answered_at')->useCurrent();
            $table->timestamps();

            $table->foreign('session_id')->references('id')->on('test_sessions')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
            
            $table->unique(['session_id', 'question_id'], 'unique_session_question');
            $table->index('session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_answers');
        Schema::dropIfExists('test_sessions');
    }
};
