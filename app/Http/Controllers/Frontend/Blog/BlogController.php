<?php

namespace App\Http\Controllers\Frontend\Blog;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Services\SeoService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BlogController extends Controller
{
    public function index()
    {
        return view('frontend.pages.blog.index'); // enthält <livewire:blog-list />
    }

    public function show($slug)
    {
        $post = BlogPost::where('slug', $slug)
            ->where('status', 'published')
            ->where('publish_start', '<=', now())
            ->with(['category', 'tags'])
            ->firstOrFail();

        // ✅ View erhöhen
        $post->increment('views');

        $seo = app(SeoService::class)->getSeoData($post); // 🔥


        $latestPosts = BlogPost::where('status', 'published')
            ->where('publish_start', '<=', now())
            ->latest('publish_start')
            ->take(5)
            ->get();

        $popularPosts = BlogPost::where('status', 'published')
            ->where('publish_start', '<=', now())
            ->orderBy('views', 'desc')
            ->take(5)
            ->get();

        $categories = BlogCategory::orderBy('name')->get();

        // ✅ Related Posts holen
        $relatedPosts = BlogPost::where('status', 'published')
            ->where('publish_start', '<=', now())
            ->where('id', '!=', $post->id) // nicht sich selbst
            ->where('category_id', $post->category_id) // gleiche Kategorie
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return view('frontend.pages.blog.show', compact('post', 'seo', 'latestPosts', 'popularPosts', 'categories', 'relatedPosts'));
    }

}
