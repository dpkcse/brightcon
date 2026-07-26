<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->boolean('show_contact_map')->default(false);
            $table->text('google_map_embed_url')->nullable();
            $table->string('map_location_name')->nullable();
            $table->string('map_address')->nullable();
            $table->decimal('map_latitude', 10, 7)->nullable();
            $table->decimal('map_longitude', 10, 7)->nullable();
            $table->unsignedTinyInteger('map_zoom')->default(15);
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'show_contact_map',
                'google_map_embed_url',
                'map_location_name',
                'map_address',
                'map_latitude',
                'map_longitude',
                'map_zoom',
            ]);
        });
    }
};
