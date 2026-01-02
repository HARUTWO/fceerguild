<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FieldOfWork extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fields_of_work';

    protected $fillable = [
        'name',
        'created_by_id',
        'updated_by_id',
    ];

    public function prefixTitles()
    {
        return $this->hasMany(PrefixTitle::class, 'field_of_work_id');
    }

    public function suffixTitles()
    {
        return $this->hasMany(SuffixTitle::class, 'field_of_work_id');
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
