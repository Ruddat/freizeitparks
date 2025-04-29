<?php

namespace App\Livewire\Frontend\Blog;

use Livewire\Component;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;

class BlogList extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';
    public $sort = 'newest';
    public $selectedTags = [];
    public $perPage = 9;

    protected $queryString = [
        'search' => ['except' => '', 'as' => 's'],
        'category' => ['except' => '', 'as' => 'cat'],
        'sort' => ['except' => 'newest'],
        'selectedTags' => ['except' => [], 'as' => 'tags'],
        'page' => ['except' => 1],
    ];

    public function resetFilters()
    {
        $this->reset(['search', 'category', 'sort', 'selectedTags']);
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function updatingSelectedTags()
    {
        $this->resetPage();
    }

    public function calculateReadingTime($content)
    {
        $wordCount = str_word_count(strip_tags($content));
        return max(1, ceil($wordCount / 200)); // 200 Wörter pro Minute
    }

    public function toggleTag($tagSlug)
    {
        if (($key = array_search($tagSlug, $this->selectedTags)) !== false) {
            unset($this->selectedTags[$key]);
        } else {
            $this->selectedTags[] = $tagSlug;
        }
        $this->selectedTags = array_values($this->selectedTags); // Reindex array
    }

    public function loadMore()
    {
        $this->perPage += 9;
    }

    public function render()
    {
        $posts = BlogPost::query()
            ->with(['category', 'tags'])
            ->when($this->search, function ($query) {
                $query->where(function($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('excerpt', 'like', '%' . $this->search . '%')
                      ->orWhere('content', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->category, function ($query) {
                $query->whereHas('category', function ($q) {
                    $q->where('slug', $this->category);
                });
            })
            ->when($this->selectedTags, function ($query) {
                $query->whereHas('tags', function ($q) {
                    $q->whereIn('slug', $this->selectedTags);
                });
            })
            ->where('status', 'published')
            ->where('publish_start', '<=', now())
            ->when($this->sort === 'newest', function ($query) {
                $query->orderBy('publish_start', 'desc');
            })
            ->when($this->sort === 'oldest', function ($query) {
                $query->orderBy('publish_start', 'asc');
            })
            ->when($this->sort === 'popular', function ($query) {
                $query->orderBy('views', 'desc');
            })
            ->paginate($this->perPage)
            ->through(function ($post) {
                $post->reading_time = $this->calculateReadingTime($post->content);
                return $post;
            });

        $categories = BlogCategory::orderBy('name')->get();
        $popularTags = BlogTag::withCount('posts')
                        ->orderBy('posts_count', 'desc')
                        ->limit(10)
                        ->get();

        return view('livewire.frontend.blog.blog-list', [
            'posts' => $posts,
            'categories' => $categories,
            'popularTags' => $popularTags,
        ]);
    }
}
