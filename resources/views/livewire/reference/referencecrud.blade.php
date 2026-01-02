<div class="reference-crud">
    @livewire(\App\Http\Livewire\Reference\ReferenceTable::class, ['modelClass' => $modelClass, 'configKey' => $configKey, 'view' => $view ?? 'rows', 'readOnly' => $readOnly ?? false, 'perPage' => $perPage ?? 15], key('reference-table-'.($configKey ?? uniqid())))

    @livewire(\App\Http\Livewire\Reference\Modal\ReferenceFormModal::class, ['modelClass' => $modelClass, 'configKey' => $configKey], key('reference-form-'.($configKey ?? uniqid())))

    @livewire(\App\Http\Livewire\Reference\Modal\ReferenceConfirmChangesModal::class, ['modelClass' => $modelClass], key('reference-confirm-'.($configKey ?? uniqid())))

    @livewire(\App\Http\Livewire\Reference\Modal\ReferenceDetailsModal::class, ['modelClass' => $modelClass, 'configKey' => $configKey], key('reference-details-'.($configKey ?? uniqid())))

    @livewire(\App\Http\Livewire\Reference\Modal\ReferenceDeleteModal::class, ['modelClass' => $modelClass], key('reference-delete-'.($configKey ?? uniqid())))
    @livewire(\App\Http\Livewire\Reference\Modal\ReferenceArchiveModal::class, ['modelClass' => $modelClass], key('reference-archive-'.($configKey ?? uniqid())))
    @livewire(\App\Http\Livewire\Reference\Modal\ReferenceRestoreModal::class, ['modelClass' => $modelClass], key('reference-restore-'.($configKey ?? uniqid())))
    @livewire(\App\Http\Livewire\Reference\Modal\ReferenceForceDeleteModal::class, ['modelClass' => $modelClass], key('reference-force-delete-'.($configKey ?? uniqid())))
</div>
