<?php

namespace App\Http\Controllers\Frontend\Blog;

use App\Models\BlogTag;
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



    public function category(string $slug)
    {
        $category = BlogCategory::where('slug', $slug)->firstOrFail();

        $posts = BlogPost::where('status', 'published')
            ->where('publish_start', '<=', now())
            ->where('category_id', $category->id)
            ->latest('publish_start')
            ->paginate(10);

        $seo = app(SeoService::class)->getSeoData($category);

        $data = $this->getSidebarData();

        return view('frontend.pages.blog.category', array_merge($data, compact('category', 'posts', 'seo')));
    }

    public function tag(string $slug)
    {
        $tag = BlogTag::where('slug', $slug)->firstOrFail();

        $posts = BlogPost::whereHas('tags', fn ($q) => $q->where('slug', $slug))
            ->where('status', 'published')
            ->where('publish_start', '<=', now())
            ->latest('publish_start')
            ->paginate(10);

        $seo = app(SeoService::class)->getSeoData($tag);

        $data = $this->getSidebarData();

        return view('frontend.pages.blog.tag', array_merge($data, compact('tag', 'posts', 'seo')));
    }

    private function getSidebarData(): array
    {
        return [
            'latestPosts' => \App\Models\BlogPost::where('status', 'published')
                ->where('publish_start', '<=', now())
                ->latest('publish_start')
                ->take(5)
                ->get(),
            'categories' => \App\Models\BlogCategory::orderBy('name')->get(),
        ];
    }

}
