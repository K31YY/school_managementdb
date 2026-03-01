<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $table = 'tblteachers';
    protected $primaryKey = 'TeacherID';
    protected $fillable = ['UserID', 'TeacherName', 'Gender', 'DOB', 'Phone', 'Email', 'Specialty', 'Address', 'StartDate', 'EndDate', 'Certificate', 'Photo', 'IsDeleted'];

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }
    public function scheduleDetails()
    {
        return $this->hasMany(ScheduleDetail::class, 'TeacherID', 'TeacherID');
    }
}
