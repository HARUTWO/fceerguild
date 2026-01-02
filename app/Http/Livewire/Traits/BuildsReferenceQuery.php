<?php

namespace App\Http\Livewire\Traits;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

/**
 * Build a query for reference tables with optional search and filters.
 *
 * Consuming component must provide:
 * - public string $modelClass
 * - public array $fields (each field an array with at least 'key')
 * - public string $search
 * - public array $filters (optional)
 * - public string $sort
 * - public string $direction
 */
trait BuildsReferenceQuery
{
    /**
     * Build and return an Eloquent query builder.
     *
     * @return Builder
     */
    protected function buildQuery(array $fields = null): Builder
    {
        $modelClass = $this->modelClass;
        $query = ($modelClass)::query();
        $model = new $modelClass;
        $table = $model->getTable();

        $fields = $fields ?? ($this->fields ?? []);

        // Eager-load relations referenced by fields that end with _id
        $relations = [];
        foreach ($fields as $f) {
            $key = $f['key'] ?? null;
            if (! is_string($key)) {
                continue;
            }

            if (str_ends_with($key, '_id')) {
                $base = substr($key, 0, -3);
                $camel = Str::camel($base);

                if (method_exists($model, $base)) {
                    $relations[] = $base;
                } elseif (method_exists($model, $camel)) {
                    $relations[] = $camel;
                }
            }
        }

        $relations = array_values(array_unique($relations));
        if (! empty($relations)) {
            $query->with($relations);
        }

        $allowedKeys = collect($fields)->pluck('key')->filter()->values()->toArray();

        $qualifiedMap = [];
        foreach ($allowedKeys as $k) {
            if (! is_string($k)) {
                continue;
            }
            $qualifiedMap[$k] = "{$table}.{$k}";
        }

        $search = trim($this->search ?? '');
        if ($search !== '') {
            $lower = mb_strtolower($search);
            $originalSearch = $search;

            $query->where(function ($q) use ($lower, $query, $fields, $qualifiedMap, $originalSearch) {
                foreach ($fields as $f) {
                    $type = $f['type'] ?? 'text';
                    $searchable = $f['searchable'] ?? in_array($type, ['text', 'string']);
                    if (! $searchable) {
                        continue;
                    }

                    $col = $f['key'] ?? null;
                    if (! is_string($col) || ! isset($qualifiedMap[$col])) {
                        continue;
                    }

                    try {
                        $qualified = $query->qualifyColumn($col);
                        $q->orWhereRaw("LOWER(COALESCE({$qualified},'')) LIKE ?", ["%{$lower}%"]);
                    } catch (\Throwable $e) {
                        \Log::debug('BuildsReferenceQuery: raw search failed, falling back', ['column' => $col, 'error' => $e->getMessage()]);
                        $q->orWhere($col, 'like', "%{$originalSearch}%");
                    }
                }
            });
        }

        if (! empty($this->filters ?? [])) {
            foreach ($this->filters as $flt) {
                $field = $flt['field'] ?? null;
                $op = $flt['op'] ?? 'contains';
                $value = $flt['value'] ?? null;

                if (empty($field) || ! is_string($field) || ! isset($qualifiedMap[$field])) {
                    continue;
                }

                $qualified = $qualifiedMap[$field];

                switch ($op) {
                    case 'contains':
                        $query->where($qualified, 'like', "%{$value}%");
                        break;
                    case 'starts_with':
                        $query->where($qualified, 'like', "{$value}%");
                        break;
                    case 'ends_with':
                        $query->where($qualified, 'like', "%{$value}");
                        break;
                    case 'equals':
                        $query->where($qualified, $value);
                        break;
                    case 'gt':
                        $query->where($qualified, '>', $value);
                        break;
                    case 'lt':
                        $query->where($qualified, '<', $value);
                        break;
                    case 'between':
                        if (is_string($value) && strpos($value, ',') !== false) {
                            [$a, $b] = array_map('trim', explode(',', $value, 2));
                            if ($a !== '' && $b !== '') {
                                $query->whereBetween($qualified, [$a, $b]);
                            }
                        }
                        break;
                    case 'in':
                        $vals = is_array($value) ? $value : array_filter(array_map('trim', explode(',', (string) $value)), fn($v) => $v !== '');
                        if (! empty($vals)) {
                            $query->whereIn($qualified, $vals);
                        }
                        break;
                    case 'is_null':
                        $query->whereNull($qualified);
                        break;
                    case 'is_not_null':
                        $query->whereNotNull($qualified);
                        break;
                    default:
                        $query->where($qualified, 'like', "%{$value}%");
                }
            }
        }

        $sort = $this->sort ?? 'id';
        $direction = strtolower($this->direction ?? 'desc') === 'asc' ? 'asc' : 'desc';

        if (! is_string($sort) || ! isset($qualifiedMap[$sort])) {
            $sort = 'id';
        }

        $orderColumn = isset($qualifiedMap[$sort]) ? $qualifiedMap[$sort] : "{$table}.id";

        return $query->orderBy($orderColumn, $direction);
    }
}