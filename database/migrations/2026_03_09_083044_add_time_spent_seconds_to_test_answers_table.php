<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_answers', function (Blueprint $table) {
            $table->unsignedInteger('time_spent_seconds')->nullable()->after('answered_at');
        });
    }

    public function down(): void
    {
        Schema::table('test_answers', function (Blueprint $table) {
            $table->dropColumn('time_spent_seconds');
        });
    }
};
