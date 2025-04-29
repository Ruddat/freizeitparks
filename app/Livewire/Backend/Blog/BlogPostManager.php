<?php

namespace App\Livewire\Backend\Blog;

use Livewire\Component;

use App\Models\BlogPost;
use Livewire\WithPagination;

class BlogPostManager extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'created_at'; // Default Sortierung
    public $sortDirection = 'desc';   // Default Richtung
    public $filterStatus = 'all';
    public $filterCouponOnly = false; // NEU: Filter für Coupons

    public $editingPostId = null;
    public $editingTitle = '';
    public $editingStatus = '';



    public function startEditing($postId)
    {
        $post = BlogPost::findOrFail($postId);

        $this->editingPostId = $post->id;
        $this->editingTitle = $post->title;
        $this->editingStatus = $post->status;
    }

    public function saveEditing()
    {
        if (!$this->editingPostId) {
            return;
        }

        $post = BlogPost::findOrFail($this->editingPostId);

        $this->validate([
            'editingTitle' => 'required|string|max:255',
            'editingStatus' => 'required|in:draft,published,scheduled',
        ]);

        $post->update([
            'title' => $this->editingTitle,
            'status' => $this->editingStatus,
        ]);

        session()->flash('success', 'Blogpost aktualisiert.');

        $this->editingPostId = null;
        $this->editingTitle = '';
        $this->editingStatus = '';
    }





    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function setFilter($status)
    {
        $this->filterStatus = $status;
        $this->resetPage(); // Immer auf Seite 1 zurückspringen
    }

    public function toggleCouponFilter()
    {
        $this->filterCouponOnly = !$this->filterCouponOnly;
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterStatus = 'all';
        $this->filterCouponOnly = false;
        $this->resetPage();
    }

    public function render()
    {
        $posts = BlogPost::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%'.$this->search.'%');
            })
            ->when($this->filterStatus !== 'all', function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterCouponOnly, function ($query) {
                $query->whereNotNull('coupon_code');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.backend.blog.blog-post-list', compact('posts'))
            ->layout('backend.layouts.backend');
    }
}
