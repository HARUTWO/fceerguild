<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HighschoolSubjectRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'highschool_subject_records';

    public $timestamps = true;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'user_id',
        'highschool_subject_id',
        'grade',
        'highschool_record_id',
        'created_by_id',
        'updated_by_id',
        'deleted_by_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'highschool_subject_id' => 'integer',
        'highschool_record_id' => 'integer',
        'created_by_id' => 'integer',
        'updated_by_id' => 'integer',
        'deleted_by_id' => 'integer',
        'grade' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subject()
    {
        return $this->belongsTo(HighschoolSubject::class, 'highschool_subject_id');
    }

    public function highschoolRecord()
    {
        return $this->belongsTo(HighschoolRecord::class, 'highschool_record_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by_id');
    }

    public function getDeletedByNameAttribute()
    {
        if ($this->deletedBy) {
            return method_exists($this->deletedBy, 'initials') ? $this->deletedBy->initials() : ($this->deletedBy->name ?? null);
        }

        return null;
    }
}
