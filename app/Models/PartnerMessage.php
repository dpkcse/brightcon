<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PartnerMessage extends Model
{
    protected $fillable = ['name', 'designation', 'organization', 'image_path', 'organization_logo_path', 'short_message', 'full_message', 'highlighted_text', 'linkedin_url', 'display_order', 'is_featured', 'is_active', 'published_at'];

    protected function casts(): array
    {
        return ['is_featured' => 'boolean', 'is_active' => 'boolean', 'display_order' => 'integer', 'published_at' => 'datetime'];
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query->where('is_active', true)->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('is_featured')->orderBy('display_order')->orderByDesc('published_at')->orderByDesc('id');
    }
}
