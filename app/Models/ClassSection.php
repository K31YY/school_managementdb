<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSection extends Model
{
    protected $table = 'tblclasssections';
    protected $primaryKey = 'SectionID';
    protected $fillable = ['SectionName', 'YearID'];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'YearID', 'YearID');
    }
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'SectionID', 'SectionID');
    }
}
