<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FceerBatch extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fceer_batches';

    protected $fillable = [
        'batch_no',
        'year',
        'created_by_id',
        'updated_by_id',
        'deleted_by_id',
        'review_season_id',
    ];

    protected $casts = [
        'batch_no' => 'integer',
        'year' => 'integer',
    ];

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
    public function reviewSeason()
    {
        return $this->belongsTo(ReviewSeason::class, 'review_season_id');
    }
}
