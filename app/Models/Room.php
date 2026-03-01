<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = 'tblrooms';
    protected $primaryKey = 'RoomID';
    protected $fillable = ['RoomName', 'Location', 'Capacity', 'Status'];
}
