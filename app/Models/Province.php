<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// removed HasAuditFields trait: auditing is handled by ReferenceCrud

class Province extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'provinces';

    protected $fillable = [
        'name',
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

    public function cities()
    {
        return $this->hasMany(City::class, 'province_id');
    }
}
