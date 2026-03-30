<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AcademicYear extends Model
{
    use HasFactory;

    protected $table = 'tblacademicyears';
    
    // Explicitly define the primary key
    protected $primaryKey = 'YearID';

    // Ensure Laravel knows this is an auto-incrementing integer
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'YearName', 
        'StartDate', 
        'EndDate', 
        'Description', 
        'IsDeleted'
    ];

    // Relationships
    public function classSections()
    {
        return $this->hasMany(ClassSection::class, 'YearID', 'YearID');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'YearID', 'YearID');
    }
}