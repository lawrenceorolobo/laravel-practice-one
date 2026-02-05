<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->integer('duration_minutes')->default(30);
            $table->decimal('pass_percentage', 5, 2)->default(50.00);
            $table->boolean('allow_back_navigation')->default(false);
            $table->boolean('shuffle_questions')->default(true);
            $table->boolean('shuffle_options')->default(true);
            $table->boolean('show_result_to_taker')->default(true);
            $table->timestamp('start_datetime')->nullable();
            $table->timestamp('end_datetime')->nullable();
            $table->enum('status', ['draft', 'scheduled', 'active', 'completed', 'cancelled'])->default('draft');
            $table->integer('total_questions')->default(0);
            $table->integer('total_invites')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->index('user_id');
            $table->index('status');
            $table->index('start_datetime');
            $table->index('end_datetime');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
