<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assets;

class SwitchgearClassificationController extends Controller
{
    public function index()
    {
        $rectifiedCount = Assets::whereNotNull('completed_status')->count();
        $notRectifiedCount = Assets::whereNull('completed_status')->count();
        $notRectifiedAssets = Assets::whereNull('completed_status')->get();
        $rectifiedAssets = Assets::whereNotNull('completed_status')->get();
        $allAssets = Assets::all();

        $defectList = [
            'CORONA DISCHARGE',
            'ARCHING SOUND',
            'TRACKING SOUND',
            'HOTSPOT',
            'ULTRASOUND',
            'MECHANICAL VIBRATION'
        ];

        $defectTypes = [];
        foreach ($defectList as $defect) {
            $defectTypes[$defect] = Assets::where(function ($query) use ($defect) {
                    $query->where('defect1', $defect);
                })
                ->count();
        }

        return view('switchgear_classification', compact('rectifiedCount', 'notRectifiedCount', 'notRectifiedAssets', 'defectTypes', 'rectifiedAssets', 'allAssets'));
    }
}
