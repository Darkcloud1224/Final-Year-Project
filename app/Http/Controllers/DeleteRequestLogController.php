<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeleteRequestLog;

class DeleteRequestLogController extends Controller
{
    public function index()
    {
        $query = DeleteRequestLog::query();

        $query->orderBy('id', 'desc');

        $deleterequestlog = $query->get();
        return view('delete_request_log', compact('deleterequestlog'));
    }
}

