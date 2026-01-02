<?php

namespace App\Support;

class ChangeSetBuilder
{
    public static function from(array $fields, array $original, array $current): array
    {
        $changes = [];

        foreach ($fields as $f) {
            $key = $f['key'] ?? null;
            if ($key === null) {
                continue;
            }

            $old = $original[$key] ?? null;
            $new = $current[$key] ?? null;

            if (is_array($old) || is_object($old)) {
                $oldNorm = json_encode($old);
            } else {
                $oldNorm = (string) $old;
            }

            if (is_array($new) || is_object($new)) {
                $newNorm = json_encode($new);
            } else {
                $newNorm = (string) $new;
            }

            if ($oldNorm !== $newNorm) {
                $changes[$key] = [
                    'label' => $f['label'] ?? $key,
                    'old' => $old,
                    'new' => $new,
                ];
            }
        }

        return $changes;
    }
}
