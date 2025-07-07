<?php

namespace App\Http\Controllers\User\Report;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\Needle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use stdClass;

class TrackByNeedleController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'user_report_track_by_needle';
        $title = 'USER REPORT TRACK BY NEEDLE';

        HelperController::activityLog('OPEN USER REPORT TRACK BY NEEDLE', null, 'read', $request->ip(), $request->userAgent());

        return view('User.Report.TrackByNeedle.index', compact('title', 'page'));
    }

    public function data(Request $request)
    {
        $filter_period = $request->filter_period;
        $filter_status = $request->filter_status;

        $xx = HelperController::period($filter_period, $request->filter_range_date, $request->filter_daily, $request->filter_weekly, $request->filter_month, $request->filter_year, '');

        $data = [];

        $needle = Needle::with(['user', 'needle', 'master_status', 'style'])
            ->whereBetween('created_at', $xx->range)
            ->when($filter_status == 'all', function ($q) {
                $q->whereIn('master_status_id', [1, 2, 3, 4]);
            })
            ->when($filter_status != 'all', function ($q) use ($filter_status) {
                $q->where('master_status_id', $filter_status);
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
            $d->machine = $n->needle->machine;
            $d->style = $n->style->name;
            $d->srf = $n->style->srf;
            $d->description = $n->master_status->name;
            $data[] = $d;
        }

        return datatables()->of($data)
            ->make(true);
    }
}
