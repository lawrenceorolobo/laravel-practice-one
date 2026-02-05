<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Modify existing columns - drop the auto-increment id
            $table->dropColumn('id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->uuid('id')->primary()->first();
            $table->string('first_name', 100)->after('id');
            $table->string('last_name', 100)->after('first_name');
            $table->string('phone', 20)->nullable()->after('password');
            $table->string('company_name', 255)->nullable()->after('phone');
            $table->boolean('is_active')->default(true)->after('company_name');
            $table->enum('subscription_status', ['none', 'active', 'expired', 'cancelled'])->default('none')->after('is_active');
            $table->timestamp('subscription_expires_at')->nullable()->after('subscription_status');
            $table->softDeletes();

            $table->index('subscription_status');
            $table->index('subscription_expires_at');
        });

        // Drop the old name column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name', 
                'phone',
                'company_name',
                'is_active',
                'subscription_status',
                'subscription_expires_at',
            ]);
            $table->dropSoftDeletes();
            $table->dropIndex(['subscription_status']);
            $table->dropIndex(['subscription_expires_at']);
            
            $table->string('name')->after('id');
        });
    }
};
