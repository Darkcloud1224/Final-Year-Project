<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportLog extends Model
{
    use HasFactory;

    protected $table = 'report_logs'; 

    protected $fillable = [
        'user_name', 'file_name', 'uploaded_at' 
    ];
}

?>