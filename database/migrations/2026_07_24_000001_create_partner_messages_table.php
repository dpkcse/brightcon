<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_messages', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('designation');
            $table->string('organization')->nullable();
            $table->string('image_path')->nullable();
            $table->string('organization_logo_path')->nullable();
            $table->text('short_message')->nullable();
            $table->longText('full_message');
            $table->text('highlighted_text')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'published_at']);
            $table->index(['is_featured', 'display_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_messages');
    }
};
