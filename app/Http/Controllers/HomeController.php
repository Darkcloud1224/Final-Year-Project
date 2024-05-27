<?php

namespace App\Http\Controllers;
use App\Models\Assets;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
{
    $goodCount = Assets::where('Health_Status', 'Minor')->count();
    $moderateCount = Assets::where('Health_Status', 'Major')->count();
    $criticalCount = Assets::where('Health_Status', 'Critical')->count();

    return view('home', compact('goodCount', 'moderateCount', 'criticalCount'));
}
}