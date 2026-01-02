<?php

namespace App\Http\Livewire\Roster;

use App\Exports\RosterExport;
use App\Models\FceerBatch;
use App\Models\StudentGroup;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RosterTable extends Component
{
    use WithPagination;

    public string $type = 'volunteers'; // 'volunteers' or 'students'
    public int $perPage = 15;
    public string $search = '';
    public string $sort = 'id';
    public string $direction = 'asc';
    public string $view = 'table'; // 'table' or 'gallery'
    public array $visibleColumns = [];
    public array $activeFilters = [];
    public array $selected = [];
    public bool $selectAll = false;
    
    // Filter builder properties
    public string $filterField = '';
    public string $filterOperator = 'equals';
    public string $filterValue = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'sort' => ['except' => 'id'],
        'direction' => ['except' => 'asc'],
    ];

    protected $listeners = [
        'refreshRoster' => '$refresh',
        'refreshRosterTable' => '$refresh',
        'searchUpdated' => 'handleSearchUpdated',
        'perPageUpdated' => 'handlePerPageUpdated',
        'viewUpdated' => 'handleViewUpdated',
        'filterAdded' => 'handleFilterAdded',
        'filterRemoved' => 'handleFilterRemoved',
        'filtersCleared' => 'handleFiltersCleared',
        'columnToggled' => 'handleColumnToggled',
        'columnsReset' => 'resetColumns',
        'openArchive' => 'handleOpenArchive',
        'exportCsv' => 'exportCsv',
        'exportXlsx' => 'exportXlsx',
    ];

    public function mount(string $type = 'volunteers')
    {
        abort_unless(Gate::allows('viewRoster'), 403);

        $this->type = $type;
        $this->resetColumns();
    }

    public function resetColumns(): void
    {
        $defaultColumns = config('roster.default_visible_columns', []);
        
        if ($this->type === 'students') {
            $defaultColumns = array_map(function ($col) {
                return $col === 'volunteer_number' ? 'student_number' : $col;
            }, $defaultColumns);
        }

        $this->visibleColumns = $defaultColumns;
    }

    public function toggleColumn(string $column): void
    {
        if (in_array($column, $this->visibleColumns)) {
            $this->visibleColumns = array_values(array_diff($this->visibleColumns, [$column]));
        } else {
            $this->visibleColumns[] = $column;
        }
    }

    public function setPerPage(int $perPage): void
    {
        $this->perPage = $perPage;
        $this->resetPage();
    }

    public function setView(string $view): void
    {
        $this->view = $view;
    }

    public function sortBy(string $column): void
    {
        $columnConfig = config("roster.columns.{$column}", []);
        if (!($columnConfig['sortable'] ?? false)) {
            return;
        }

        if ($this->sort === $column) {
            $this->direction = $this->direction === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sort = $column;
            $this->direction = 'asc';
        }

        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function handleSearchUpdated(string $search): void
    {
        $this->search = $search;
        $this->resetPage();
    }

    public function handlePerPageUpdated(int $perPage): void
    {
        $this->perPage = $perPage;
        $this->resetPage();
    }

    public function handleViewUpdated(string $view): void
    {
        $this->view = $view;
    }

    public function handleFilterAdded(string $key, $value): void
    {
        if (is_array($value)) {
            $filterValue = $value['value'] ?? null;
            $operator = $value['operator'] ?? 'equals';
            
            if (in_array($operator, ['is_null', 'is_not_null'])) {
                $this->activeFilters[$key] = $value;
            } elseif ($filterValue === '' || $filterValue === null) {
                unset($this->activeFilters[$key]);
            } else {
                $this->activeFilters[$key] = $value;
            }
        } else {
            if ($value === '' || $value === null) {
                unset($this->activeFilters[$key]);
            } else {
                $this->activeFilters[$key] = $value;
            }
        }
        $this->resetPage();
    }

    public function handleFilterRemoved(string $key): void
    {
        unset($this->activeFilters[$key]);
        $this->resetPage();
    }

    public function handleFiltersCleared(): void
    {
        $this->activeFilters = [];
        $this->resetPage();
    }

    public function handleColumnToggled(string $column): void
    {
        if (in_array($column, $this->visibleColumns)) {
            $this->visibleColumns = array_values(array_diff($this->visibleColumns, [$column]));
        } else {
            $this->visibleColumns[] = $column;
        }
    }

    public function handleOpenArchive(): void
    {
        $this->dispatch('open-archive-modal', type: $this->type);
    }

    public function toggleRow(string $id): void
    {
        if (in_array($id, $this->selected)) {
            $this->selected = array_values(array_diff($this->selected, [$id]));
        } else {
            $this->selected[] = $id;
        }
    }

    public function updatedSelectAll(): void
    {
        if ($this->selectAll) {
            // Select all on current page
            $this->selected = $this->getQuery()
                ->paginate($this->perPage)
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function getAvailableFilters(): array
    {
        $result = [];
        
        $configFilters = config('roster.filters', []);
        foreach ($configFilters as $key => $filter) {
            $appliesTo = $filter['applies_to'] ?? null;
            if ($appliesTo && $appliesTo !== $this->type) {
                continue;
            }

            $filterData = $filter;
            $filterData['key'] = $key;

            if ($filter['type'] === 'select') {
                if (isset($filter['options'])) {
                    $filterData['options'] = $filter['options'];
                } elseif (isset($filter['options_source'])) {
                    $filterData['options'] = config("roster.{$filter['options_source']}", []);
                } elseif (isset($filter['model'])) {
                    $model = $filter['model'];
                    $display = $filter['display'] ?? 'name';
                    $filterData['options'] = $model::orderBy($display)->pluck($display, 'id')->toArray();
                }
            }

            $result[$key] = $filterData;
        }
        
        $columns = config('roster.columns', []);
        foreach ($columns as $key => $col) {
            if (isset($result[$key])) {
                continue;
            }
            
            if (isset($col['applies_to']) && $col['applies_to'] !== $this->type) {
                continue;
            }
            if (isset($col['list_relation'])) {
                $filterData = [
                    'key' => $key,
                    'label' => $col['label'] ?? $key,
                    'type' => 'list_relation',
                    'list_relation' => $col['list_relation'],
                    'list_format' => $col['list_format'] ?? 'default',
                ];
                $result[$key] = $filterData;
                continue;
            }
            if (isset($col['count_relation'])) {
                continue;
            }
            
            if (in_array($key, ['row_number', 'full_name', 'address', 'deleted_at', 'deleted_by'])) {
                continue;
            }
            
            $filterData = [
                'key' => $key,
                'label' => $col['label'] ?? $key,
                'type' => 'text',
            ];
            
            if (isset($col['relation'])) {
                $filterData['relation'] = $col['relation'];
                $filterData['type'] = 'text';
            }
            
            if (isset($col['accessor'])) {
                $filterData['accessor'] = $col['accessor'];
                $filterData['type'] = 'text';
            }
            
            if (($col['format'] ?? null) === 'boolean') {
                $filterData['type'] = 'boolean';
                $filterData['true_label'] = 'Yes';
                $filterData['false_label'] = 'No';
            }
            
            $result[$key] = $filterData;
        }

        return $result;
    }

    protected function getFilterConfig(string $filterKey): array
    {
        $configFilter = config("roster.filters.{$filterKey}", []);
        if (!empty($configFilter)) {
            return $configFilter;
        }
        
        $column = config("roster.columns.{$filterKey}", []);
        if (!empty($column)) {
            $filterData = [
                'key' => $filterKey,
                'label' => $column['label'] ?? $filterKey,
                'type' => 'text',
            ];
            if (isset($column['list_relation'])) {
                $filterData['type'] = 'list_relation';
                $filterData['list_relation'] = $column['list_relation'];
                $filterData['list_format'] = $column['list_format'] ?? 'default';
            } elseif (isset($column['relation'])) {
                $filterData['relation'] = $column['relation'];
            }
            
            if (isset($column['accessor'])) {
                $filterData['accessor'] = $column['accessor'];
            }
            if (($column['format'] ?? null) === 'boolean') {
                $filterData['type'] = 'boolean';
            }
            
            return $filterData;
        }
        
        return [];
    }

    public function exportCsv(): BinaryFileResponse
    {
        $filename = $this->type . '_roster_' . now()->format('Y-m-d_His') . '.csv';
        $columns = config('roster.columns', []);

        return Excel::download(
            new RosterExport(
                $this->getQuery(),
                $columns,
                $this->visibleColumns,
                $this->type,
                $this,
                [
                    'exported_at' => now()->format('Y-m-d H:i:s'),
                    'exported_by' => Auth::user()?->name,
                ]
            ),
            $filename,
            \Maatwebsite\Excel\Excel::CSV
        );
    }

    public function exportXlsx(): BinaryFileResponse
    {
        $filename = $this->type . '_roster_' . now()->format('Y-m-d_His') . '.xlsx';
        $columns = config('roster.columns', []);

        return Excel::download(
            new RosterExport(
                $this->getQuery(),
                $columns,
                $this->visibleColumns,
                $this->type,
                $this,
                [
                    'exported_at' => now()->format('Y-m-d H:i:s'),
                    'exported_by' => Auth::user()?->name,
                ]
            ),
            $filename,
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    protected function getQuery()
    {
        $roles = $this->type === 'students'
            ? config('roster.student_roles', ['Student'])
            : config('roster.volunteer_roles', []);

        $eagerLoad = config('roster.eager_load', []);

        $query = User::query()
            ->with($eagerLoad);

        $query->whereHas('role', function ($q) use ($roles) {
            $q->whereIn('name', $roles);
        });

        // Apply active filters
        foreach ($this->activeFilters as $filterKey => $filterData) {
            // Support both simple value and object with value/operator
            $filterValue = is_array($filterData) ? ($filterData['value'] ?? $filterData) : $filterData;
            $operator = is_array($filterData) ? ($filterData['operator'] ?? 'equals') : 'equals';
            
            $filterConfig = $this->getFilterConfig($filterKey);
            
            if (empty($filterConfig)) {
                continue;
            }
            
            if ($operator === 'is_null') {
                if (isset($filterConfig['relation'])) {
                    $parts = explode('.', $filterConfig['relation']);
                    if (count($parts) === 2) {
                        $query->whereDoesntHave($parts[0]);
                    }
                } elseif (isset($filterConfig['accessor'])) {
                    $query->whereNull($filterConfig['accessor']);
                } else {
                    $query->whereNull($filterKey);
                }
                continue;
            }
            
            if ($operator === 'is_not_null') {
                if (isset($filterConfig['relation'])) {
                    $parts = explode('.', $filterConfig['relation']);
                    if (count($parts) === 2) {
                        $query->whereHas($parts[0]);
                    }
                } elseif (isset($filterConfig['accessor'])) {
                    $query->whereNotNull($filterConfig['accessor']);
                } else {
                    $query->whereNotNull($filterKey);
                }
                continue;
            }
            
            if ($filterConfig['type'] === 'select') {
                if (isset($filterConfig['relation'])) {
                    $parts = explode('.', $filterConfig['relation']);
                    if (count($parts) === 2) {
                        $query->whereHas($parts[0], function ($q) use ($parts, $filterValue, $operator) {
                            if ($operator === 'contains') {
                                $q->where($parts[1], 'like', '%' . $filterValue . '%');
                            } else {
                                $q->where($parts[1], $filterValue);
                            }
                        });
                    }
                } elseif ($filterKey === 'role') {
                    $query->whereHas('role', function ($q) use ($filterValue, $operator) {
                        if ($operator === 'contains') {
                            $q->where('name', 'like', '%' . $filterValue . '%');
                        } else {
                            $q->where('name', $filterValue);
                        }
                    });
                }
            } elseif ($filterConfig['type'] === 'boolean') {
                $accessor = $filterConfig['accessor'] ?? $filterKey;
                if ($filterValue === '1' || $filterValue === 'true' || $filterValue === true) {
                    $query->whereNotNull($accessor);
                } else {
                    $query->whereNull($accessor);
                }
            } elseif ($filterConfig['type'] === 'text') {
                // Handle text filters
                if (isset($filterConfig['relation'])) {
                    $parts = explode('.', $filterConfig['relation']);
                    if (count($parts) >= 2) {
                        $relation = $parts[0];
                        $field = implode('.', array_slice($parts, 1));
                        $query->whereHas($relation, function ($q) use ($field, $filterValue, $operator) {
                            // Handle nested relations
                            $fieldParts = explode('.', $field);
                            if (count($fieldParts) > 1) {
                                $nestedRel = $fieldParts[0];
                                $nestedField = $fieldParts[1];
                                $q->whereHas($nestedRel, function ($nq) use ($nestedField, $filterValue, $operator) {
                                    if ($operator === 'contains') {
                                        $nq->where($nestedField, 'like', '%' . $filterValue . '%');
                                    } else {
                                        $nq->where($nestedField, $filterValue);
                                    }
                                });
                            } else {
                                if ($operator === 'contains') {
                                    $q->where($field, 'like', '%' . $filterValue . '%');
                                } else {
                                    $q->where($field, $filterValue);
                                }
                            }
                        });
                    }
                } elseif (isset($filterConfig['accessor'])) {
                    if ($operator === 'contains') {
                        $query->where($filterConfig['accessor'], 'like', '%' . $filterValue . '%');
                    } else {
                        $query->where($filterConfig['accessor'], $filterValue);
                    }
                } else {
                    if ($operator === 'contains') {
                        $query->where($filterKey, 'like', '%' . $filterValue . '%');
                    } else {
                        $query->where($filterKey, $filterValue);
                    }
                }
            } elseif ($filterConfig['type'] === 'list_relation') {
                $relationName = $filterConfig['list_relation'];
                $listFormat = $filterConfig['list_format'] ?? 'default';
                
                $searchRelation = match($listFormat) {
                    'committee-position' => 'committee',
                    'subject-proficiency' => 'volunteerSubject',
                    'classroom-position' => 'classroom',
                    'subject-grade' => 'subject',
                    'field-abbreviation' => 'fieldOfWork',
                    'degree-program' => 'degreeProgram',
                    'highschool' => 'highschool',
                    default => null,
                };
                
                if ($searchRelation) {
                    $query->whereHas($relationName, function ($q) use ($searchRelation, $filterValue, $operator) {
                        $q->whereHas($searchRelation, function ($rq) use ($filterValue, $operator) {
                            if ($operator === 'contains') {
                                $rq->where('name', 'like', '%' . $filterValue . '%');
                            } else {
                                $rq->where('name', $filterValue);
                            }
                        });
                    });
                } else {
                    $query->whereHas($relationName);
                }
            }
        }

        if ($this->search) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm)
                  ->orWhereHas('profile', function ($pq) use ($searchTerm) {
                      $pq->where('first_name', 'like', $searchTerm)
                         ->orWhere('middle_name', 'like', $searchTerm)
                         ->orWhere('lived_name', 'like', $searchTerm)
                         ->orWhere('phone_number', 'like', $searchTerm);
                  })
                  ->orWhereHas('fceerProfile', function ($fq) use ($searchTerm) {
                      $fq->where('volunteer_number', 'like', $searchTerm)
                         ->orWhere('student_number', 'like', $searchTerm)
                         ->orWhere('fceer_id', 'like', $searchTerm);
                  });
            });
        }

        $sortColumn = $this->sort;
        $columnConfig = config("roster.columns.{$sortColumn}", []);

        if ($sortColumn === 'full_name') {
            $query->orderBy('name', $this->direction);
        } elseif ($sortColumn === 'deleted_at') {
            $query->orderBy('deleted_at', $this->direction);
        } elseif ($sortColumn === 'deleted_by') {
            $query->leftJoin('users as deleter', 'users.deleted_by', '=', 'deleter.id')
                  ->orderBy('deleter.name', $this->direction)
                  ->select('users.*');
        } elseif (isset($columnConfig['relation'])) {
            $parts = explode('.', $columnConfig['relation']);
            if ($parts[0] === 'profile') {
                $query->leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id')
                      ->orderBy("user_profiles.{$parts[1]}", $this->direction)
                      ->select('users.*');
            } elseif ($parts[0] === 'role') {
                $query->leftJoin('user_roles', 'users.role_id', '=', 'user_roles.id')
                      ->orderBy("user_roles.{$parts[1]}", $this->direction)
                      ->select('users.*');
            } elseif ($parts[0] === 'fceerProfile') {
                $query->leftJoin('fceer_profiles', 'users.id', '=', 'fceer_profiles.user_id')
                      ->orderBy("fceer_profiles.{$parts[1]}", $this->direction)
                      ->select('users.*');
            } else {
                $query->orderBy($sortColumn, $this->direction);
            }
        } elseif (isset($columnConfig['accessor'])) {
            $query->orderBy($columnConfig['accessor'], $this->direction);
        } else {
            $query->orderBy($sortColumn, $this->direction);
        }

        return $query;
    }

    public function getColumnValue(User $user, string $column): mixed
    {
        $columnConfig = config("roster.columns.{$column}", []);

        if ($column === 'row_number') {
            return null; // Computed in view
        }

        if ($column === 'full_name') {
            $profile = $user->profile;
            if ($profile) {
                $parts = array_filter([
                    $profile->first_name,
                    $profile->middle_name,
                    $profile->suffix_name,
                ]);
                return implode(' ', $parts) ?: $user->name;
            }
            return $user->name;
        }

        if ($column === 'deleted_at') {
            return $user->deleted_at?->format('M d, Y H:i');
        }

        if ($column === 'deleted_by') {
            return $user->deletedBy?->name ?? '—';
        }

        if ($column === 'address') {
            $address = $user->profile?->address;
            if (!$address) {
                return null;
            }
            $parts = array_filter([
                $address->house_number,
                $address->block_number ? "Block {$address->block_number}" : null,
                $address->street,
                $address->barangay?->name,
                $address->city?->name,
                $address->province?->name,
            ]);
            return implode(', ', $parts) ?: null;
        }

        if ($column === 'fceer_status') {
            $status = $user->fceerProfile?->status;
            return $status === 1 ? 'Active' : ($status === 0 ? 'Inactive' : '—');
        }

        if (isset($columnConfig['list_relation'])) {
            $relationName = $columnConfig['list_relation'];
            $format = $columnConfig['list_format'] ?? 'default';
            $items = $user->{$relationName};
            
            if ($items->isEmpty()) {
                return '—';
            }
            
            return $items->map(function ($item) use ($format) {
                return match($format) {
                    'committee-position' => ($item->committee?->name && $item->committeePosition?->name) 
                        ? $item->committee->name . ' - ' . $item->committeePosition->name : null,
                    'subject-proficiency' => ($item->volunteerSubject?->name && $item->subject_proficiency) 
                        ? $item->volunteerSubject->name . ' - ' . $item->subject_proficiency : null,
                    'classroom-position' => ($item->classroom?->name && $item->classroomPosition?->name) 
                        ? $item->classroom->name . ' - ' . $item->classroomPosition->name : null,
                    'subject-grade' => ($item->subject?->name && $item->grade) 
                        ? $item->subject->name . ' - ' . $item->grade : null,
                    'field-abbreviation' => $this->formatProfessionalCredential($item),
                    'degree-program' => ($item->degreeProgram?->name && $item->university?->name)
                        ? $item->degreeProgram->name . ' (' . $item->university->name . ')'
                        : ($item->degreeProgram?->name ?? null),
                    'highschool' => $this->formatHighschoolRecord($item),
                    default => (string) $item,
                };
            })->filter()->implode('<hr class="my-1 border-gray-300 dark:border-gray-600">') ?: '—';
        }

        if (isset($columnConfig['count_relation'])) {
            $relationName = $columnConfig['count_relation'];
            $countAttribute = str($relationName)->snake()->append('_count')->toString();
            return $user->{$countAttribute} ?? 0;
        }

        if (isset($columnConfig['relation'])) {
            $value = $this->getNestedValue($user, $columnConfig['relation']);
            return $this->formatValue($value, $columnConfig);
        }

        if (isset($columnConfig['accessor'])) {
            $value = $user->{$columnConfig['accessor']};
            return $this->formatValue($value, $columnConfig);
        }

        $value = $user->{$column} ?? null;
        return $this->formatValue($value, $columnConfig);
    }

    protected function getNestedValue($object, string $path): mixed
    {
        $parts = explode('.', $path);
        $value = $object;

        foreach ($parts as $part) {
            if ($value === null) {
                return null;
            }
            $value = $value->{$part} ?? null;
        }

        return $value;
    }

    protected function formatProfessionalCredential($item): ?string
    {
        $fieldName = $item->fieldOfWork?->name;
        if (!$fieldName) {
            return null;
        }
        
        $title = $item->suffix ?? $item->prefix;
        if ($title && $title->name && $title->abbreviation) {
            return "{$fieldName} - {$title->name} ({$title->abbreviation})";
        } elseif ($title && $title->name) {
            return "{$fieldName} - {$title->name}";
        }
        
        return $fieldName;
    }

    protected function formatHighschoolRecord($item): ?string
    {
        $name = $item->highschool?->name;
        if (!$name) {
            return null;
        }
        
        $levelAbbr = match(strtolower($item->level ?? '')) {
            'junior highschool', 'junior' => 'JHS',
            'senior highschool', 'senior' => 'SHS',
            default => $item->level,
        };
        
        if ($levelAbbr) {
            return "{$name} ({$levelAbbr})";
        }
        
        return $name;
    }

    protected function formatValue($value, array $config): mixed
    {
        if ($value === null) {
            return null;
        }

        $format = $config['format'] ?? null;

        if ($format === 'date' && $value) {
            return $value instanceof \Carbon\Carbon 
                ? $value->format('M d, Y')
                : $value;
        }

        if ($format === 'boolean') {
            return (bool) $value;
        }

        return $value;
    }

    public function getAvailableColumns(): array
    {
        $columns = config('roster.columns', []);
        $grouped = [];

        foreach ($columns as $key => $col) {
            if (isset($col['applies_to']) && $col['applies_to'] !== $this->type) {
                continue;
            }
            
            if ($this->type === 'students' && $key === 'volunteer_number') {
                continue;
            }
            if ($this->type === 'volunteers' && $key === 'student_number') {
                continue;
            }
            if ($this->type === 'volunteers' && $key === 'student_group') {
                continue;
            }

            $section = $col['section'] ?? 'Other';
            if (!isset($grouped[$section])) {
                $grouped[$section] = [];
            }
            $grouped[$section][$key] = $col;
        }

        return $grouped;
    }

    public function render()
    {
        $users = $this->getQuery()->paginate($this->perPage);
        $columns = config('roster.columns', []);
        $availableColumns = $this->getAvailableColumns();
        $availableFilters = $this->getAvailableFilters();

        return view('livewire.roster.roster-table', [
            'users' => $users,
            'columns' => $columns,
            'availableColumns' => $availableColumns,
            'visibleColumns' => $this->visibleColumns,
            'availableFilters' => $availableFilters,
        ]);
    }
}
