<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\AssetsExport;
use App\Imports\AssetsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Assets;
use App\Models\User;
use App\Models\ReportLog;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\DeleteRequest;

use Carbon\Carbon;

class AssetRecommendationController extends Controller
{
    /**
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $query = Assets::query();

        if ($request->has('search')) {
            $query->where('Functional_Location', 'like', '%' . $request->input('search') . '%');
        }
        if ($request->has('brand')) {
            $query->where('Switchgear_Brand', 'like', '%' . $request->input('brand') . '%');
        }

        $query->orderBy('id', 'desc');

        $assets = $query->paginate(10); 
        foreach ($assets as $asset) {
            if ($asset->completed_status && $asset->Date) {
                $completionDate = Carbon::parse($asset->completed_status);
                $reportDate = Carbon::parse($asset->Date);
                $asset->Average = $completionDate->diffInDays($reportDate);
            } else {
                $asset->Average = null;  
            }

            if (!$asset->completed_status && $asset->Target_Date) {
                $targetDate = Carbon::parse($asset->Target_Date);
                if ($targetDate->isPast()) {
                    $asset->PendingDays = Carbon::now()->diffInDays($targetDate);
                } else {
                    $asset->PendingDays = null;  
                }
            } else {
                $asset->PendingDays = null;  
            }

            $asset->save();
        }
        $users = User::all();
        return view('asset_recommendation', compact('assets','users'));
    }

    /**
     *
     * @return \Illuminate\Support\Collection
     */
    public function export()
    {
        return Excel::download(new AssetsExport, 'assets.xlsx');
    }

    /**
     *
     * @param Request $request
     * @return \Illuminate\Support\Collection
     */
    public function import(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx'
        ]);

        if ($validator->fails()) {
            session()->flash('error', 'Failed to import assets. Please upload a valid .xlsx file');
        }

        try {
            $file = $request->file('file');
            $existingLogs = ReportLog::where('file_name', $file->getClientOriginalName())->exists();
                Excel::import(new AssetsImport, $file, null, \Maatwebsite\Excel\Excel::XLSX, [
                    'startRow' => 3
                ]);
                
                ReportLog::create([
                    'user_name' => auth()->user()->name,
                    'file_name' => $file->getClientOriginalName(),
                    'uploaded_at' => now(),
                ]);

        } catch (\Exception $e) {
            Log::error('Failed to import assets: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            session()->flash('error', 'Failed to import assets.');
        }
        return back();
    }

    /**
     * Acknowledge the asset
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function acknowledge(Request $request, $id)
    {
        $asset = Assets::findOrFail($id);
        $asset->acknowledgment_status = now();
        $asset->save();

        return redirect()->route('asset_recommendation')->with('success', 'Asset acknowledged successfully.');
    }

    /**
     * Update the asset status
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $asset = Assets::findOrFail($id);
        $asset->rectifier_name = $request->input('rectifierName');
        $progressDate = $request->input('progressDate');
        $rectifyStatus = $request->input('rectifyStatus');

        if ($progressDate <= $asset->Date) {
            session()->flash('error', 'Progress date must be after the report date');
            return redirect()->back()->withInput();
        }

        if ($asset->acknowledgment_status && $progressDate <= $asset->acknowledgment_status) {
            session()->flash('error', 'Progress date must be after the acknowledgment date');
            return redirect()->back()->withInput();
        }

        if ($rectifyStatus == 'ongoing') {
            $asset->ongoing_status = $progressDate;
            $asset->completed_status = null;
        } elseif ($rectifyStatus == 'completed') {
            if (!$asset->ongoing_status || $progressDate <= $asset->ongoing_status) {
                session()->flash('error', 'Completion date must be after the ongoing status date');
                return redirect()->back()->withInput();
            }

            if ($progressDate <= $asset->Date) {
                session()->flash('error', 'Completion date must be after the report date');
                return redirect()->back()->withInput();
            }

            if ($asset->acknowledgment_status && $progressDate <= $asset->acknowledgment_status) {
                session()->flash('error', 'Completion date must be after the acknowledgment date');
                return redirect()->back()->withInput();
            }
            $asset->acknowledgment_status = $asset->acknowledgment_status;
            $asset->ongoing_status = $asset->ongoing_status;
            $asset->completed_status = $progressDate;
        }

        $asset->save();
        session()->flash('success', 'Asset status updated successfully.');
        return redirect()->route('asset_recommendation');
    }




    public function delete(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string',
        ]);

        $asset = Assets::findOrFail($id);
        DeleteRequest::create([
            'Functional_Location' => $asset->Functional_Location,
            'Switchgear_Brand' => $asset->Switchgear_Brand,
            'Substation_Name' => $asset->Substation_Name,
            'TEV' => $asset->TEV,
            'Hotspot' => $asset->Hotspot,
            'Date' => $asset->Date,
            'Defect' => $asset->Defect,
            'Defect1' => $asset->Defect1,
            'Defect2' => $asset->Defect2,
            'Target_Date' =>$asset->Target_Date,
            'completed_status'=>$asset->completed_status,
            'reason' => $request->reason,
            'User_Name' => auth()->user()->name,
        ]);

        return redirect()->route('asset_recommendation')->with('success', 'Asset deletion request has been submited.');
    }

}
