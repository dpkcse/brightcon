<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    protected $fillable = ['title', 'slug', 'excerpt', 'content', 'featured_image_path', 'featured_image_alt', 'seo_title', 'seo_description', 'seo_keywords', 'status', 'is_featured', 'display_order', 'published_at', 'created_by', 'updated_by'];

    protected function casts(): array
    {
        return ['is_featured' => 'boolean', 'display_order' => 'integer', 'published_at' => 'datetime'];
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('status', 'published')->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('display_order')->orderBy('title');
    }
}
