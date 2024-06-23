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

        $assets = Assets::all();

        // Use provided average rectification times
        $averageRectificationTimes = [
            'CORONA DISCHARGE' => 70,
            'ARCHING SOUND' => 25,
            'TRACKING SOUND' => 47,
            'HOTSPOT' => 39,
            'ULTRASOUND' => 52,
            'MECHANICAL VIBRATION' => 54
        ];

        // Calculate the criticality of the assets
        $criticalityLevels = [
            'Clear' => 0,
            'Minor' => 0,
            'Major' => 0,
            'Critical' => 0
        ];

        foreach ($criticalityLevels as $level => $count) {
            $criticalityLevels[$level] = Assets::where('Health_Status', $level)->count();
        }

        return view('switchgear_progress_monitoring', compact('rectifiedCount', 'pendingCount', 'assets', 'averageRectificationTimes', 'criticalityLevels'));
    }
}
