<?php

namespace App\Livewire\Backend\Blog;

use Livewire\Component;

class BlogPostList extends Component
{
    public $search = '';

    public function render()
    {
        $posts = BlogPost::when($this->search, function($query) {
                $query->where('title', 'like', '%'.$this->search.'%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.backend.blog.blog-post-list', compact('posts'));
    }
}
