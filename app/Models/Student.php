<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'tblstudents';
    protected $primaryKey = 'StuID';
    protected $fillable = ['UserID', 'StuName', 'StuNameKH', 'StuNameEN', 'Gender', 'DOB', 'POB', 'Address', 'Phone', 'Email', 'Promotion', 'Photo', 'FatherName', 'FatherJob', 'MotherName', 'MotherJob', 'FamilyContact', 'Status', 'IsDeleted'];

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'StuID', 'StuID');
    }
    public function studies()
    {
        return $this->hasMany(Study::class, 'StuID', 'StuID');
    }
    public function requests()
    {
        return $this->hasMany(LeaveRequest::class, 'StuID', 'StuID');
    }
}
