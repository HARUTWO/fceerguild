<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuffixTitle extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'suffix_titles';

    protected $fillable = [
        'title',
        'abbreviation',
        'field_of_work_id',
        'created_by_id',
        'updated_by_id',
    ];

    public function fieldOfWork()
    {
        return $this->belongsTo(FieldOfWork::class, 'field_of_work_id');
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
