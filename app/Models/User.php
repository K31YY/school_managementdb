<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // ១. កំណត់ឈ្មោះ Table ឱ្យត្រូវជាមួយ Migration
    protected $table = 'tblusers';

    // ២. កំណត់ Primary Key ឱ្យត្រូវ
    protected $primaryKey = 'UserID';

    // ៣. កំណត់ Field ដែលអាចបញ្ចូលទិន្នន័យបាន (Fillable)
    protected $fillable = [
        'Username',
        'Password',
        'Role',
        'Status',
    ];

    // ៤. លាក់ Field សម្ងាត់មិនឱ្យបង្ហាញពេល Query
    protected $hidden = [
        'Password',
        'remember_token',
    ];

    // ៥. កំណត់ប្រភេទ Data (Casting)
    protected function casts(): array
    {
        return [
            'Password' => 'hashed', // Hash password ស្វ័យប្រវត្តិ
            'Status' => 'boolean',  // បំប្លែង Status ជា true/false
        ];
    }

    // ៦. Relationship ទៅ Table ផ្សេងទៀត
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
