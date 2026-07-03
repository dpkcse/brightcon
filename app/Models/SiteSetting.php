<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'company_name',
        'tagline',
        'logo',
        'favicon',
        'email',
        'phone',
        'address',
        'profile_pdf',
        'default_language',
        'timezone',
        'copyright_text',
        'developer_name',
        'developer_link',
    ];

    protected function casts(): array
    {
        return [
        ];
    }
}
