<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommitteePosition extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'committee_positions';

    protected $fillable = [
        'name',
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

    public function CommitteeMemberships()
    {
        return $this->hasMany(CommitteeMembership::class, 'committee_position_id');
    }
}
