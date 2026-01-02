<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VolunteerSubject extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'volunteer_subjects';

    protected $fillable = [
        'code',
        'name',
        'description',
        'created_by_id',
        'updated_by_id',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    public function subjectTeachers()
    {
        return $this->hasMany(SubjectTeacher::class, 'volunteer_subject_id');
    }
}
