<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{

    use HasFactory;
    protected $table = 'tblrooms';
    // Explicitly define the primary key
    protected $primaryKey = 'RoomID';
    // Tell Laravel that this is an auto-incrementing integer
    public $incrementing = true;
    protected $keyType = 'int';
    // Disable timestamps if your 'tblrooms' table doesn't have
    // created_at and updated_at columns.
    // If you DO have them, keep this as true (or remove this line).
    public $timestamps = true;
    protected $fillable = [
        'RoomName',
        'Location',
        'Capacity',
        'Status',
        'IsDeleted'
    ];
}