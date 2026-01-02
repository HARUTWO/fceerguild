<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EducationalRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'educational_records';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'degree_program_id',
        'year_started',
        'university_id',
        'year_graduated',
        'dost_scholarship',
        'latin_honor',
    ];

    protected $casts = [
        'year_started' => 'integer',
        'year_graduated' => 'integer',
        'dost_scholarship' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function degreeProgram()
    {
        return $this->belongsTo(DegreeProgram::class, 'degree_program_id');
    }

    public function university()
    {
        return $this->belongsTo(University::class, 'university_id');
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
}
