<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrefixTitle extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'prefix_titles';

    protected $fillable = [
        'name',
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
