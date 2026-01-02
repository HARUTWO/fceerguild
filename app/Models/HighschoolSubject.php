<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HighschoolSubject extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'highschool_subjects';

    protected $fillable = [
        'name',
        'subname',
        'code',
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

    public function highschool()
    {
        return $this->belongsTo(Highschool::class);
    }
}
