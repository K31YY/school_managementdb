<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $table = 'tblteachers';
    
    // Rigorous Check: Ensure this matches TeacherID used in TeacherController and AuthController
    protected $primaryKey = 'TeacherID';

    protected $fillable = [
        'UserID',
        'TeacherName',
        'Gender',
        'DOB',
        'Phone',
        'Email',
        'password',
        'Specialty',
        'Address',
        'StartDate',
        'EndDate',
        'Certificate',
        'Photo',
        'IsDeleted'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Data Type Casting
     */
    protected function casts(): array
    {
        return [
            // ANALYTICAL NOTE: Removed 'password' => 'hashed' to avoid double-hashing
            // since you use Hash::make() in your TeacherController.
            'DOB'       => 'date',
            'StartDate' => 'date',
            'EndDate'   => 'date',
            'IsDeleted' => 'boolean',
        ];
    }

    // --- Relationships ---

    /**
     * Link back to the central Admin/User table
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }

    /**
     * Link to schedule details
     */
    public function scheduleDetails()
    {
        return $this->hasMany(ScheduleDetail::class, 'TeacherID', 'TeacherID');
    }
}