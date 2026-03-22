<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Correct table name mapping
    protected $table = 'tblusers';

    // Correct Primary Key for your custom schema
    protected $primaryKey = 'UserID';

    // Mass assignment protection
    protected $fillable = [
        'Username',
        'Password',
        'Role',
        'Status',
    ];

    // Hide sensitive data from API responses
    protected $hidden = [
        'Password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            // ANALYTICAL NOTE: Removed 'Password' => 'hashed' to avoid double-hashing
            // since you are already using Hash::make() in your Controllers.
            'Status' => 'boolean', 
        ];
    }

    // --- Relationships ---

    public function student() 
    { 
        return $this->hasOne(Student::class, 'UserID', 'UserID'); 
    }

    public function teacher() 
    { 
        return $this->hasOne(Teacher::class, 'UserID', 'UserID'); 
    }

    public function notifications() 
    { 
        return $this->hasMany(Notification::class, 'UserID', 'UserID'); 
    }
}