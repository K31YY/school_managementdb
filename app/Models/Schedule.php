<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'tblschedules';
    protected $primaryKey = 'ScheduleID';
    protected $fillable = ['YearID', 'SectionID', 'IsDeleted']; // Add IsDeleted here

    public $timestamps = false;

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'YearID', 'YearID');
    }
    public function classSection()
    {
        return $this->belongsTo(ClassSection::class, 'SectionID', 'SectionID');
    }
    public function details()
    {
        return $this->hasMany(ScheduleDetail::class, 'ScheduleID', 'ScheduleID');
    }
}
