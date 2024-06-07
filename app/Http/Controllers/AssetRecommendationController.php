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

        $assets = $query->get(); 
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
            'file' => 'required|file|mimes:xlsx,xls,csv,txt'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
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
            Log::error('Failed to import data. Error: ' . $e->getMessage());
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

        if ($rectifyStatus == 'ongoing') {
            $asset->ongoing_status = $progressDate;
            $asset->completed_status = null;
        } elseif ($rectifyStatus == 'completed') {
            if ($asset->ongoing_status && $progressDate > $asset->ongoing_status) {
                $asset->completed_status = $progressDate;
            } else {
                return redirect()->back()->withErrors([
                    'error' => 'Completed status date must be after the ongoing status date'
                ])->withInput();
            }
        }

        $asset->save();

        return redirect()->route('asset_recommendation')->with('success', 'Asset status updated successfully.');
    }

}
