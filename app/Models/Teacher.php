<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // use for API Token Authentication

class Teacher extends Authenticatable
{
    use HasApiTokens, Notifiable; // use HasApiTokens for API Token Authentication

    protected $table = 'tblteachers';
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
        'remember_token', // must be hidden for security
    ];

    /**
     * manage data type casting for certain fields
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed', // bettween Laravel 10/11, you can give Laravel the hint to automatically hash the password when saving by using 'hashed' cast
            'DOB' => 'date',
            'StartDate' => 'date',
            'EndDate' => 'date',
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }

    public function scheduleDetails()
    {
        return $this->hasMany(ScheduleDetail::class, 'TeacherID', 'TeacherID');
    }
}