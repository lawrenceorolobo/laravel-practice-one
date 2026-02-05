<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('assessment_id');
            $table->text('question_text');
            $table->enum('question_type', ['single_choice', 'multiple_choice', 'text_input']);
            $table->integer('points')->default(1);
            $table->integer('question_order');
            $table->timestamps();

            $table->foreign('assessment_id')->references('id')->on('assessments')->onDelete('cascade');
            
            $table->index('assessment_id');
            $table->index(['assessment_id', 'question_order']);
        });

        Schema::create('question_options', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('question_id');
            $table->text('option_text');
            $table->char('option_label', 1); // A, B, C, D...
            $table->boolean('is_correct')->default(false);
            $table->integer('option_order');
            $table->timestamps();

            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
            
            $table->index('question_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');
    }
};
