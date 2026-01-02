<?php

namespace App\Services;

use Illuminate\Support\Collection;

class ActivityHistoryNormalizer
{
    /**
     * 
     *
     * @param Collection $activities
     * @param array $fields
     * @return array
     */
    public function normalize(Collection $activities, array $fields): array
    {
        return $activities->map(function ($act) use ($fields) {
            $props = $act->properties ?? [];
            if ($props instanceof Collection) {
                $props = $props->toArray();
            }

            $rows = [];

            if (! empty($props['changes']) && is_array($props['changes'])) {
                foreach ($props['changes'] as $key => $change) {
                    $rows[] = [
                        'field' => $change['label'] ?? $key,
                        'old' => $change['old'] ?? null,
                        'new' => $change['new'] ?? null,
                    ];
                }
            } elseif (! empty($props['attributes']) && is_array($props['attributes'])) {
                foreach ($props['attributes'] as $key => $val) {
                    $label = collect($fields)->firstWhere('key', $key)['label'] ?? $key;

                    $rows[] = [
                        'field' => $label,
                        'old' => null,
                        'new' => $val,
                    ];
                }
            }

            return [
                'id' => $act->id,
                'created_at' => (string) $act->created_at,
                'created_at_human' => optional($act->created_at)->timezone('Asia/Manila') ? optional($act->created_at->timezone('Asia/Manila'))->format('Y-m-d g:i A') : (string) $act->created_at,
                'causer_name' => optional($act->causer)->name ?: null,
                'description' => $act->description ?? null,
                'rows' => $rows,
            ];
        })->all();
    }
}
