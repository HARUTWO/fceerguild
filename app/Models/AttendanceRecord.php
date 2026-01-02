<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'attendance_records';

    protected $fillable = [
        'user_id',
        'date',
        'time',
        'time_in',
        'time_out',
        'duration_minutes',
        'session',
        'absence_value',
        'review_season_id',
        'student_status_id',
        'recorded_by_id',
        'updated_by_id',
    ];

    protected $casts = [
        'date' => 'date',
        'duration_minutes' => 'integer',
        'absence_value' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewSeason()
    {
        return $this->belongsTo(ReviewSeason::class, 'review_season_id');
    }

    /**
     * Get the student attendance status for this record.
     */
    public function studentStatus()
    {
        return $this->belongsTo(StudentAttendanceStatus::class, 'student_status_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    public function audits()
    {
        return $this->hasMany(AttendanceAudit::class, 'attendance_record_id');
    }

    public function studentExcuseLetters()
    {
        return $this->belongsToMany(
            StudentExcuseLetter::class,
            'attendance_record_excuse_letter',
            'attendance_record_id',
            'student_excuse_letter_id'
        );
    }
}
