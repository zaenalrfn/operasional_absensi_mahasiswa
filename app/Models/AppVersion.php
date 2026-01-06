<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'version_number',
        'release_date',
        'release_notes',
        'is_mandatory',
        'platform',
        'download_url',
    ];

    protected $casts = [
        'release_date' => 'date',
        'release_notes' => 'array',
        'is_mandatory' => 'boolean',
    ];
}
