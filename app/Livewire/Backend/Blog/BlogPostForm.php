<?php

namespace App\Livewire\Backend\Blog;


use App\Models\BlogTag;
use Livewire\Component;
use App\Models\BlogPost;
use Illuminate\Support\Str;
use App\Models\BlogCategory;



class BlogPostForm extends Component
{
    public $post;
    public $title, $slug, $excerpt, $content, $featured_image;
    public $category_id, $tags = [];
    public $coupon_code, $valid_until;
    public $publish_start, $publish_end;
    public $seo_title, $seo_description;
    public $status = 'draft';

    public function mount($post = null)
    {
        if ($post) {
            $this->post = BlogPost::findOrFail($post);

            $this->fill([
                'title' => $this->post->title,
                'slug' => $this->post->slug,
                'excerpt' => $this->post->excerpt,
                'content' => $this->post->content,
                'featured_image' => $this->post->featured_image,
                'category_id' => $this->post->category_id,
                'coupon_code' => $this->post->coupon_code,
                'valid_until' => $this->post->valid_until ? \Carbon\Carbon::parse($this->post->valid_until)->format('Y-m-d') : null,
                'publish_start' => $this->post->publish_start ? \Carbon\Carbon::parse($this->post->publish_start)->format('Y-m-d\TH:i') : null,
                'publish_end' => $this->post->publish_end ? \Carbon\Carbon::parse($this->post->publish_end)->format('Y-m-d\TH:i') : null,
                'seo_title' => $this->post->seo_title,
                'seo_description' => $this->post->seo_description,
                'status' => $this->post->status,
            ]);

            $this->tags = $this->post->tags->pluck('id')->toArray();
        }
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'category_id' => 'required|exists:blog_categories,id',
            'status' => 'required|in:draft,published,scheduled',
        ]);

        $slug = $this->slug ?: Str::slug($this->title);

        // ✅ Standardwerte setzen, wenn nicht vorhanden
        $publishStart = $this->publish_start ? \Carbon\Carbon::parse($this->publish_start) : now();
        $publishEnd = $this->publish_end ? \Carbon\Carbon::parse($this->publish_end) : now()->addMonths(3);
        $validUntil = $this->valid_until ? \Carbon\Carbon::parse($this->valid_until) : null;

        $post = BlogPost::updateOrCreate(
            ['id' => $this->post->id ?? null],
            [
                'title' => $this->title,
                'slug' => $slug,
                'excerpt' => $this->excerpt,
                'content' => $this->content,
                'featured_image' => $this->featured_image,
                'category_id' => $this->category_id,
                'coupon_code' => $this->coupon_code,
                'valid_until' => $validUntil,
                'publish_start' => $publishStart,
                'publish_end' => $publishEnd,
                'seo_title' => $this->seo_title,
                'seo_description' => $this->seo_description,
                'status' => $this->status,
            ]
        );

        $post->tags()->sync($this->tags);

        session()->flash('success', 'Blogpost gespeichert.');

        return redirect()->route('admin.blogposts');
    }


    public function render()
    {
        $categories = BlogCategory::all();
        $allTags = BlogTag::all(); // ALLE Tags separat laden!

        return view('livewire.backend.blog.blog-post-form', compact('categories', 'allTags'))
            ->layout('backend.layouts.backend');
    }
}
