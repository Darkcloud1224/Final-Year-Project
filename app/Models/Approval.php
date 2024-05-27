<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    use HasFactory;

    protected $fillable = [
        'Functional_Location',
        'Switchgear_Brand',
        'Substation_Name',
        'TEV',
        'Hotspot',
    ];
}
