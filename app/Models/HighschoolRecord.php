<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HighschoolRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'highschool_records';

    protected $fillable = [
        'user_id',
        'highschool_id',
        'year_started',
        'level',
        'year_ended',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'highschool_id' => 'integer',
        'year_started' => 'integer',
        'year_ended' => 'integer',
        'level' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function highschool()
    {
        return $this->belongsTo(Highschool::class, 'highschool_id');
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

    public function subjectRecords()
    {
        return $this->hasMany(HighschoolSubjectRecord::class, 'highschool_record_id');
    }
}
