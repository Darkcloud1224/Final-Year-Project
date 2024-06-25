<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assets;
use Illuminate\Support\Facades\DB;

class SwitchgearProgressMonitoringController extends Controller
{
    public function index()
    {
        $rectifiedCount = Assets::whereNotNull('completed_status')->count();
        $pendingCount = Assets::whereNull('completed_status')->count();

        $rectifiedAssets = Assets::whereNotNull('completed_status')->get();

        $pendingAssets = Assets::whereNull('completed_status')->get();

        $assets = Assets::all();

        $averageRectificationTimes = [
            'CORONA DISCHARGE' => 70,
            'ARCHING SOUND' => 25,
            'TRACKING SOUND' => 47,
            'HOTSPOT' => 39,
            'ULTRASOUND' => 52,
            'MECHANICAL VIBRATION' => 54
        ];

        $criticalityLevels = [
            'Clear' => 0,
            'Minor' => 0,
            'Major' => 0,
            'Critical' => 0
        ];

        foreach ($criticalityLevels as $level => $count) {
            $criticalityLevels[$level] = Assets::where('Health_Status', $level)->count();
        }

        return view('switchgear_progress_monitoring', compact('rectifiedCount', 'pendingCount', 'assets', 'averageRectificationTimes', 'criticalityLevels', 'rectifiedAssets', 'pendingAssets'));
    }
}
