<?php

namespace App\Http\Controllers;

use App\Models\DeleteRequest;
use App\Models\Assets;
use App\Models\DeleteRequestLog;

use Illuminate\Http\Request;

class DeleteRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pendingDeleteApprovals = DeleteRequest::all();  
        return view('delete_request', compact('pendingDeleteApprovals'));
    }

    public function approveDeleteRequest($id)
    {
        $deleteRequest = DeleteRequest::findOrFail($id);
        $asset = Assets::where('Functional_Location', $deleteRequest->Functional_Location)->first();

        if ($asset) {
            DeleteRequestLog::create([
                'Functional_Location' => $asset->Functional_Location,
                'reason' => $deleteRequest->reason,
            ]);

            $asset->delete();
        }

        $deleteRequest->delete();

        return redirect()->route('delete_requests.index')->with('success', 'Delete request approved and asset deleted.');
    }

    public function rejectDeleteRequest($id)
    {
        $deleteRequest = DeleteRequest::findOrFail($id);
        $deleteRequest->delete();

        return redirect()->route('delete_requests.index')->with('success', 'Delete request rejected.');
    }
}
