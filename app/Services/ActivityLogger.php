<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    public function logCreateOrUpdate(
        Model $model,
        string $action,
        array $changes,
        ?object $causer = null
    ): void {
        $activity = activity()
            ->performedOn($model)
            ->withProperties([
                'changes' => $changes,
                'attributes' => $model->getAttributes(),
            ]);

        if ($causer) {
            $activity->causedBy($causer);
        }

        $activity->log($action);
    }

    public function logDelete(
        Model $model,
        array $attributes,
        ?object $causer = null
    ): void {
        $activity = activity()
            ->performedOn($model)
            ->withProperties(['attributes' => $attributes]);

        if ($causer) {
            $activity->causedBy($causer);
        }

        $activity->log('deleted');
    }
}
