<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DegreeLevel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'degree_levels';

    protected $fillable = [
        'name',
        'abbreviation',
        'created_by_id',
        'updated_by_id',
    ];

    public function programs()
    {
        return $this->hasMany(DegreeProgram::class, 'degree_level_id');
    }

    public function degreeTypes()
    {
        return $this->hasMany(DegreeType::class, 'degree_level_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }
}
