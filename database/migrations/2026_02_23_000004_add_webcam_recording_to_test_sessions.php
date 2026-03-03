<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_sessions', function (Blueprint $table) {
            $table->string('webcam_recording_url', 500)->nullable()->after('tab_switches');
            $table->string('webcam_recording_id', 255)->nullable()->after('webcam_recording_url');
        });
    }

    public function down(): void
    {
        Schema::table('test_sessions', function (Blueprint $table) {
            $table->dropColumn(['webcam_recording_url', 'webcam_recording_id']);
        });
    }
};
