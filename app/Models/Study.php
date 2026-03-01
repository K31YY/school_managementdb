<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Study extends Model
{
    protected $table = 'tblstudies';
    protected $primaryKey = 'StudyID';
    protected $fillable = ['StuID', 'SubID', 'YearID', 'Quiz', 'Homework', 'AttendanceScore', 'Participation', 'Midterm', 'Final', 'TotalScore', 'Semester'];

    public function student()
    {
        return $this->belongsTo(Student::class, 'StuID', 'StuID');
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'SubID', 'SubID');
    }
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'YearID', 'YearID');
    }
}
