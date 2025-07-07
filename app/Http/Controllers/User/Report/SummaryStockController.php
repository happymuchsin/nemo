<?php

namespace App\Http\Controllers\User\Report;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\DailyClosing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use stdClass;

class SummaryStockController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'user_report_summary_stock';
        $title = 'USER REPORT SUMMARY STOCK';

        HelperController::activityLog('OPEN USER REPORT SUMMARY STOCK', null, 'read', $request->ip(), $request->userAgent());

        return view('User.Report.SummaryStock.index', compact('title', 'page'));
    }

    public function data(Request $request)
    {
        $filter_period = $request->filter_period;

        $xx = HelperController::period($filter_period, $request->filter_range_date, $request->filter_daily, $request->filter_weekly, $request->filter_month, $request->filter_year, 'Summary Stock');

        $data = [];

        $daily_closing = DailyClosing::whereBetween('tanggal', [$xx->start, $xx->end])->get();
        $collect_daily_closing = collect($daily_closing);

        $ys = Carbon::parse($xx->start);
        $ye = Carbon::parse($xx->end);

        while ($ys->lte($ye)) {
            $d = new stdClass;
            $tanggal = $ys->toDateString();

            $d->tanggal = $tanggal;
            $d->opening = $collect_daily_closing->where('tanggal', $tanggal)->sum('opening');
            $d->in = $collect_daily_closing->where('tanggal', $tanggal)->sum('in');
            $d->out = $collect_daily_closing->where('tanggal', $tanggal)->sum('out');
            $d->closing = $collect_daily_closing->where('tanggal', $tanggal)->sum('closing');
            $data[] = $d;
            $ys->addDay();
        }

        return datatables()->of($data)
            ->make(true);
    }
}
