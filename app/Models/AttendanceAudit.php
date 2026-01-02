<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceAudit extends Model
{
    use HasFactory;

    protected $table = 'attendance_audits';

    protected $fillable = [
        'attendance_record_id',
        'changed_by_id',
        'action',
        'previous',
        'changes',
        'note',
    ];

    protected $casts = [
        'previous' => 'array',
        'changes' => 'array',
    ];

    public function attendanceRecord()
    {
        return $this->belongsTo(AttendanceRecord::class, 'attendance_record_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by_id');
    }
}
