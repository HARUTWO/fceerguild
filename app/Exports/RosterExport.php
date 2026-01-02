<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Maatwebsite\Excel\Concerns\FromArray;

class RosterExport implements FromArray
{
    protected EloquentBuilder $query;
    protected array $columns;
    protected array $visibleColumns;
    protected array $meta;
    protected string $type;
    protected $component;

    public function __construct(
        EloquentBuilder $query,
        array $columns,
        array $visibleColumns,
        string $type,
        $component,
        array $meta = []
    ) {
        $this->query = $query;
        $this->columns = $columns;
        $this->visibleColumns = $visibleColumns;
        $this->type = $type;
        $this->component = $component;
        $this->meta = $meta;
    }

    /**
     * Build an array where first rows are metadata, then header row, then data rows.
     */
    public function array(): array
    {
        $rows = [];

        // Metadata rows
        $rosterType = ucfirst($this->type);
        $exportedAt = $this->meta['exported_at'] ?? now()->format('Y-m-d H:i:s');
        $exportedBy = $this->meta['exported_by'] ?? null;

        $rows[] = ["Roster: {$rosterType}"];
        $rows[] = ["Exported At: {$exportedAt}"];
        if ($exportedBy) {
            $rows[] = ["Exported By: {$exportedBy}"];
        }
        $rows[] = []; // Blank separator row

        // Header row
        $headers = [];
        foreach ($this->visibleColumns as $colKey) {
            if ($colKey === 'row_number') {
                $headers[] = '#';
            } else {
                $col = $this->columns[$colKey] ?? null;
                $headers[] = $col['label'] ?? $colKey;
            }
        }
        $rows[] = $headers;

        // Data rows
        $index = 0;
        foreach ($this->query->cursor() as $user) {
            $index++;
            $row = [];

            foreach ($this->visibleColumns as $colKey) {
                if ($colKey === 'row_number') {
                    $row[] = $index;
                } else {
                    $value = $this->component->getColumnValue($user, $colKey);
                    // Handle booleans
                    if (is_bool($value)) {
                        $row[] = $value ? 'Yes' : 'No';
                    } elseif ($value instanceof \Carbon\Carbon) {
                        $row[] = $value->format('Y-m-d');
                    } else {
                        $row[] = $value ?? '';
                    }
                }
            }

            $rows[] = $row;
        }

        return $rows;
    }
}
