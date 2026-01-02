<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfilePicture extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'profile_pictures';

    protected $fillable = [
        'user_id',
        'attachment_id',
        'is_current',
        'uploaded_by_id',
        'metadata',
        'note',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function attachment()
    {
        return $this->belongsTo(Attachment::class, 'attachment_id');
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by_id');
    }
}
