<?php

namespace App\Livewire\Backend\Blog;

use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\BlogCategory;

class CategoryManager extends Component
{
    public $name;

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
        ]);

        BlogCategory::create([
            'name' => $this->name,
            'slug' => Str::slug($this->name),
        ]);

        session()->flash('success', 'Kategorie erfolgreich gespeichert.');
        $this->reset('name');
    }

    public function delete($id)
    {
        BlogCategory::findOrFail($id)->delete();
        session()->flash('success', 'Kategorie gelöscht.');
    }

    public function render()
    {
        return view('livewire.backend.blog.category-manager', [
            'categories' => BlogCategory::orderBy('name')->get(),
        ]);
    }
}
