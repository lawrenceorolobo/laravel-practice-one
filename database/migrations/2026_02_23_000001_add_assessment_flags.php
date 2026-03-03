<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->boolean('auto_end_on_leave')->default(false)->after('fullscreen_required');
            $table->boolean('send_answers_to_taker')->default(false)->after('show_result_to_taker');
        });
    }

    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn(['auto_end_on_leave', 'send_answers_to_taker']);
        });
    }
};
