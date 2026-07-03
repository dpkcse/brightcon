<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'subject',
        'message',
        'is_read',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
        'is_read' => 'boolean',
        ];
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }
}
