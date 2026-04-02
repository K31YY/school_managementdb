<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'tblattendances';
    protected $primaryKey = 'AttID';
    public $timestamps = false; // Set to false if your table does NOT have created_at and updated_at columns
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
