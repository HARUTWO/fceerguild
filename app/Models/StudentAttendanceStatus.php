<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendanceStatus extends Model
{
    use HasFactory;

    protected $table = 'student_attendance_status';

    protected $fillable = [
        'name',
        'description',
        'point',
    ];

    protected $casts = [
        'point' => 'integer',
    ];

    /**
     * Get the attendance records with this status.
     */
    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class, 'student_status_id');
    }
}
