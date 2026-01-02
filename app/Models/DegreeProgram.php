<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DegreeProgram extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'degree_programs';

    protected $fillable = [
        'name',
        'abbreviation',
        'degree_level_id',
        'degree_type_id',
        'degree_field_id',
        'created_by_id',
        'updated_by_id',
    ];

    public function level()
    {
        return $this->belongsTo(DegreeLevel::class, 'degree_level_id');
    }

    public function type()
    {
        return $this->belongsTo(DegreeType::class, 'degree_type_id');
    }

    public function field()
    {
        return $this->belongsTo(DegreeField::class, 'degree_field_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    public function educationalRecords()
    {
        return $this->hasMany(EducationalRecord::class, 'degree_program_id');
    }
}
