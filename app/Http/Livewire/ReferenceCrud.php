<?php

namespace App\Http\Livewire;

use Illuminate\Pagination\Paginator;
use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\Traits\BuildsReferenceQuery;

class ReferenceCrud extends Component
{
    use WithPagination;
    use BuildsReferenceQuery;

    public string $modelClass;
    public array $fields = [];
    protected array $rawFields = [];
    public $configKey;
    public int $perPage = 15;
    public string $search = '';
    public string $sort = 'id';
    public string $direction = 'desc';
    public string $view = 'rows';
    public bool $readOnly = false;
    public $showId = null;

    protected $listeners = [
        'refreshList' => '$refresh',
        'savedReference' => '$refresh',
    ];

    public function mount($modelClass = null, $fields = [], $configKey = null)
    {
        $this->modelClass = $modelClass;
        $this->configKey = $configKey;
        if ($this->configKey) {
            $this->rawFields = config('reference-tables.' . $this->configKey) ?? [];
            $this->fields = app(\App\Services\ReferenceFieldSanitizer::class)->sanitize($this->rawFields);
        } else {
            $this->fields = $fields;
        }
        $this->resetPage();
    }

    public function sortBy($column, $dir = null)
    {
        if ($dir) {
            $this->sort = $column;
            $this->direction = $dir;
        } else {
            if ($this->sort === $column) {
                $this->direction = $this->direction === 'asc' ? 'desc' : 'asc';
            } else {
                $this->sort = $column;
                $this->direction = 'asc';
            }
        }

        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        Paginator::useBootstrap();

        $safeFields = $this->fields;

        return view('livewire.reference.referencecrud', [
            'items' => $this->buildQuery()->paginate($this->perPage),
            'modelClass' => $this->modelClass,
            'fields' => $safeFields,
            'configKey' => $this->configKey,
            'readOnly' => $this->readOnly,
        ]);
    }

    // Field sanitization delegated to `ReferenceFieldSanitizer` servic
}
