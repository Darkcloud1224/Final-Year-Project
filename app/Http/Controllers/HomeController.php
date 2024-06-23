<?php

namespace App\Http\Controllers;

use App\Models\Assets;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $assets = Assets::all();

        $totalAssets = $assets->count();
        $rectifiedAssets = $assets->where('completed_status', '!=', null)->count();
        $notRectifiedAssets = $totalAssets - $rectifiedAssets;

        $criticalityData = [
            'Clear' => $assets->where('Health_Status', 'Clear')->count(),
            'Minor' => $assets->where('Health_Status', 'Minor')->count(),
            'Major' => $assets->where('Health_Status', 'Major')->count(),
            'Critical' => $assets->where('Health_Status', 'Critical')->count()
        ];

        $defectsData = [
            'Hotspot' => $assets->where('Defect1', 'HOTSPOT')->count(),
            'Arching Sound' => $assets->where('Defect1', 'ARCHING SOUND')->count(),
            'Tracking Sound' => $assets->where('Defect1', 'TRACKING SOUND')->count(),
            'Ultrasound' => $assets->where('Defect1', 'ULTRASOUND')->count(),
            'Mechanical Vibration' => $assets->where('Defect1', 'MECHANICAL VIBRATION')->count(),
            'Corona Discharge' => $assets->where('Defect1', 'CORONA DISCHARGE')->count()
        ];

        $defectsByBrand = $assets->groupBy('Switchgear_Brand')->map(function ($group) {
            return $group->groupBy('Defect1')->map->count();
        });

        $categories = $defectsByBrand->keys()->toArray();
        $series = [];

        $defectTypes = ['HOTSPOT', 'ARCHING SOUND', 'TRACKING SOUND', 'ULTRASOUND', 'MECHANICAL VIBRATION', 'CORONA DISCHARGE'];

        foreach ($defectTypes as $defectType) {
            $data = [];
            foreach ($categories as $brand) {
                $data[] = $defectsByBrand[$brand]->get($defectType, 0);
            }
            $series[] = [
                'name' => $defectType,
                'data' => $data
            ];
        }

        return view('home', compact('totalAssets', 'rectifiedAssets', 'notRectifiedAssets', 'criticalityData', 'defectsData', 'categories', 'series'));
    }
}
