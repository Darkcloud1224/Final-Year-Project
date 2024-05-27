<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportGenerationController extends Controller
{
    public function index()
    {
        return view('report_generation');
    }
}
