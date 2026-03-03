<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Change question_type from enum to string to support unlimited types
        DB::statement("ALTER TABLE questions MODIFY COLUMN question_type VARCHAR(50) NOT NULL DEFAULT 'single_choice'");

        Schema::table('questions', function (Blueprint $table) {
            $table->json('question_metadata')->nullable()->after('question_type');
        });

        Schema::table('question_options', function (Blueprint $table) {
            $table->string('media_url')->nullable()->after('option_text');
            $table->string('media_type')->nullable()->after('media_url');
        });
    }

    public function down(): void
    {
        Schema::table('question_options', function (Blueprint $table) {
            $table->dropColumn(['media_url', 'media_type']);
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('question_metadata');
        });

        // Revert to enum
        DB::statement("ALTER TABLE questions MODIFY COLUMN question_type ENUM('single_choice','multiple_choice','text_input') NOT NULL DEFAULT 'single_choice'");
    }
};
