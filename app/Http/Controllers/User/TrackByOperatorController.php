<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\Needle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use stdClass;

class TrackByOperatorController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'user_track_by_operator';
        $title = 'USER TRACK BY OPERATOR';

        HelperController::activityLog('OPEN USER TRACK BY OPERATOR', null, 'read', $request->ip(), $request->userAgent());

        return view('User.TrackByOperator.index', compact('title', 'page'));
    }

    public function data(Request $request)
    {
        $period = $request->filter_period;
        $status = $request->filter_status;

        if ($period == 'range') {
            $range_date = explode(' - ', $request->filter_range_date);
            $start = $range_date[0] ? $range_date[0] : Carbon::today()->subMonth();
            $end = $range_date[1] ? $range_date[1] : Carbon::today();
            $range = ["$start 00:00:00", "$end 23:59:59"];
        } else if ($period == 'daily') {
            $filter_daily = $request->filter_daily;
            $range = ["$filter_daily 00:00:00", "$filter_daily 23:59:59"];
            $start = Carbon::parse($filter_daily);
            $end = Carbon::parse($filter_daily);
        } else if ($period == 'weekly') {
            $filter_weekly = $request->filter_weekly;
            $x = explode('-W', $filter_weekly);
            $year = $x[0];
            $week = $x[1];
            $start = Carbon::now()->setISODate($year, $week)->startOfWeek();
            $end = Carbon::now()->setISODate($year, $week)->endOfWeek();
            $range = [$start . ' 00:00:00', $end . ' 23:59:59'];
        } else if ($period == 'monthly') {
            $filter_month = $request->filter_month;
            $x = explode('-', $filter_month);
            $tahun = $x[0];
            $bulan = $x[1];
            $lastDay = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
            $range = ["$tahun-$bulan-01 00:00:00", "$tahun-$bulan-$lastDay 23:59:59"];
            $start = Carbon::parse("$tahun-$bulan-01");
            $end = Carbon::parse("$tahun-$bulan-$lastDay");
        } else if ($period == 'yearly') {
            $filter_year = $request->filter_year;
            $start = Carbon::parse("$filter_year-01-01");
            $end = Carbon::parse("$filter_year-12-31");
        }

        $data = [];

        $needle = Needle::with(['user', 'needle', 'master_status', 'style'])
            ->whereBetween('created_at', $range)
            ->when($status == 'all', function ($q) {
                $q->whereIn('master_status_id', [1, 2, 3, 4]);
            })
            ->when($status != 'all', function ($q) use ($status) {
                $q->where('master_status_id', $status);
            })
            ->get();
        foreach ($needle as $n) {
            $d = new stdClass;
            $d->tanggal = $n->created_at->format('Y-m-d H:i:s');
            $d->username = $n->user->username;
            $d->name = $n->user->name;
            $d->brand = $n->needle->brand;
            $d->tipe = $n->needle->tipe;
            $d->size = $n->needle->size;
            $d->code = $n->needle->code;
            $d->style = $n->style->name;
            $d->srf = $n->style->srf;
            $d->description = $n->master_status->name;
            $data[] = $d;
        }

        return datatables()->of($data)
            ->make(true);
    }
}
