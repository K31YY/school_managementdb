<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'tblattendances';
    protected $primaryKey = 'AttID';
    protected $fillable = ['StuID', 'DetailID', 'AttDate', 'Status', 'Reason'];

    public function student()
    {
        return $this->belongsTo(Student::class, 'StuID', 'StuID');
    }
    public function scheduleDetail()
    {
        return $this->belongsTo(ScheduleDetail::class, 'DetailID', 'DetailID');
    }
}
