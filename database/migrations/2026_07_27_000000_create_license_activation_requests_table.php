<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('license_activation_requests', function (Blueprint $table): void {
            $table->id();
            $table->uuid('installation_uuid')->index();
            $table->string('provider', 32)->default('naxas_portal');
            $table->string('remote_request_id', 191)->unique();
            $table->string('request_token_hash', 64);
            $table->text('request_token_ciphertext')->nullable();
            $table->string('masked_request_token', 64);
            $table->string('normalized_domain', 255)->index();
            $table->string('product_reference', 100);
            $table->string('application_version', 50);
            $table->string('portal_url', 2048);
            $table->string('status', 32)->index();
            $table->dateTime('requested_at');
            $table->dateTime('expires_at')->index();
            $table->dateTime('last_checked_at')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->string('failure_code', 64)->nullable();
            $table->string('safe_failure_message', 255)->nullable();
            $table->timestamps();
            $table->index(['installation_uuid', 'provider', 'normalized_domain'], 'license_request_scope');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('license_activation_requests');
    }
};
