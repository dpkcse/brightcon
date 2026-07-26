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
        'user_agent', 'workflow_status', 'delivery_status', 'delivered_at', 'delivery_failure_code', 'replied_at', 'replied_by', 'internal_note', 'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean', 'delivered_at' => 'datetime', 'replied_at' => 'datetime', 'archived_at' => 'datetime',
        ];
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }
}
