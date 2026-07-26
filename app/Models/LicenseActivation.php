<?php

namespace App\Models;

use App\Enums\LicenseStatus;
use Illuminate\Database\Eloquent\Model;

class LicenseActivation extends Model
{
    protected $guarded = [];

    protected $hidden = ['credential_hash', 'provider_data'];

    protected function casts(): array
    {
        return [
            'status' => LicenseStatus::class,
            'provider_data' => 'encrypted:array',
            'verified_at' => 'immutable_datetime',
            'expires_at' => 'immutable_datetime',
        ];
    }
}
