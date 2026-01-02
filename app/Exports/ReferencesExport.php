<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Maatwebsite\Excel\Concerns\FromArray;
use Illuminate\Support\Str;
use App\Services\ReferenceDisplayFormatter;

class ReferencesExport implements FromArray
{
    protected EloquentBuilder $query;
    protected array $fields;
    protected array $meta;

    public function __construct(EloquentBuilder $query, array $fields = [], array $meta = [])
    {
        $this->query = $query;
        $this->fields = $fields;
        $this->meta = $meta;
    }

    /**
     * Build an array where first rows are metadata, then header row, then data rows.
     */
    public function array(): array
    {
        $rows = [];

        $tableName = $this->meta['table'] ?? null;
        $exportedAt = $this->meta['exported_at'] ?? null;
        $exportedBy = $this->meta['exported_by'] ?? null;

        if ($tableName) {
            $rows[] = ["Table: {$tableName}"];
        }
        if ($exportedAt) {
            $rows[] = ["Exported At: {$exportedAt}"];
        }
        if ($exportedBy) {
            $rows[] = ["Exported By: {$exportedBy}"];
        }

        // blank separator row
        $rows[] = [];

        // header row (include row number as first column)
        if (empty($this->fields)) {
            $headers = ['#', 'id'];
        } else {
            $headers = array_merge(['#'], array_map(function ($f) {
                return $f['label'] ?? ($f['key'] ?? '');
            }, $this->fields));
        }

        $rows[] = $headers;

        // data rows: fetch all matching rows (use cursor for memory safety)
        $query = clone $this->query;

        // Eager-load relations for any *_id fields to avoid N+1 and ensure related labels are available
        $eager = [];
        foreach ($this->fields as $f) {
            $key = $f['key'] ?? null;
            if (is_string($key) && Str::endsWith($key, '_id')) {
                $rel = substr($key, 0, -3);
                $eager[] = Str::camel($rel);
            }
        }
        $eager = array_values(array_unique(array_filter($eager)));
        if (! empty($eager)) {
            try {
                $query = $query->with($eager);
            } catch (\Throwable $e) {
                // ignore if relation not defined
            }
        }

        $formatter = app(ReferenceDisplayFormatter::class);

        $index = 1;
        foreach ($query->cursor() as $row) {
            if (empty($this->fields)) {
                $rows[] = [$index++, $row->id];
                continue;
            }

            $data = [];
            foreach ($this->fields as $f) {
                $key = $f['key'] ?? null;
                if (! $key) {
                    $data[] = '';
                    continue;
                }

                // Let the shared display formatter decide how to present the value
                try {
                    $display = $formatter->formatFieldValue($row, $f);
                } catch (\Throwable $e) {
                    $display = data_get($row, $key);
                }

                $data[] = $display;
            }

            // prepend row number
            $rows[] = array_merge([$index++], $data);
        }

        return $rows;
    }
}
