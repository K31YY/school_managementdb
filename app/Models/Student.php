<?php

namespace App\Models;

// change to Authenticatable and add HasApiTokens for API Token Authentication
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // use for API Token Authentication

class Student extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'tblstudents';
    protected $primaryKey = 'StuID';

    protected $fillable = [
        'UserID',
        'StuName',
        'StuNameKH',
        'StuNameEN',
        'Gender',
        'DOB',
        'POB',
        'Address',
        'Phone',
        'Email',
        'password',
        'Promotion',
        'Photo',
        'FatherName',
        'FatherJob',
        'MotherName',
        'MotherJob',
        'FamilyContact',
        'Status',
        'IsDeleted'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * make Laravel automatically hash the password when saving
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed', // give Laravel the hint to automatically hash the password when saving (Laravel 10/11 feature)
            'DOB' => 'date',
            'Status' => 'boolean',
            'IsDeleted' => 'boolean',
        ];
    }

    // --- Relationships ---
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