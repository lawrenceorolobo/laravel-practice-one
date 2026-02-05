<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('assessment_id');
            $table->string('email', 255);
            $table->char('invite_token', 64)->unique();
            $table->timestamp('email_sent_at')->nullable();
            $table->timestamp('email_opened_at')->nullable();
            $table->enum('status', ['pending', 'sent', 'opened', 'started', 'completed', 'expired'])->default('pending');
            $table->timestamps();

            $table->foreign('assessment_id')->references('id')->on('assessments')->onDelete('cascade');
            
            $table->unique(['assessment_id', 'email'], 'unique_assessment_email');
            $table->index('invite_token');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitees');
    }
};
