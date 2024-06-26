<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Approval;
use App\Models\Assets;
use App\Models\ApprovalLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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

        $duplicate = Assets::where('Functional_Location', $approval->Functional_Location)
            ->where('Switchgear_Brand', $approval->Switchgear_Brand)
            ->where('Substation_Name', $approval->Substation_Name)
            ->where('TEV', $approval->TEV)
            ->where('Hotspot', $approval->Hotspot)
            ->where('Date', $approval->Date)
            ->where('Defect', $approval->Defect)
            ->where('Defect1', $approval->Defect1)
            ->where('Defect2', $approval->Defect2)
            ->where('Target_Date', $approval->Target_Date)
            ->exists();

        if ($duplicate) {
            return redirect()->route('approval.index')->with('error', 'Duplicate asset found. Approval not processed.');
        }

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
            'Target_Date' => $approval->Target_Date,
            'completed_status' => $approval->completed_status,
        ]);

        // Log the approval action
        ApprovalLog::create([
            'Recitified_Action' => 'Approved', 
            'User_Name' => auth()->user()->name,
            'Asset_Name' => $approval->Functional_Location,
            'reasons' => $request->input('reason'),
        ]);

        // Delete the approval request
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
