<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key', 100)->primary();
            $table->text('value');
            $table->enum('type', ['string', 'int', 'float', 'bool', 'json'])->default('string');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('idempotency_keys', function (Blueprint $table) {
            $table->char('key_hash', 64)->primary();
            $table->uuid('user_id')->nullable();
            $table->string('endpoint', 255);
            $table->char('request_hash', 64);
            $table->integer('response_code')->nullable();
            $table->text('response_body')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();

            $table->index('expires_at');
        });

        Schema::create('rate_limits', function (Blueprint $table) {
            $table->id();
            $table->string('identifier', 255);
            $table->string('endpoint', 255);
            $table->integer('hits')->default(1);
            $table->timestamp('window_start')->useCurrent();

            $table->index(['identifier', 'endpoint']);
            $table->index('window_start');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rate_limits');
        Schema::dropIfExists('idempotency_keys');
        Schema::dropIfExists('settings');
    }
};
