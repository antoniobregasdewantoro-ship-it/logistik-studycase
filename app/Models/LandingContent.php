<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 */
class LandingContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_key',
        'title',
        'subtitle',
        'badge_text',
        'extra_data',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'extra_data' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
