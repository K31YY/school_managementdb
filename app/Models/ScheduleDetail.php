<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScheduleDetail extends Model
{
    use HasFactory;

    // 1. Explicitly define the table name
    protected $table = 'tblscheduledetails';

    // 2. Explicitly define the Primary Key (Laravel expects 'id' by default)
    // This is the most common cause for 404 errors during updates!
    protected $primaryKey = 'DetailID';

    // 3. Since your primary key is an INT, ensure this is true
    public $incrementing = true;

    // 4. Fields allowed for mass assignment
    protected $fillable = [
        'ScheduleID', 
        'TeacherID', 
        'SubID', 
        'RoomID', 
        'DayOfWeek', 
        'StartTime', 
        'EndTime', 
        'IsDeleted'
    ];

    // --- Relationships ---

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'ScheduleID', 'ScheduleID');
    }

    public function subject() 
    {
        return $this->belongsTo(Subject::class, 'SubID', 'SubID');
    }

    public function teacher() 
    {
        return $this->belongsTo(Teacher::class, 'TeacherID', 'TeacherID');
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
