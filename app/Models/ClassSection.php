<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSection extends Model
{
    // 1. Table and Primary Key Settings
    protected $table = 'tblclasssections';
    protected $primaryKey = 'SectionID';
    
    // Set to false if your table does NOT have created_at and updated_at columns
    public $timestamps = false; 

    // 2. Mass Assignment Protection
    // Added 'IsDeleted' so your Controller's destroy() method can update it
    protected $fillable = [
        'SectionName', 
        'YearID', 
        'IsDeleted' 
    ];

    // 3. Relationships
    
    /**
     * Get the Academic Year associated with the Class Section.
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'YearID', 'YearID');
    }

    /**
     * Get the Schedules associated with the Class Section.
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'SectionID', 'SectionID');
    }
}