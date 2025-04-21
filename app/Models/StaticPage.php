<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaticPage extends Model
{
    protected $fillable = [
        'title', 'slug', 'content', 'show_in_footer', 'show_in_nav',
    ];


    protected $casts = [
        'show_in_footer' => 'boolean',
        'show_in_nav' => 'boolean',
    ];

    
}
