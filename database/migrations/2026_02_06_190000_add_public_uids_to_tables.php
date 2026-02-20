<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Add public UIDs to all tables for external reference
 * Format: prefix_timestamp-random (e.g., usr_1707235200_8x3k2)
 * 
 * This keeps UUID as primary key internally but adds human-readable UIDs
 */
return new class extends Migration
{
    public function up(): void
    {
        // Users table - add uid
        Schema::table('users', function (Blueprint $table) {
            $table->string('uid', 30)->unique()->after('id')->nullable();
        });

        // Generate UIDs for existing users
        DB::table('users')->orderBy('created_at')->each(function ($user) {
            DB::table('users')->where('id', $user->id)->update([
                'uid' => 'usr_' . time() . '_' . substr(md5($user->id), 0, 6)
            ]);
        });

        // Make uid not nullable after populating
        Schema::table('users', function (Blueprint $table) {
            $table->string('uid', 30)->nullable(false)->change();
        });

        // Admins table - add uid
        Schema::table('admins', function (Blueprint $table) {
            $table->string('uid', 30)->unique()->after('id')->nullable();
        });

        // Assessments table - add public_id
        Schema::table('assessments', function (Blueprint $table) {
            $table->string('public_id', 30)->unique()->after('id')->nullable();
        });

        // Payments table - add public_id  
        Schema::table('payments', function (Blueprint $table) {
            $table->string('public_id', 30)->unique()->after('id')->nullable();
        });

        // Test sessions table - add public_id
        Schema::table('test_sessions', function (Blueprint $table) {
            $table->string('public_id', 30)->unique()->after('id')->nullable();
        });

        // Invitees table - add public_id
        Schema::table('invitees', function (Blueprint $table) {
            $table->string('public_id', 30)->unique()->after('id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('uid');
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('uid');
        });

        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn('public_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('public_id');
        });

        Schema::table('test_sessions', function (Blueprint $table) {
            $table->dropColumn('public_id');
        });

        Schema::table('invitees', function (Blueprint $table) {
            $table->dropColumn('public_id');
        });
    }
};
