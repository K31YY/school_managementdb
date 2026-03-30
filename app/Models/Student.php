<?php



namespace App\Models;



use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\Factories\HasFactory;



class Student extends Authenticatable

{

    use HasApiTokens, HasFactory, Notifiable;



    protected $table = 'tblstudents';

   

    // Logic: Ensure this matches your actual database column name

    protected $primaryKey = 'StuID';



    protected $fillable = [

        'UserID', 'StuName', 'StuNameKH', 'StuNameEN', 'Gender', 'DOB', 'POB',

        'Address', 'Phone', 'Email', 'password', 'Promotion', 'Photo',

        'FatherName', 'FatherJob', 'MotherName', 'MotherJob', 'FamilyContact',

        'Status', 'IsDeleted'

    ];



    protected $hidden = [

        'password',

        'remember_token',

    ];



    /**

     * Analytical Correction: We removed 'password' => 'hashed'

     * to prevent double-hashing because your AuthController

     * already hashes the password manually.

     */

    protected function casts(): array

    {

        return [

            'DOB' => 'date',

            'Status' => 'boolean',

            'IsDeleted' => 'boolean',

        ];

    }



    // --- Relationships ---



    /**

     * Link back to the central User table

     */

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