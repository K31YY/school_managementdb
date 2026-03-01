<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'tblnotifications';
    protected $primaryKey = 'NotiID';
    protected $fillable = ['UserID', 'Message', 'IsRead'];

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }
}
