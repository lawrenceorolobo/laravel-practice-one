<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitees', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('email');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('email_status', 20)->default('pending')->after('status');
            $table->index(['assessment_id', 'status']);
            $table->index(['email', 'assessment_id']);
        });

        Schema::table('assessments', function (Blueprint $table) {
            $table->string('access_code', 32)->nullable()->unique()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('invitees', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'email_status']);
            $table->dropIndex(['assessment_id', 'status']);
            $table->dropIndex(['email', 'assessment_id']);
        });

        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn('access_code');
        });
    }
};
