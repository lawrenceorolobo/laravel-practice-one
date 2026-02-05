<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->boolean('proctoring_enabled')->default(false)->after('show_result_to_taker');
            $table->boolean('webcam_required')->default(false)->after('proctoring_enabled');
            $table->boolean('fullscreen_required')->default(false)->after('webcam_required');
        });
    }

    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn(['proctoring_enabled', 'webcam_required', 'fullscreen_required']);
        });
    }
};
