<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApprovalLog;

class ApprovalLogController extends Controller
{
    public function index()
    {
        $approvalLogs = ApprovalLog::all(); 
        return view('approval_log', compact('approvalLogs'));
    }
}

