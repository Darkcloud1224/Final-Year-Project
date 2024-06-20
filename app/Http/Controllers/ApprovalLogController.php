<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApprovalLog;

class ApprovalLogController extends Controller
{
    public function index()
    {
        $query = ApprovalLog::query();

        $query->orderBy('id', 'desc');

        $approvalLogs = $query->get();
        return view('approval_log', compact('approvalLogs'));
    }
}

