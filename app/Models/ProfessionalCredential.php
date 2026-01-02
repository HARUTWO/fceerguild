<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfessionalCredential extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'field_of_work_id',
        'prefix_id',
        'suffix_id',
        'issued_on',
        'notes',
        'created_by_id',
        'updated_by_id',
    ];

    protected $casts = [
        'issued_on' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fieldOfWork(): BelongsTo
    {
        return $this->belongsTo(FieldOfWork::class);
    }

    public function prefix(): BelongsTo
    {
        return $this->belongsTo(PrefixTitle::class, 'prefix_id');
    }

    public function suffix(): BelongsTo
    {
        return $this->belongsTo(SuffixTitle::class, 'suffix_id');
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
