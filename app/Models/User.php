<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // manage table name, primary key, fillable fields, hidden fields, and casts
    protected $table = 'tblusers';

    // manage primary key
    protected $primaryKey = 'UserID';

    // manage fillable fields for mass assignment
    protected $fillable = [
        'Username',
        'Password',
        'Role',
        'Status',
    ];

    // Hide sensitive fields when serializing
    protected $hidden = [
        'Password',
        'remember_token',
    ];

    // manage castd for automatic type conversion
    protected function casts(): array
    {
        return [
            'Password' => 'hashed', 
            'Status' => 'boolean', 
        ];
    }

    // manage relationships with other tables
    public function student() { return $this->hasOne(Student::class, 'UserID', 'UserID'); }
    public function teacher() { return $this->hasOne(Teacher::class, 'UserID', 'UserID'); }
    public function notifications() { return $this->hasMany(Notification::class, 'UserID', 'UserID'); }
}
//=======================================================================================================
// namespace App\Models;
// // use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Notifications\Notifiable;

// class User extends Authenticatable
// {
//     /** @use HasFactory<\Database\Factories\UserFactory> */
//     use HasFactory,Notifiable;

//     /**
//      * The attributes that are mass assignable.
//      *
//      * @var list<string>
//      */
//     protected $table = 'tblusers'; // ប្ដូរឈ្មោះ Table
//     protected $primaryKey = 'UserID'; // ប្ដូរ Primary Key

//     protected $fillable = [
//         'Username', 
//         'Password', 
//         'Role', 
//         'Status'
//     ];
//     /**
//      * The attributes that should be hidden for serialization.
//      *
//      * @var list<string>
//      */
//     protected $hidden = [
//         'Password',
//         'remember_token',
//     ];

//     // Relationships
//     public function student() { return $this->hasOne(Student::class, 'UserID', 'UserID'); }
//     public function teacher() { return $this->hasOne(Teacher::class, 'UserID', 'UserID'); }
// }
//=======================================================================================================
// namespace App\Models;
// // use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Notifications\Notifiable;

// class User extends Authenticatable
// {
//     /** @use HasFactory<\Database\Factories\UserFactory> */
//     use HasFactory, Notifiable;

//     /**
//      * The attributes that are mass assignable.
//      *
//      * @var list<string>
//      */
//     protected $fillable = [
//         'name',
//         'email',
//         'password',
//     ];

//     /**
//      * The attributes that should be hidden for serialization.
//      *
//      * @var list<string>
//      */
//     protected $hidden = [
//         'password',
//         'remember_token',
//     ];

//     /**
//      * Get the attributes that should be cast.
//      *
//      * @return array<string, string>
//      */
//     protected function casts(): array
//     {
//         return [
//             'email_verified_at' => 'datetime',
//             'password' => 'hashed',
//         ];
//     }
// }
