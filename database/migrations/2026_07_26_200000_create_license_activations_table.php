<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('license_activations', function (Blueprint $table): void {
            $table->id();
            $table->uuid('installation_id')->unique();
            $table->string('provider', 64)->index();
            $table->string('status', 32)->index();
            $table->string('external_reference')->nullable();
            $table->string('credential_hash', 64);
            $table->string('host_hash', 64);
            $table->text('provider_data')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('license_activations');
    }
};
