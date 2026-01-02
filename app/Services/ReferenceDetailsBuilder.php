<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ReferenceDetailsBuilder
{
    public function build(Model $model, array $fields): array
    {
        $data = [];

        foreach ($fields as $f) {
            $key = $f['key'] ?? null;
            if ($key === null) {
                continue;
            }
            $data[$key] = data_get($model, $key);
        }

        $createdAt = $model->created_at ?? null;
        $updatedAt = $model->updated_at ?? null;

        $data['_meta'] = [
            'created_at' => (string) $createdAt,
            'updated_at' => (string) $updatedAt,
            'created_at_human' => $createdAt ? Carbon::parse($createdAt)->timezone('Asia/Manila')->format('Y-m-d g:i A') : null,
            'updated_at_human' => $updatedAt ? Carbon::parse($updatedAt)->timezone('Asia/Manila')->format('Y-m-d g:i A') : null,
            'created_by' => optional($model->createdBy)->name ?: null,
            'updated_by' => optional($model->updatedBy)->name ?: null,
        ];

        return $data;
    }
}
