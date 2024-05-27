<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalLog extends Model
{
    use HasFactory;

    protected $table = 'approval_logs'; 

    protected $fillable = [
        'User_Name', 'Asset_Name', 'Recitified_Action', 'reasons'
    ];
}

?>