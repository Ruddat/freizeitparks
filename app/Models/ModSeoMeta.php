<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ModSeoMeta extends Model
{
    use HasFactory;

    protected $table = 'mod_seo_metas';

    protected $fillable = [
        'model_type',
        'model_id',
        'title',
        'description',
        'canonical',
        'image',
        'extra_meta',
        'keywords',
        'prevent_override',
    ];

    protected $casts = [
        'extra_meta' => 'array',
        'keywords' => 'array',
        'prevent_override' => 'boolean',
    ];

    // Optionale Hilfsmethoden
    public function seoable()
    {
        return $this->morphTo(__FUNCTION__, 'model_type', 'model_id');
    }
}
