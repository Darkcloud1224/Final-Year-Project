<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeleteRequestLog extends Model
{
    use HasFactory;

    protected $table = 'delete_request_log'; 


    protected $fillable = [ 
        'Functional_Location',
        'Switchgear_Brand',
        'Substation_Name',
        'TEV',
        'Hotspot',
        'Date',
        'Defect',
        'Defect1',
        'Defect2',
        'Target_Date',
        'completed_status',
        'reason',
        'User_Name',
        'Approved',
        ];
}
