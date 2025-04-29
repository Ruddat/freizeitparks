<?php

namespace App\Livewire\Backend\Blog;

use App\Models\BlogTag;
use Livewire\Component;
use Illuminate\Support\Str;

class TagManager extends Component
{
    public $name;

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
        ]);

        BlogTag::create([
            'name' => $this->name,
            'slug' => Str::slug($this->name),
        ]);

        session()->flash('success', 'Tag erfolgreich gespeichert.');
        $this->reset('name');
    }

    public function delete($id)
    {
        BlogTag::findOrFail($id)->delete();
        session()->flash('success', 'Tag gelöscht.');
    }

    public function render()
    {
        return view('livewire.backend.blog.tag-manager', [
            'tags' => BlogTag::orderBy('name')->get(),
        ]);
    }
}
