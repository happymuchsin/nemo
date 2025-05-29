<?php

namespace App\Http\Controllers\User;

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
        $page = 'user_summary_stock';
        $title = 'USER SUMMARY STOCK';

        HelperController::activityLog('OPEN USER SUMMARY STOCK', null, 'read', $request->ip(), $request->userAgent());

        return view('User.SummaryStock.index', compact('title', 'page'));
    }

    public function data(Request $request)
    {
        $period = $request->filter_period;
        $filter_month = $request->filter_month;
        $tahun = $request->filter_year;

        if ($period == 'yearly') {
            $start = Carbon::parse("$tahun-01-01");
            $end = Carbon::parse("$tahun-12-31");
        } else {
            $x = explode('-', $filter_month);
            $tahun = $x[0];
            $bulan = $x[1];
            $lastDay = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
            $start = Carbon::parse("$tahun-$bulan-01");
            $end = Carbon::parse("$tahun-$bulan-$lastDay");
        }

        $data = [];

        $daily_closing = DailyClosing::selectRaw('tanggal, sum(opening) as opening, sum(`in`) as `in`, sum(`out`) as `out`, sum(closing) as closing')
            ->whereBetween('tanggal', [$start, $end])
            ->groupBy('tanggal')
            ->get();
        foreach ($daily_closing as $dc) {
            $d = new stdClass;
            $d->tanggal = $dc->tanggal;
            $d->opening = $dc->opening;
            $d->in = $dc->in;
            $d->out = $dc->out;
            $d->closing = $dc->closing;
            $data[] = $d;
        }

        return datatables()->of($data)
            ->make(true);
    }
}
