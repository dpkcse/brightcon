<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('company_name')->nullable();
            $table->string('tagline')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('profile_pdf')->nullable();
            $table->string('default_language')->nullable()->default('en');
            $table->string('timezone')->nullable()->default('Asia/Dhaka');
            $table->string('copyright_text')->nullable();
            $table->string('developer_name')->nullable();
            $table->string('developer_link')->nullable();
            $table->timestamps();
        });

        Schema::create('theme_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('primary_color')->default('#d80d4c');
            $table->string('secondary_color')->default('#ffffff');
            $table->string('footer_background_color')->nullable();
            $table->string('body_text_color')->nullable();
            $table->string('heading_text_color')->nullable();
            $table->string('body_font_family')->nullable();
            $table->string('heading_font_family')->nullable();
            $table->string('base_font_size')->nullable();
            $table->string('h1_font_size')->nullable();
            $table->string('h2_font_size')->nullable();
            $table->string('h3_font_size')->nullable();
            $table->string('button_radius')->nullable();
            $table->string('section_spacing')->nullable();
            $table->longText('custom_css')->nullable();
            $table->timestamps();
        });

        Schema::create('social_links', function (Blueprint $table): void {
            $table->id();
            $table->string('platform');
            $table->string('icon_class')->nullable();
            $table->string('url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('menu_items', function (Blueprint $table): void {
            $table->id();
            $table->string('label');
            $table->string('url');
            $table->string('target')->default('_self');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('sliders', function (Blueprint $table): void {
            $table->id();
            $table->string('heading')->nullable();
            $table->string('sub_heading')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_link')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('feature_items', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->text('short_text')->nullable();
            $table->string('icon_class')->nullable();
            $table->string('image')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('homepage_sections', function (Blueprint $table): void {
            $table->id();
            $table->string('section_key')->unique();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->longText('content')->nullable();
            $table->string('image')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_link')->nullable();
            $table->string('background_color')->nullable();
            $table->string('background_image')->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('project_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('featured_image')->nullable();
            $table->unsignedTinyInteger('progress_percentage')->default(0);
            $table->text('short_description')->nullable();
            $table->longText('full_description')->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();

            $table->index('project_category_id');
            $table->index('status');
            $table->index('is_featured');
            $table->index('sort_order');
        });

        Schema::create('services', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->string('icon_class')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('full_description')->nullable();
            $table->boolean('status')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
        });

        Schema::create('gallery_images', function (Blueprint $table): void {
            $table->id();
            $table->string('title')->nullable();
            $table->string('image');
            $table->string('category')->nullable()->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('status')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table): void {
            $table->id();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->boolean('is_read')->default(false)->index();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index('created_at');
        });

        Schema::create('footer_links', function (Blueprint $table): void {
            $table->id();
            $table->string('label');
            $table->string('url');
            $table->string('target')->default('_self');
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('status')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('footer_links');
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('gallery_images');
        Schema::dropIfExists('services');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('project_categories');
        Schema::dropIfExists('homepage_sections');
        Schema::dropIfExists('feature_items');
        Schema::dropIfExists('sliders');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('social_links');
        Schema::dropIfExists('theme_settings');
        Schema::dropIfExists('site_settings');
    }
};
