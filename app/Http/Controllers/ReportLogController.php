<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportLog;


class ReportLogController extends Controller
{
    public function index()
    {
        $reportLogs = ReportLog::all(); 
        return view('report_log', compact('reportLogs'));
    }
}
