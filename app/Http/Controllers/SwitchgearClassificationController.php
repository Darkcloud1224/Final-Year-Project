<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assets;
use Illuminate\Support\Facades\DB;

class SwitchgearClassificationController extends Controller
{
    public function index()
    {
        $rectifiedCount = Assets::whereNotNull('completed_status')->count();
        $notRectifiedCount = Assets::whereNull('completed_status')->count();

        $notRectifiedAssets = Assets::whereNull('completed_status')->get();

        // Define the list of defects
        $defectList = [
            'CORONA DISCHARGE',
            'ARCHING SOUND',
            'TRACKING SOUND',
            'HOTSPOT',
            'ULTRASOUND',
            'MECHANICAL VIBRATION'
        ];

        // Calculate the count of each defect type
        $defectTypes = [];
        foreach ($defectList as $defect) {
            $defectTypes[$defect] = Assets::where('completed_status', null)->where('defect1', $defect)->count();
        }

        return view('switchgear_classification', compact('rectifiedCount', 'notRectifiedCount', 'notRectifiedAssets', 'defectTypes'));
    }
}
