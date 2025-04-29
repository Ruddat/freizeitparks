<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">
                <a href="{{ route('admin.blogposts') }}">Blogposts</a>
            </li>
            <li class="breadcrumb-item active">
                {{ $post ? 'Bearbeiten' : 'Erstellen' }}
            </li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        {{ $post ? '✏️ Blogpost bearbeiten' : '📝 Neuen Blogpost erstellen' }}
                    </h4>
                </div>

                <div class="card-body">
                    <form wire:submit.prevent="save" class="space-y-4">

                        <!-- Titel -->
                        <div class="form-group">
                            <label>Titel *</label>
                            <input type="text" wire:model.defer="title" class="form-control">
                            @error('title') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Slug -->
                        <div class="form-group">
                            <label>Slug (optional)</label>
                            <input type="text" wire:model.defer="slug" class="form-control">
                        </div>

                        <!-- Excerpt -->
                        <div class="form-group">
                            <label>Kurzbeschreibung (Excerpt)</label>
                            <textarea wire:model.defer="excerpt" class="form-control" rows="3"></textarea>
                        </div>

                        <!-- Content -->
                        <div class="form-group">
                            <label>Inhalt *</label>
                            <textarea wire:model.defer="content" class="form-control" rows="10"></textarea>
                            @error('content') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Featured Image -->
                        <div class="form-group">
                            <label>Featured Image (URL oder Pfad)</label>
                            <input type="text" wire:model.defer="featured_image" class="form-control">
                        </div>

                        <!-- Kategorie -->
                        <div class="form-group">
                            <label>Kategorie *</label>
                            <select wire:model.defer="category_id" class="form-control">
                                <option value="">-- Kategorie wählen --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Tags -->
                        <div class="form-group">
                            <label>Tags</label>
                            <div class="row">
                                @foreach($allTags as $tag)
                                <div class="col-md-4">
                                    <label class="d-flex align-items-center">
                                        <input type="checkbox" wire:model.defer="tags" value="{{ $tag->id }}">
                                        {{ $tag->name }}
                                    </label>
                                </div>
                            @endforeach
                            </div>
                        </div>

                        <!-- Coupon Code -->
                        <div class="form-group">
                            <label>Coupon Code (optional)</label>
                            <input type="text" wire:model.defer="coupon_code" class="form-control">
                        </div>

                        <!-- Coupon Gültigkeit -->
                        <div class="form-group">
                            <label>Gültig bis (Coupon)</label>
                            <input type="date" wire:model.defer="valid_until" class="form-control">
                        </div>

                        <!-- Veröffentlichungsstart -->
                        <div class="form-group">
                            <label>Veröffentlichen ab</label>
                            <input type="datetime-local" wire:model.defer="publish_start" class="form-control">
                        </div>

                        <!-- Veröffentlichungsende -->
                        <div class="form-group">
                            <label>Veröffentlichen bis</label>
                            <input type="datetime-local" wire:model.defer="publish_end" class="form-control">
                        </div>

                        <!-- SEO Titel -->
                        <div class="form-group">
                            <label>SEO Titel</label>
                            <input type="text" wire:model.defer="seo_title" class="form-control">
                        </div>

                        <!-- SEO Beschreibung -->
                        <div class="form-group">
                            <label>SEO Beschreibung</label>
                            <textarea wire:model.defer="seo_description" class="form-control" rows="3"></textarea>
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <label>Status *</label>
                            <select wire:model.defer="status" class="form-control">
                                <option value="draft">Entwurf</option>
                                <option value="published">Veröffentlicht</option>
                                <option value="scheduled">Geplant</option>
                            </select>
                        </div>

                        <!-- Button -->
                        <div class="form-group text-end">
                            <button type="submit" class="btn btn-success">
                                💾 Speichern
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
