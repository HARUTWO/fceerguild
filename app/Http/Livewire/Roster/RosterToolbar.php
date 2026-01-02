<?php

namespace App\Http\Livewire\Roster;

use Livewire\Component;

class RosterToolbar extends Component
{
    public string $type = 'volunteers';
    public string $headerIcon = 'users';
    public string $headerTitle = 'Volunteers';
    public array $availableColumns = [];
    public array $visibleColumns = [];
    public array $availableFilters = [];
    public array $activeFilters = [];
    public array $selected = [];
    public array $selectedRowNumbers = [];
    public string $search = '';
    public int $perPage = 15;
    public string $view = 'table';
    
    // Filter builder
    public string $filterField = '';
    public string $filterOperator = 'equals';
    public string $filterValue = '';

    protected $listeners = [
        'setToolbarSearch' => 'setSearch',
        'setToolbarFilters' => 'setFilters',
        'setToolbarVisibleColumns' => 'setVisibleColumns',
        'setSelected' => 'setSelected',
    ];

    public function mount(
        string $type = 'volunteers',
        string $headerIcon = 'users',
        string $headerTitle = 'Volunteers',
        array $availableColumns = [],
        array $visibleColumns = [],
        array $availableFilters = [],
        array $activeFilters = [],
        array $selected = [],
        array $selectedRowNumbers = [],
        string $search = '',
        int $perPage = 15,
        string $view = 'table'
    ) {
        $this->type = $type;
        $this->headerIcon = $headerIcon;
        $this->headerTitle = $headerTitle;
        $this->availableColumns = $availableColumns;
        $this->visibleColumns = $visibleColumns;
        $this->availableFilters = $availableFilters;
        $this->activeFilters = $activeFilters;
        $this->selected = $selected;
        $this->selectedRowNumbers = $selectedRowNumbers;
        $this->search = $search;
        $this->perPage = $perPage;
        $this->view = $view;
    }

    public function setSearch(string $val)
    {
        $this->search = $val;
    }

    public function setFilters(array $f)
    {
        $this->activeFilters = $f;
    }

    public function setVisibleColumns(array $v)
    {
        $this->visibleColumns = $v;
    }

    public function setSelected(array $s)
    {
        $this->selected = $s ?? [];
    }

    public function updatedSearch()
    {
        $this->dispatch('searchUpdated', search: $this->search)->to(RosterTable::class);
    }

    public function updatedPerPage()
    {
        $this->dispatch('perPageUpdated', perPage: $this->perPage)->to(RosterTable::class);
    }

    public function setView(string $view)
    {
        $this->view = $view;
        $this->dispatch('viewUpdated', view: $this->view)->to(RosterTable::class);
    }

    public function addFilter()
    {
        if (empty($this->filterField)) {
            return;
        }

        // Build filter data with operator
        $filterData = [
            'value' => $this->filterValue,
            'operator' => $this->filterOperator,
        ];
        
        // For null operators, value is not needed
        if (in_array($this->filterOperator, ['is_null', 'is_not_null'])) {
            $filterData['value'] = null;
        }
        
        // Update local state immediately
        $this->activeFilters[$this->filterField] = $filterData;

        $this->dispatch('filterAdded', key: $this->filterField, value: $filterData)->to(RosterTable::class);
        
        // Reset filter builder
        $this->filterField = '';
        $this->filterOperator = 'equals';
        $this->filterValue = '';
    }

    public function removeFilter(string $filterKey)
    {
        // Update local state
        unset($this->activeFilters[$filterKey]);
        
        $this->dispatch('filterRemoved', key: $filterKey)->to(RosterTable::class);
    }

    public function clearFilters()
    {
        // Clear local state
        $this->activeFilters = [];
        
        $this->dispatch('filtersCleared')->to(RosterTable::class);
    }

    public function toggleColumn(string $column)
    {
        // Update local state immediately for UI feedback
        if (in_array($column, $this->visibleColumns)) {
            $this->visibleColumns = array_values(array_diff($this->visibleColumns, [$column]));
        } else {
            $this->visibleColumns[] = $column;
        }
        
        $this->dispatch('columnToggled', column: $column)->to(RosterTable::class);
    }

    public function resetColumns()
    {
        // Reset to default columns
        $defaultColumns = config('roster.default_visible_columns', []);
        
        if ($this->type === 'students') {
            $defaultColumns = array_map(function ($col) {
                return $col === 'volunteer_number' ? 'student_number' : $col;
            }, $defaultColumns);
        }
        
        $this->visibleColumns = $defaultColumns;
        $this->dispatch('columnsReset')->to(RosterTable::class);
    }

    public function exportCsv()
    {
        $this->dispatch('exportCsv')->to(RosterTable::class);
    }

    public function exportXlsx()
    {
        $this->dispatch('exportXlsx')->to(RosterTable::class);
    }

    public function openArchive()
    {
        $this->dispatch('openArchive')->to(RosterTable::class);
    }

    public function openCreateForm()
    {
        $this->dispatch('openRosterUserForm', type: $this->type);
    }

    public function render()
    {
        return view('livewire.roster.roster-toolbar', [
            'type' => $this->type,
            'availableColumns' => $this->availableColumns,
            'visibleColumns' => $this->visibleColumns,
            'availableFilters' => $this->availableFilters,
            'activeFilters' => $this->activeFilters,
            'selected' => $this->selected,
            'search' => $this->search,
            'perPage' => $this->perPage,
            'view' => $this->view,
        ]);
    }
}
