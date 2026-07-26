<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->string('featured_image_path')->nullable();
            $table->string('featured_image_alt')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
        Schema::table('menu_items', function (Blueprint $table): void {
            $table->string('menu_location', 20)->default('header')->after('id')->index();
            $table->foreignId('parent_id')->nullable()->after('menu_location')->constrained('menu_items')->restrictOnDelete();
            $table->string('link_type', 20)->default('legacy')->after('url');
            $table->string('route_name')->nullable()->after('link_type');
            $table->foreignId('page_id')->nullable()->after('route_name')->constrained('pages')->restrictOnDelete();
            $table->string('external_url')->nullable()->after('page_id');
        });
        foreach (['equipment' => 'name', 'competencies' => 'title'] as $tableName => $label) {
            Schema::create($tableName, function (Blueprint $table) use ($label, $tableName): void {
                $table->id();
                $table->string($label);
                $table->string('slug')->unique();
                if ($tableName === 'equipment') {
                    $table->string('category')->nullable();
                    $table->string('brand')->nullable();
                    $table->string('model_number')->nullable();
                    $table->string('capacity')->nullable();
                    $table->unsignedInteger('quantity')->nullable();
                    $table->string('unit')->nullable();
                } else {
                    $table->string('icon')->nullable();
                }
                $table->text('short_description')->nullable();
                $table->longText('description')->nullable();
                $table->string('image_path')->nullable();
                $table->string('image_alt')->nullable();
                $table->string('status', 20)->default('draft')->index();
                $table->boolean('is_featured')->default(false);
                $table->unsignedInteger('display_order')->default(0);
                $table->timestamp('published_at')->nullable();
                $table->timestamps();
            });
        }
        Schema::table('contact_messages', function (Blueprint $table): void {
            $table->string('workflow_status', 20)->default('unread')->index();
            $table->string('delivery_status', 20)->default('pending')->index();
            $table->timestamp('delivered_at')->nullable();
            $table->string('delivery_failure_code', 50)->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->foreignId('replied_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('internal_note')->nullable();
            $table->timestamp('archived_at')->nullable();
        });
        Schema::table('homepage_sections', fn (Blueprint $table) => $table->unsignedSmallInteger('record_limit')->nullable()->after('sort_order'));
    }

    public function down(): void
    {
        Schema::table('homepage_sections', fn (Blueprint $table) => $table->dropColumn('record_limit'));
        Schema::table('contact_messages', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('replied_by');
            $table->dropColumn(['workflow_status', 'delivery_status', 'delivered_at', 'delivery_failure_code', 'replied_at', 'internal_note', 'archived_at']);
        });
        Schema::dropIfExists('competencies');
        Schema::dropIfExists('equipment');
        Schema::table('menu_items', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('page_id');
            $table->dropConstrainedForeignId('parent_id');
            $table->dropColumn(['menu_location', 'link_type', 'route_name', 'external_url']);
        });
        Schema::dropIfExists('pages');
    }
};
