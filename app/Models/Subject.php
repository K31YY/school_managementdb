<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    // Table name in your phpMyAdmin
    protected $table = 'tblsubjects';

    // Primary Key in your phpMyAdmin
    protected $primaryKey = 'SubID';

    // Columns you are allowed to insert/update
    protected $fillable = [
        'SubName', 
        'Level', 
        'Credit', 
        'Hour', 
        'Description',
        'IsDeleted' 
    ];

    // Matches created_at and updated_at in your table
    public $timestamps = true;

    /**
     * RELATIONSHIPS
     * Logic: Ensure these classes exist in your app/Models folder
     */
    public function scheduleDetails()
    {
        // Check if ScheduleDetail.php exists before using this
        return $this->hasMany(ScheduleDetail::class, 'SubID', 'SubID');
    }

    public function studies()
    {
        // Check if Study.php exists before using this
        return $this->hasMany(Study::class, 'SubID', 'SubID');
    }
}