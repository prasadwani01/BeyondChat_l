<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'original_url',
        'original_content',
        'enhanced_content',
        'reference_sources',
        'status',
    ];

    protected $casts = [
        'reference_sources' => 'array',
    ];
}