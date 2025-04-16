<div class="space-y-8">

    {{-- Filter-Leiste --}}
    @include('livewire.frontend.partials.filter-leiste', ['parks' => $parks, 'laender' => $laender])

    {{-- Karten-Komponente --}}
    <livewire:frontend.park-map :parks="$parks" />
</div>
