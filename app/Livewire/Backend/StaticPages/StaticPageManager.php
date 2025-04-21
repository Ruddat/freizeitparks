<?php

namespace App\Livewire\Backend\StaticPages;

use Livewire\Component;
use App\Models\StaticPage;

class StaticPageManager extends Component
{
    public $pages, $title, $slug, $content_txt, $show_in_footer = false, $show_in_nav = false, $editId = null;
    public $continent_text = '';

    public function mount()
    {
        $this->loadPages();
    }

    public function loadPages()
    {
        $this->pages = StaticPage::orderBy('title')->get();
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string',
            'slug' => 'required|string|unique:static_pages,slug,' . $this->editId,
        ]);

        StaticPage::updateOrCreate(
            ['id' => $this->editId],
            [
                'title' => $this->title,
                'slug' => $this->slug,
                'content' => $this->content_txt,
                'show_in_footer' => $this->show_in_footer,
                'show_in_nav' => $this->show_in_nav,
            ]
        );

        $this->resetForm();
        $this->loadPages();
    }

    public function edit($id)
    {
        $page = StaticPage::findOrFail($id);
        $this->editId = $id;
        $this->title = $page->title;
        $this->slug = $page->slug;
        $this->content_txt = $page->content; // Stelle sicher, dass dies der richtige Feldname ist
        // $this->continent_text = $page->continent_text; // Diese Zeile scheint nicht notwendig

        $this->show_in_footer = $page->show_in_footer;
        $this->show_in_nav = $page->show_in_nav;
    }

    public function delete($id)
    {
        StaticPage::find($id)?->delete();
        $this->loadPages();
    }

    public function initEditor()
    {
        $this->dispatch('editor:refresh');
    }

    public function resetForm()
    {
        $this->editId = null;
        $this->title = '';
        $this->slug = '';
        $this->content = '';
        $this->show_in_footer = false;
        $this->show_in_nav = false;
    }

    public function render()
    {
        return view('livewire.backend.static-pages.static-page-manager')
        ->layout('backend.layouts.backend');

    }
}
