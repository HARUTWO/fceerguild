<?php

namespace App\Http\Controllers;

use App\Exports\ReferencesExport;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ReferenceExportController extends Controller
{
    public function export(Request $request)
    {
        $modelClass = $request->query('modelClass');
        $format = $request->query('format', 'xlsx');

        if (empty($modelClass) || ! class_exists($modelClass)) {
            abort(404);
        }

        // Basic authorization: allow if the current user passes the model policy `viewAny`
        // or if they have the broader `manageReferenceTables` ability used elsewhere in the app.
        $user = $request->user();
        $allowed = false;

        if ($user) {
            try {
                $allowed = $user->can('viewAny', $modelClass);
            } catch (\Throwable $e) {
                // policy not defined or threw — fall through to other checks
                $allowed = false;
            }

            if (! $allowed && Gate::allows('manageReferenceTables')) {
                $allowed = true;
            }
        }

        if (! $allowed) {
            abort(403);
        }

        // Rebuild a query using provided filters/fields/search/sort
        $query = $modelClass::query();
        $table = (new $modelClass)->getTable();

        $search = trim((string) $request->query('search', ''));
        $fields = json_decode($request->query('fields', '[]'), true) ?: [];

        if ($search !== '') {
            $lower = mb_strtolower($search);
            $query->where(function ($q) use ($lower, $table, $fields) {
                foreach ($fields as $f) {
                    $col = $f['key'] ?? null;
                    if (! $col) continue;

                    $qualified = "{$table}.{$col}";
                    try {
                        $q->orWhereRaw("LOWER(COALESCE({$qualified},'')) LIKE ?", ["%{$lower}%"]);
                    } catch (\Throwable $e) {
                        $q->orWhere($col, 'like', "%{$lower}%");
                    }
                }
            });
        }

        // apply filters param (JSON encoded array of ['field','op','value'])
        $filters = json_decode($request->query('filters', '[]'), true) ?: [];
        foreach ($filters as $flt) {
            $field = $flt['field'] ?? null;
            $op = $flt['op'] ?? 'contains';
            $value = $flt['value'] ?? null;

            if (empty($field)) continue;

            $qualified = "{$table}.{$field}";

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
                        $query->whereBetween($qualified, [$a, $b]);
                    }
                    break;
                case 'in':
                    $vals = is_array($value) ? $value : array_map('trim', explode(',', (string) $value));
                    $query->whereIn($qualified, $vals);
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

        $sort = $request->query('sort', 'id');
        $direction = $request->query('direction', 'desc');
        $query->orderBy($sort, $direction);

        $filenameBase = $table ?: 'references';

        // Use Manila timezone for exported timestamp and filename
        $nowManila = now()->setTimezone('Asia/Manila');
        $filename = Str::slug($filenameBase) . '-' . $nowManila->format('Ymd-His') . '.' . ($format === 'csv' ? 'csv' : 'xlsx');

        $meta = [
            'table' => $table,
            'exported_at' => $nowManila->format('Y-m-d H:i:s'),
            'exported_by' => $user?->name ?? $user?->email ?? 'unknown',
        ];

        $export = new ReferencesExport($query, $fields ?: [], $meta);

        $writerType = $format === 'csv' ? \Maatwebsite\Excel\Excel::CSV : \Maatwebsite\Excel\Excel::XLSX;

        return Excel::download($export, $filename, $writerType);
    }
}
