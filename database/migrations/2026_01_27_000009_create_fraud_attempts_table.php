<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fraud_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('assessment_id');
            $table->string('email', 255)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('device_fingerprint', 255)->nullable();
            $table->uuid('matched_session_id')->nullable();
            $table->enum('match_type', ['email_exact', 'email_fuzzy', 'name_fuzzy', 'ip', 'device', 'combo']);
            $table->decimal('similarity_score', 5, 2)->nullable();
            $table->boolean('blocked')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('assessment_id')->references('id')->on('assessments')->onDelete('cascade');
            $table->foreign('matched_session_id')->references('id')->on('test_sessions')->onDelete('set null');
            
            $table->index('assessment_id');
            $table->index('email');
            $table->index('ip_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fraud_attempts');
    }
};
