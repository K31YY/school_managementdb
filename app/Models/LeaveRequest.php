<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $table = 'tblrequests';
    protected $primaryKey = 'ReqID';
    protected $fillable = ['StuID', 'Reason', 'DateRequested', 'Status'];

    public function student()
    {
        return $this->belongsTo(Student::class, 'StuID', 'StuID');
    }
}
