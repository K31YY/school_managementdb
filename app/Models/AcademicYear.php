<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $table = 'tblacademicyears';
    protected $primaryKey = 'YearID';
    protected $fillable = ['YearName', 'StartDate', 'EndDate', 'Description', 'IsDeleted'];

    public function classSections()
    {
        return $this->hasMany(ClassSection::class, 'YearID', 'YearID');
    }
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'YearID', 'YearID');
    }
}
