<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assets;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $goodCount = Assets::where('Health_Status', 'Minor')->count();
        $moderateCount = Assets::where('Health_Status', 'Major')->count();
        $criticalCount = Assets::where('Health_Status', 'Critical')->count();
        $assets = Assets::all();

        $healthStatusData = $assets->groupBy('Health_Status')->map(function ($group) {
            return $group->count();
        })->toArray();

        $tevHotspotData = $assets->map(function ($asset) {
            return [(float) $asset->TEV, (float) $asset->hotspot];
        })->toArray();

        $rectifyStatusData = $assets->groupBy('Rectify_Status')->map(function ($group) {
            return $group->count();
        })->toArray();

        $defectsData = $assets->map(function ($asset) {
            return [
                'Defect1' => $asset->Defect1,
                'Defect2' => $asset->Defect2,
                'Defect3' => $asset->Defects,
            ];
        })->reduce(function ($carry, $item) {
            foreach ($item as $key => $value) {
                if (!isset($carry[$key])) {
                    $carry[$key] = 0;
                }
                $carry[$key] += (int) $value; 
            }
            return $carry;
        }, []);

        return view('home', compact('goodCount', 'moderateCount', 'criticalCount', 'assets', 'healthStatusData', 'tevHotspotData', 'rectifyStatusData', 'defectsData'));
    }
}
