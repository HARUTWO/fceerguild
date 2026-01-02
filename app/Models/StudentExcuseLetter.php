<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentExcuseLetter extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'student_excuse_letters';

    protected $fillable = [
        'attachment_id',
        'user_id',
        'reason',
        'date_attendance',
        'letter_status',
        'updated_by_id',
        'letter_link',
    ];

    protected $casts = [
        'date_attendance' => 'date',
    ];

    public function attachment()
    {
        return $this->belongsTo(Attachment::class, 'attachment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    public function attendanceRecords()
    {
        return $this->belongsToMany(
            AttendanceRecord::class,
            'attendance_record_excuse_letter',
            'student_excuse_letter_id',
            'attendance_record_id'
        );
    }
}
