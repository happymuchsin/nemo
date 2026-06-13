<?php

namespace App\Http\Controllers\Admin\Tools;

use App\Http\Controllers\ClosingController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ToolsDailyClosingController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'admin_tools_daily_closing';
        $title = 'ADMIN TOOLS DAILY CLOSING';

        HelperController::activityLog('OPEN ADMIN TOOLS DAILY CLOSING', '', 'read', $request->ip(), $request->userAgent());

        $admin_tools = 'menu-open';
        return view('Admin.Tools.DailyClosing.index', compact('title', 'page', 'admin_tools'));
    }

    public function save(Request $request)
    {
        $action = $request->action;
        $range_date = explode(' - ', $request->range_date);
        $start = Carbon::parse($range_date[0]);
        $end = Carbon::parse($range_date[1]);
        $now = Carbon::now();

        try {
            DB::beginTransaction();

            if ($action == 'recalculate') {
                ClosingController::generateStockReport($now, $start, $end, null);
            }

            DB::commit();
            return response()->json('Recalculate Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json('Recalculate Failed', 422);
        }
    }
}
