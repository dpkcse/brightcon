<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FeatureItem extends Model
{
    protected $fillable = [
        'title',
        'short_text',
        'icon_class',
        'image',
        'sort_order',
        'status',
    ];

    protected function casts(): array
    {
        return [
        'status' => 'boolean',
        'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
