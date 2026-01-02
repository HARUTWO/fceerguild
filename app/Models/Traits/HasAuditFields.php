<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait HasAuditFields
{
    public static function bootHasAuditFields()
    {
        static::creating(function ($model) {
            if (Schema::hasColumn($model->getTable(), 'created_by_id') && Auth::check()) {
                $model->created_by_id = $model->created_by_id ?? Auth::id();
            }

            if (Schema::hasColumn($model->getTable(), 'updated_by_id') && Auth::check()) {
                $model->updated_by_id = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Schema::hasColumn($model->getTable(), 'updated_by_id') && Auth::check()) {
                $model->updated_by_id = Auth::id();
            }
        });

        static::created(function ($model) {
            try {
                if (function_exists('activity')) {
                    activity()
                        ->performedOn($model)
                        ->causedBy(Auth::user())
                        ->withProperties(['attributes' => $model->getAttributes()])
                        ->log('created');
                }
            } catch (\Throwable $e) {
            }
        });

        static::updated(function ($model) {
            try {
                if (function_exists('activity')) {
                    activity()
                        ->performedOn($model)
                        ->causedBy(Auth::user())
                        ->withProperties(['changes' => $model->getChanges()])
                        ->log('updated');
                }
            } catch (\Throwable $e) {
            }
        });

        static::deleted(function ($model) {
            try {
                if (function_exists('activity')) {
                    activity()
                        ->performedOn($model)
                        ->causedBy(Auth::user())
                        ->withProperties(['attributes' => $model->getAttributes()])
                        ->log('deleted');
                }
            } catch (\Throwable $e) {
            }
        });
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by_id');
    }
}
