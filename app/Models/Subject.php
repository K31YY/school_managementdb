<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = 'tblsubjects';
    protected $primaryKey = 'SubID';
    protected $fillable = ['SubName', 'Level', 'Credit', 'Hour', 'Description'];

    public function scheduleDetails()
    {
        return $this->hasMany(ScheduleDetail::class, 'SubID', 'SubID');
    }
    public function studies()
    {
        return $this->hasMany(Study::class, 'SubID', 'SubID');
    }
}
