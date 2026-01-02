<?php

namespace App\Http\Livewire\Reference;

use Livewire\Component;

class ReferenceToolbar extends Component
{
    public string $modelClass = '';
    public ?string $configKey = null;
    public array $fields = [];
    public array $visibleFields = [];
    public array $filters = [];
    // Filter UI state
    public ?string $filterField = null;
    public string $filterOperator = 'contains';
    public $filterValue = null;
    public string $search = '';
    public int $perPage = 15;
    public bool $readOnly = false;
    // View state expected by the toolbar partial
    public string $view = 'rows';
    public array $selected = [];
    public string $sort = 'id';
    public string $direction = 'desc';

    protected $listeners = [
        // allow external components to set values if needed
        'setToolbarSearch' => 'setSearch',
        'setToolbarFilters' => 'setFilters',
        'setToolbarVisibleFields' => 'setVisibleFields',
        // also accept direct table-emitted events
        'setFilters' => 'setFilters',
        'setSearch' => 'setSearch',
        'setVisibleFields' => 'setVisibleFields',
        'selectedUpdated' => 'setSelected',
        // selection updates from parent table
        'setSelected' => 'setSelected',
    ];

    public function mount($modelClass = null, $configKey = null, $fields = [], $visibleFields = [], $readOnly = false)
    {
        $this->modelClass = $modelClass ?? '';
        $this->configKey = $configKey;
        $this->fields = $fields;
        $this->visibleFields = $visibleFields;
        $this->readOnly = (bool) $readOnly;
    }

    public function setSearch(string $val)
    {
        $this->search = $val;
    }

    public function setFilters(array $f)
    {
        $this->filters = $f;
    }

    public function setVisibleFields(array $v)
    {
        $this->visibleFields = $v;
    }

    public function setSelected(array $s)
    {
        $this->selected = $s ?? [];
    }

    public function updatedSearch()
    {
        // Emit upward to parent table when mounted inside it, and also emit globally
        try {
            $this->emitUp('searchUpdated', $this->search);
        } catch (\Throwable $e) {
            // ignore
        }

        try {
            $this->emit('searchUpdated', $this->search);
        } catch (\Throwable $e) {
            // noop
        }
    }

    /**
     * Called from the input's wire:input to reliably propagate the search
     * value to the parent table and any listeners.
     */
    public function inputSearch($val)
    {
        $this->search = (string) $val;

        try {
            $this->emitUp('searchUpdated', $this->search);
        } catch (\Throwable $e) {
            // ignore
        }

        try {
            $this->emit('searchUpdated', $this->search);
        } catch (\Throwable $e) {
            // noop
        }
    }

    public function updatedPerPage()
    {
        $this->emit('setPerPage', $this->perPage);
    }

    public function updatedView()
    {
        $this->emit('setView', $this->view);
    }

    public function setView(string $v)
    {
        $this->view = $v;
        $this->emit('setView', $this->view);
    }

    public function deleteSelected()
    {
        $this->emit('deleteSelected');
    }

    public function addFilter()
    {
        if (empty($this->filterField)) {
            return;
        }

        $noValueOps = ['is_null', 'is_not_null'];

        if (! in_array($this->filterOperator, $noValueOps, true) && $this->filterValue === null) {
            return;
        }

        $filter = [
            'field' => $this->filterField,
            'op' => $this->filterOperator,
            'value' => $this->filterValue,
        ];

        // clear the value but keep the field so user can add multiple filters
        $this->filterValue = null;

        $this->emit('filtersAdded', $filter);
    }

    public function removeFilter(int $index)
    {
        $this->emit('filtersRemoved', $index);
    }

    public function clearFilters()
    {
        $this->emit('filtersCleared');
    }

    public function toggleVisibleField(string $key)
    {
        $this->emit('toggleVisibleField', $key);
    }

    public function resetVisibleFields()
    {
        $this->emit('resetVisibleFields');
    }

    public function create()
    {
        $this->emit('createReference');
    }

    public function openArchive()
    {
        $this->emit('openReferenceArchive');
    }

    public function render()
    {
        // Compute export fields from currently visible columns so the partial
        // can build export links without inline logic.
        $exportFields = collect($this->fields ?? [])->filter(function ($f) {
            return ! empty($f['key']) && in_array($f['key'], $this->visibleFields ?? []);
        })->map(function ($f) {
            return ['key' => $f['key'] ?? null, 'label' => $f['label'] ?? ($f['key'] ?? '')];
        })->values()->toArray();

        return view('livewire.reference.reference-toolbar', [
            'modelClass' => $this->modelClass,
            'configKey' => $this->configKey,
            'fields' => $this->fields,
            'visibleFields' => $this->visibleFields,
            'filters' => $this->filters,
            'search' => $this->search,
            'perPage' => $this->perPage,
            'view' => $this->view,
            'selected' => $this->selected,
            'sort' => $this->sort,
            'direction' => $this->direction,
            'readOnly' => $this->readOnly,
            'exportFields' => $exportFields,
        ]);
    }
}
