<?php

namespace App\Http\Controllers\User\Report;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\Needle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use stdClass;

class SummaryWipController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'user_report_summary_wip';
        $title = 'USER REPORT SUMMARY WIP';

        HelperController::activityLog('OPEN USER REPORT SUMMARY WIP', null, 'read', $request->ip(), $request->userAgent());

        return view('User.Report.SummaryWip.index', compact('title', 'page'));
    }

    public function data(Request $request)
    {
        $filter_period = $request->filter_period;

        $xx = HelperController::period($filter_period, $request->filter_range_date, $request->filter_daily, $request->filter_weekly, $request->filter_month, $request->filter_year, 'Summary WIP (Date)');

        $data = [];

        $dataNeedle = Needle::whereBetween('created_at', $xx->range)
            ->orderBy('created_at')
            ->get();

        $collectNeedle = collect($dataNeedle);

        $ys = Carbon::parse($xx->start);
        $ye = Carbon::parse($xx->end);

        while ($ys->lte($ye)) {
            $d = new stdClass;
            $tanggal = $ys->toDateString();

            $z = $collectNeedle->whereBetween('created_at', [$tanggal . ' 00:00:00', $tanggal . ' 23:59:59']);

            $d->tanggal = $tanggal;
            $d->deformed = $z->where('master_status_id', 1)->count();
            $d->routing = $z->where('master_status_id', 2)->count();
            $d->change = $z->where('master_status_id', 3)->count();
            $d->broken = $z->where('master_status_id', 4)->count();
            $d->total = $z->whereIn('master_status_id', [1, 2, 3, 4])->count();
            $data[] = $d;
            $ys->addDay();
        }

        return datatables()->of($data)
            ->make(true);
    }
}
