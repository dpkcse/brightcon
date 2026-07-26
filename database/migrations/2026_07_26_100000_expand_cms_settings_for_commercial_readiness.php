<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->string('product_name')->nullable();
            $table->string('product_version')->nullable();
            $table->string('company_short_name')->nullable();
            $table->text('company_description')->nullable();
            $table->string('dark_logo_path')->nullable();
            $table->string('light_logo_path')->nullable();
            $table->string('powered_by_text')->nullable();
            $table->boolean('show_powered_by')->default(true);
            $table->string('date_format')->nullable()->default('M d, Y');
            $table->string('website_status', 20)->default('active');
            $table->text('maintenance_message')->nullable();
            $table->text('secondary_address')->nullable();
            $table->string('secondary_phone', 50)->nullable();
            $table->string('whatsapp_number', 50)->nullable();
            $table->string('secondary_email')->nullable();
            $table->text('business_hours')->nullable();
            $table->string('contact_recipient_email')->nullable();
            $table->boolean('contact_form_enabled')->default(true);
            $table->boolean('contact_phone_enabled')->default(true);
            $table->boolean('contact_subject_enabled')->default(true);
            $table->string('contact_success_message', 500)->nullable();
            $table->string('contact_failure_message', 500)->nullable();
            $table->string('contact_email_subject_prefix')->nullable();
            $table->string('default_seo_title')->nullable();
            $table->text('default_meta_description')->nullable();
            $table->text('default_meta_keywords')->nullable();
            $table->string('canonical_base_url')->nullable();
            $table->string('open_graph_image_path')->nullable();
            $table->string('twitter_card_image_path')->nullable();
            $table->string('robots_directive')->nullable()->default('index, follow');
            $table->boolean('organization_schema_enabled')->default(false);
            $table->string('google_analytics_id', 32)->nullable();
            $table->string('google_tag_manager_id', 32)->nullable();
            $table->string('search_console_verification', 255)->nullable();
            $table->timestamp('installation_completed_at')->nullable();
            $table->string('installed_version')->nullable();
        });
        Schema::table('theme_settings', function (Blueprint $table): void {
            $table->string('accent_color')->nullable();
            $table->string('header_background_color')->nullable();
            $table->string('header_text_color')->nullable();
            $table->string('button_background_color')->nullable();
            $table->string('button_text_color')->nullable();
            $table->string('link_color')->nullable();
            $table->string('link_hover_color')->nullable();
            $table->string('card_border_radius')->nullable();
            $table->string('input_border_radius')->nullable();
            $table->string('header_logo_variant', 20)->nullable();
            $table->boolean('custom_css_enabled')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('theme_settings', fn (Blueprint $table) => $table->dropColumn(['accent_color', 'header_background_color', 'header_text_color', 'button_background_color', 'button_text_color', 'link_color', 'link_hover_color', 'card_border_radius', 'input_border_radius', 'header_logo_variant', 'custom_css_enabled']));
        Schema::table('site_settings', fn (Blueprint $table) => $table->dropColumn(['product_name', 'product_version', 'company_short_name', 'company_description', 'dark_logo_path', 'light_logo_path', 'powered_by_text', 'show_powered_by', 'date_format', 'website_status', 'maintenance_message', 'secondary_address', 'secondary_phone', 'whatsapp_number', 'secondary_email', 'business_hours', 'contact_recipient_email', 'contact_form_enabled', 'contact_phone_enabled', 'contact_subject_enabled', 'contact_success_message', 'contact_failure_message', 'contact_email_subject_prefix', 'default_seo_title', 'default_meta_description', 'default_meta_keywords', 'canonical_base_url', 'open_graph_image_path', 'twitter_card_image_path', 'robots_directive', 'organization_schema_enabled', 'google_analytics_id', 'google_tag_manager_id', 'search_console_verification', 'installation_completed_at', 'installed_version']));
    }
};
