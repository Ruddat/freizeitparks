<?php

namespace App\Models;

use App\Models\BlogTag;
use App\Models\BlogCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BlogPost extends Model
{
    /** @use HasFactory<\Database\Factories\BlogPostFactory> */
    use HasFactory;

    protected $table = 'blog_posts';

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'category_id',
        'coupon_code',
        'valid_until',
        'publish_start',
        'publish_end',
        'seo_title',
        'seo_description',
        'status',
    ];


    protected $casts = [
        'publish_start' => 'datetime',
        'publish_end' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(BlogTag::class, 'blog_posts_tags', 'post_id', 'tag_id');
    }

    // Accessor
    public function getFeaturedImageUrlAttribute()
    {
        if (!$this->featured_image) {
            return asset('img/default-bg.jpg');
        }

        if (Str::startsWith($this->featured_image, ['http', 'https'])) {
            return $this->featured_image;
        }

        return asset($this->featured_image);
    }


}
