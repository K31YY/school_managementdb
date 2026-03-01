<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleDetail extends Model
{
    protected $table = 'tblscheduledetails';
    protected $primaryKey = 'DetailID';
    protected $fillable = ['ScheduleID', 'TeacherID', 'SubID', 'RoomID', 'DayOfWeek', 'StartTime', 'EndTime'];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'ScheduleID', 'ScheduleID');
    }
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'TeacherID', 'TeacherID');
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'SubID', 'SubID');
    }
    public function room()
    {
        return $this->belongsTo(Room::class, 'RoomID', 'RoomID');
    }
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'DetailID', 'DetailID');
    }
}
