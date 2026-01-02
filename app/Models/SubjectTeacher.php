<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectTeacher extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'subject_teachers';

    protected $fillable = [
        'volunteer_subject_id',
        'user_id',
        'subject_proficiency',
        'created_by_id',
        'updated_by_id',
        'deleted_by_id'
    ];

    protected $casts = [
        'subject_proficiency' => 'string',
    ];

    public function volunteerSubject()
    {
        return $this->belongsTo(VolunteerSubject::class, 'volunteer_subject_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
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
