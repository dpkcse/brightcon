<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicenseActivationRequest extends Model
{
    protected $guarded = [];

    protected $hidden = ['request_token_hash', 'request_token_ciphertext'];

    protected function casts(): array
    {
        return [
            'request_token_ciphertext' => 'encrypted',
            'requested_at' => 'immutable_datetime',
            'expires_at' => 'immutable_datetime',
            'last_checked_at' => 'immutable_datetime',
            'approved_at' => 'immutable_datetime',
            'completed_at' => 'immutable_datetime',
        ];
    }
}
