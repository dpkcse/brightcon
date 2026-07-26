<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $table = 'equipment';

    protected $fillable = ['name', 'slug', 'category', 'brand', 'model_number', 'capacity', 'quantity', 'unit', 'short_description', 'description', 'image_path', 'image_alt', 'status', 'is_featured', 'display_order', 'published_at'];

    protected function casts(): array
    {
        return ['quantity' => 'integer', 'is_featured' => 'boolean', 'display_order' => 'integer', 'published_at' => 'datetime'];
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('status', 'published')->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderByDesc('is_featured')->orderBy('display_order')->orderBy('name');
    }
}
