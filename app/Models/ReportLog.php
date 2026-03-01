<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportLog extends Model
{
    protected $table = 'tblreportlogs';
    protected $primaryKey = 'LogID';
    protected $fillable = ['UserID', 'ReportType', 'GeneratedAt'];

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }
}
