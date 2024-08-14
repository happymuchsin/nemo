<?php

namespace App\Http\Controllers\Admin\Tools;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ToolsActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'admin_tools_activity_log';
        $ngapain = 'OPEN ADMIN TOOLS ACTIVITY LOG';
        $title = 'ADMIN TOOLS ACTIVITY LOG';

        HelperController::activityLog('OPEN ADMIN TOOLS ACTIVITY LOG', 'activity_logs', 'read', $request->ip(), $request->userAgent());

        $tools_log = 'active';
        $admin_tools = 'menu-open';

        return view('Admin.Tools.ActivityLog.index', compact('title', 'page', 'ngapain', 'tools_log', 'admin_tools'));
    }

    public function data(Request $request)
    {
        $data = ActivityLog::when($request->start && $request->end, function ($q) use ($request) {
            $q->whereBetween('created_at', [$request->start, $request->end]);
        })
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();
        return datatables()->of($data)
            ->editColumn('created_at', function ($q) {
                return date('Y-m-d H:i:s', strtotime($q->created_at));
            })
            ->make(true);
    }

    public function hapus(Request $request)
    {
        HelperController::activityLog("TRUNCATE TOOLS ACTIVITY LOG", 'activity_logs', 'truncate', $request->ip(), $request->userAgent());
        ActivityLog::truncate();
        return response()->json('Log Cleared', 200);
    }
}
