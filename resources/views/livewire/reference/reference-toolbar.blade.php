<div class="flex flex-col gap-3 mb-3">
    <div class="w-full">
        @include('livewire.reference.partials.toolbar-search', [
            'search' => $search ?? '',
            'perPage' => $perPage ?? 15,
        ])
    </div>

    <div class="w-full">
        @include('livewire.reference.partials.toolbar-actions', [
            'modelClass' => $modelClass ?? null,
            'configKey' => $configKey ?? null,
            'fields' => $fields ?? [],
            'visibleFields' => $visibleFields ?? [],
            'filters' => $filters ?? [],
            'search' => $search ?? '',
            'perPage' => $perPage ?? 15,
            'view' => $view ?? 'rows',
            'selected' => $selected ?? [],
            'sort' => $sort ?? 'id',
            'direction' => $direction ?? 'desc',
            'readOnly' => $readOnly ?? false,
        ])
    </div>
</div>