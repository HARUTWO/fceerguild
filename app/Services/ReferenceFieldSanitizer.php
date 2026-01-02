<?php

namespace App\Services;

class ReferenceFieldSanitizer
{
    public function sanitize(array $fields): array
    {
        return collect($fields)->map(function ($f) {
            if (isset($f['options']) && is_callable($f['options'])) {
                $f['options'] = [];
            }
            return $f;
        })->toArray();
    }
}
