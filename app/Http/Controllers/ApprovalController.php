<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Approval;
use App\Models\Assets;
use App\Models\ApprovalLog;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    public function index()
    {
        $pendingApprovals = Approval::all();  
        $approvalLogs = ApprovalLog::all();
        return view('approval', compact('pendingApprovals', 'approvalLogs'));
    }

    public function approve(Request $request, $id)
    {
        $approval = Approval::findOrFail($id);
        Assets::create([
            'Functional_Location' => $approval->Functional_Location,
            'Switchgear_Brand' => $approval->Switchgear_Brand,
            'Substation_Name' => $approval->Substation_Name,
            'TEV' => $approval->TEV,
            'Hotspot' => $approval->Hotspot,
            'Date' => $approval->Date,
            'Defect' => $approval->Defect,
            'Defect1' => $approval->Defect1,
            'Defect2' => $approval->Defect2,
            'Target_Date' =>$approval->Target_Date,
        ]);

        ApprovalLog::create([
            'Recitified_Action' => 'Approved', 
            'User_Name' => auth()->user()->name,
            'Asset_Name' => $approval->Functional_Location,
            'reasons' => $request->input('reason'),
        ]);

        $approval->delete();

        return redirect()->route('approval.index')->with('success', 'Asset approved successfully.');
    }


    public function reject(Request $request, $id)
    {
        $approval = Approval::findOrFail($id);

        $approval->delete();

        ApprovalLog::create([
            'Recitified_Action' => 'Rejected', 
            'User_Name' => auth()->user()->name,
            'Asset_Name' => $approval->Functional_Location,
            'reasons' => $request->input('reason'),
        ]);

        return redirect()->route('approval.index')->with('success', 'Asset rejected successfully.');
    }

    }




