<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FceerProfile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'fceer_id',
        'volunteer_number',
        'student_number',
        'batch_id',
        'student_group_id',
        'status',
        'notes',
        'created_by_id',
        'updated_by_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(FceerBatch::class, 'batch_id');
    }

    public function studentGroup(): BelongsTo
    {
        return $this->belongsTo(Classroom::class, 'student_group_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }
}