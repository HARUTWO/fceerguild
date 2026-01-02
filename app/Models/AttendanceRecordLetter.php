<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecordLetter extends Model
{
    use HasFactory;

    protected $table = 'attendance_records_letter';

    protected $fillable = [
        'attendance_record_id',
        'student_excuse_letter_id',
    ];

    public function attendanceRecord()
    {
        return $this->belongsTo(AttendanceRecord::class, 'attendance_record_id');
    }

    public function studentExcuseLetter()
    {
        return $this->belongsTo(StudentExcuseLetter::class, 'student_excuse_letter_id');
    }
}
