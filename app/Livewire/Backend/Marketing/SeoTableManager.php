<?php

namespace App\Livewire\Backend\Marketing;

use App\Models\ModSeoMeta;
use App\Models\StaticPage;
use App\Models\Park;
use App\Services\SeoService;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Cache;

class SeoTableManager extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $sortField = 'id';
    public $sortDirection = 'asc';
    public $deleteId = null;

    public function render()
    {
        $seoMetas = ModSeoMeta::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('model_type', 'like', '%' . $this->search . '%')
                      ->orWhere('model_id', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $seoMetas->getCollection()->transform(function ($seoMeta) {
            $seoMeta->model_name = $this->getModelName($seoMeta->model_type, $seoMeta->model_id);
            return $seoMeta;
        });

        return view('livewire.backend.marketing.seo-table-manager', [
            'seoMetas' => $seoMetas,
        ])->layout('backend.layouts.backend');
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

    public function edit($id)
    {
        return redirect()->route('seo.edit', ['id' => $id]);
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
    }

    public function delete()
    {
        if ($this->deleteId) {
            $seoMeta = ModSeoMeta::find($this->deleteId);
            if ($seoMeta) {
                $cacheKey = "seo_{$seoMeta->model_type}_{$seoMeta->model_id}";
                $seoMeta->delete();
                Cache::forget($cacheKey);
                session()->flash('message', 'SEO-Eintrag erfolgreich gelöscht.');
            }
            $this->deleteId = null;
        }
    }

    public function deleteAndReset($id)
    {
        $seoMeta = ModSeoMeta::find($id);

        if ($seoMeta) {
            $modelType = $seoMeta->model_type;
            $modelId = $seoMeta->model_id;

            $cacheKey = "seo_{$modelType}_{$modelId}";
            $seoMeta->delete();
            Cache::forget($cacheKey);

            $model = null;
            if ($modelType === Park::class) {
                $model = Park::find($modelId);
            } elseif ($modelType === StaticPage::class) {
                $model = StaticPage::find($modelId);
            } elseif ($modelType === 'startpage' && $modelId === 0) {
                $model = ['model_type' => 'startpage', 'model_id' => 0];
            }

            if ($model) {
                app(SeoService::class)->getSeoData($model);
                session()->flash('message', 'SEO-Eintrag wurde zurückgesetzt und neu generiert.');
            } else {
                session()->flash('message', 'SEO-Eintrag gelöscht. Modell nicht gefunden für automatische Neugenerierung.');
            }
        }
    }

    public function togglePreventOverride($id)
    {
        $seoMeta = ModSeoMeta::find($id);
        if ($seoMeta) {
            $seoMeta->prevent_override = !$seoMeta->prevent_override;
            $seoMeta->save();
            $cacheKey = "seo_{$seoMeta->model_type}_{$seoMeta->model_id}";
            Cache::forget($cacheKey);
            session()->flash('message', "Überschreiben für ID {$id} wurde " . ($seoMeta->prevent_override ? 'gesperrt' : 'erlaubt') . '.');
        }
    }

    public function updating($name, $value)
    {
        if ($name === 'search' || $name === 'perPage') {
            $this->resetPage();
        }
    }

    protected function getModelName($modelType, $modelId)
    {
        if ($modelType === Park::class) {
            $model = Park::find($modelId);
            return $model ? "Park: {$model->name} ({$modelId})" : "Park ({$modelId})";
        }

        if ($modelType === StaticPage::class) {
            $model = StaticPage::find($modelId);
            return $model ? "Seite: {$model->title} ({$modelId})" : "StaticPage ({$modelId})";
        }

        if ($modelType === 'startpage' && $modelId === 0) {
            return 'Startseite';
        }

        return "{$modelType} ({$modelId})";
    }
}
