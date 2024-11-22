<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\MasterStatus;
use App\Models\Needle;
use App\Models\Stock;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use stdClass;

class ReportController extends Controller
{
    public function __construct()
    {

        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'user_report';
        $title = 'USER REPORT';

        HelperController::activityLog('OPEN USER REPORT', null, 'read', $request->ip(), $request->userAgent());

        $user_report = 'active';

        $x = MasterStatus::where('name', '!=', 'RETURN')->get();
        $master_status = [];
        foreach ($x as $x) {
            $d = new stdClass;
            $d->id = $x->id;
            $d->name = ucwords(strtolower($x->name));
            $d->kolom = str_replace(' ', '_', strtolower($x->name));
            $master_status[] = $d;
        }

        if (date('m') == '01' || date('m') == '02' || date('m') == '03') {
            $quarter = 'Q1';
        } else if (date('m') == '04' || date('m') == '05' || date('m') == '06') {
            $quarter = 'Q2';
        } else if (date('m') == '07' || date('m') == '08' || date('m') == '09') {
            $quarter = 'Q3';
        } else if (date('m') == '10' || date('m') == '11' || date('m') == '12') {
            $quarter = 'Q4';
        } else {
            $quarter = '';
        }

        if (date('m') == '01' || date('m') == '02' || date('m') == '03' || date('m') == '04' || date('m') == '05' || date('m') == '06') {
            $half = 'H1';
        } else if (date('m') == '07' || date('m') == '08' || date('m') == '09' || date('m') == '10' || date('m') == '11' || date('m') == '12') {
            $half = 'H2';
        } else {
            $half = '';
        }

        return view('User.Report.index', compact('title', 'page', 'user_report', 'master_status', 'quarter', 'half'));
    }

    public function data(Request $request)
    {
        $id = $request->id;

        $ms = [];
        $dms = MasterStatus::where('name', '!=', 'RETURN')->get();
        foreach ($dms as $dms) {
            $d = new stdClass;
            $d->id = $dms->id;
            $d->name = str_replace(' ', '_', strtolower($dms->name));
            $ms[] = $d;
        }

        if ($id == 'report_daily') {
            $filter_date = $request->filter_date;
            $data = Needle::with(['user', 'line', 'needle', 'master_status', 'style.buyer'])
                ->whereDate('created_at', $filter_date)
                ->orderBy('created_at')
                ->get();
            return datatables()->of($data)
                ->addColumn('time', function ($q) {
                    return Carbon::parse($q->created_at)->format('H:i:s');
                })
                ->addColumn('line', function ($q) {
                    return $q->line->name;
                })
                ->addColumn('user', function ($q) {
                    return $q->user->username . ' - ' . $q->user->name;
                })
                ->addColumn('buyer', function ($q) {
                    return $q->style->buyer ? $q->style->buyer->name : '';
                })
                ->addColumn('style', function ($q) {
                    return $q->style ? $q->style->name : '';
                })
                ->addColumn('brand', function ($q) {
                    return $q->needle ? $q->needle->brand : '';
                })
                ->addColumn('tipe', function ($q) {
                    return $q->needle ? $q->needle->tipe : '';
                })
                ->addColumn('size', function ($q) {
                    return $q->needle ? $q->needle->size : '';
                })
                ->addColumn('remark', function ($q) {
                    return $q->master_status->name;
                })
                ->make(true);
        } else if ($id == 'report_weekly') {
            $filter_week = $request->filter_week;
            $x = explode('-W', $filter_week);
            $year = $x[0];
            $week = $x[1];
            $start = Carbon::now()->setISODate($year, $week)->startOfWeek();
            $end = Carbon::now()->setISODate($year, $week)->endOfWeek();
            $period = CarbonPeriod::create($start, $end);

            $dataNeedle = Needle::whereBetween('created_at', [$start, $end])
                ->orderBy('created_at')
                ->get();

            $collectNeedle = collect($dataNeedle);

            $dataStock = Stock::whereBetween('created_at', [$start, $end])
                ->orderBy('created_at')
                ->get();

            $collectStock = collect($dataStock);

            $data = [];
            foreach ($period as $p) {
                $d = new stdClass;
                $tanggal = $p->toDateString();
                $d->date = $tanggal;
                foreach ($ms as $m) {
                    $kol = $m->name;
                    $id = $m->id;
                    $d->$kol = $collectNeedle
                        ->whereBetween('created_at', [$tanggal . ' 00:00:00', $tanggal . '23:59:59'])
                        ->where('master_status_id', $id)
                        ->count();
                }
                $in = $collectStock->whereBetween('created_at', [$tanggal . ' 00:00:00', $tanggal . '23:59:59'])->sum('in');
                $out = $collectStock->whereBetween('created_at', [$tanggal . ' 00:00:00', $tanggal . '23:59:59'])->sum('out');
                $d->stock = $in - $out;
                $data[] = $d;
            }
            return datatables()->of($data)
                ->make(true);
        } else if ($id == 'report_monthly') {
            $filter_month = $request->filter_month;
            $x = explode('-', $filter_month);
            $year = $x[0];
            $month = $x[1];

            $dataNeedle = Needle::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->orderBy('created_at')
                ->get();

            $collectNeedle = collect($dataNeedle);

            $dataStock = Stock::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->orderBy('created_at')
                ->get();

            $collectStock = collect($dataStock);

            $data = [];
            for ($i = 1; $i <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $i++) {
                $d = new stdClass;
                $tanggal = Carbon::parse("$year-$month-$i")->toDateString();
                $d->date = $tanggal;
                foreach ($ms as $m) {
                    $kol = $m->name;
                    $id = $m->id;
                    $d->$kol = $collectNeedle
                        ->whereBetween('created_at', [$tanggal . ' 00:00:00', $tanggal . '23:59:59'])
                        ->where('master_status_id', $id)
                        ->count();
                }
                $in = $collectStock->whereBetween('created_at', [$tanggal . ' 00:00:00', $tanggal . '23:59:59'])->sum('in');
                $out = $collectStock->whereBetween('created_at', [$tanggal . ' 00:00:00', $tanggal . '23:59:59'])->sum('out');
                $d->stock = $in - $out;
                $data[] = $d;
            }
            return datatables()->of($data)
                ->make(true);
        } else if ($id == 'report_quarterly') {
            $filter_year = $request->filter_year;
            $filter_quarter = $request->filter_quarter;
            $start = '';
            $end = '';
            if ($filter_quarter == 'Q1') {
                $s = 1;
                $e = 3;
                $start = date('Y-m-d', strtotime($filter_year . '-01-01'));
                $end = date('Y-m-d', strtotime($filter_year . '-03-31'));
            } else if ($filter_quarter == 'Q2') {
                $s = 4;
                $e = 6;
                $start = date('Y-m-d', strtotime($filter_year . '-04-01'));
                $end = date('Y-m-d', strtotime($filter_year . '-06-30'));
            } else if ($filter_quarter == 'Q3') {
                $s = 7;
                $e = 9;
                $start = date('Y-m-d', strtotime($filter_year . '-07-01'));
                $end = date('Y-m-d', strtotime($filter_year . '-09-30'));
            } else if ($filter_quarter == 'Q4') {
                $s = 10;
                $e = 12;
                $start = date('Y-m-d', strtotime($filter_year . '-10-01'));
                $end = date('Y-m-d', strtotime($filter_year . '-12-31'));
            }

            $dataNeedle = Needle::whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])
                ->orderBy('created_at')
                ->get();

            $collectNeedle = collect($dataNeedle);

            $dataStock = Stock::whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])
                ->orderBy('created_at')
                ->get();

            $collectStock = collect($dataStock);

            $data = [];
            for ($i = $s; $i <= $e; $i++) {
                $d = new stdClass;
                $start = Carbon::now()->setMonth($i)->startOfMonth();
                $end = Carbon::now()->setMonth($i)->endOfMonth();
                $d->date = date('F', strtotime($filter_year . '-' . $i . '-01'));
                foreach ($ms as $m) {
                    $kol = $m->name;
                    $id = $m->id;
                    $d->$kol = $collectNeedle
                        ->whereBetween('created_at', [$start . ' 00:00:00', $end  . ' 23:59:59'])
                        ->where('master_status_id', $id)
                        ->count();
                }
                $in = $collectStock->whereBetween('created_at', [$start . ' 00:00:00', $end  . ' 23:59:59'])->sum('in');
                $out = $collectStock->whereBetween('created_at', [$start . ' 00:00:00', $end  . ' 23:59:59'])->sum('out');
                $d->stock = $in - $out;
                $data[] = $d;
            }
            return datatables()->of($data)
                ->make(true);
        } else if ($id == 'report_half') {
            $filter_year = $request->filter_year;
            $filter_half = $request->filter_half;
            $start = '';
            $end = '';
            if ($filter_half == 'H1') {
                $s = 1;
                $e = 6;
                $start = date('Y-m-d', strtotime($filter_year . '-01-01'));
                $end = date('Y-m-d', strtotime($filter_year . '-06-30'));
            } else if ($filter_half == 'H2') {
                $s = 7;
                $e = 12;
                $start = date('Y-m-d', strtotime($filter_year . '-07-01'));
                $end = date('Y-m-d', strtotime($filter_year . '-12-31'));
            }

            $dataNeedle = Needle::whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])
                ->orderBy('created_at')
                ->get();

            $collectNeedle = collect($dataNeedle);

            $dataStock = Stock::whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])
                ->orderBy('created_at')
                ->get();

            $collectStock = collect($dataStock);

            $data = [];
            for ($i = $s; $i <= $e; $i++) {
                $d = new stdClass;
                $start = Carbon::now()->setMonth($i)->startOfMonth();
                $end = Carbon::now()->setMonth($i)->endOfMonth();
                $d->date = date('F', strtotime($filter_year . '-' . $i . '-01'));
                foreach ($ms as $m) {
                    $kol = $m->name;
                    $id = $m->id;
                    $d->$kol = $collectNeedle
                        ->whereBetween('created_at', [$start . ' 00:00:00', $end  . ' 23:59:59'])
                        ->where('master_status_id', $id)
                        ->count();
                }
                $in = $collectStock->whereBetween('created_at', [$start . ' 00:00:00', $end  . ' 23:59:59'])->sum('in');
                $out = $collectStock->whereBetween('created_at', [$start . ' 00:00:00', $end  . ' 23:59:59'])->sum('out');
                $d->stock = $in - $out;
                $data[] = $d;
            }
            return datatables()->of($data)
                ->make(true);
        } else if ($id == 'report_yearly') {
            $filter_year = $request->filter_year;
            $start = Carbon::now()->setYear($filter_year)->startOfYear();
            $end = Carbon::now()->setYear($filter_year)->endOfYear();

            $dataNeedle = Needle::whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])
                ->orderBy('created_at')
                ->get();

            $collectNeedle = collect($dataNeedle);

            $dataStock = Stock::whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])
                ->orderBy('created_at')
                ->get();

            $collectStock = collect($dataStock);

            $data = [];
            for ($i = 1; $i <= 12; $i++) {
                $d = new stdClass;
                $start = Carbon::now()->setMonth($i)->startOfMonth();
                $end = Carbon::now()->setMonth($i)->endOfMonth();
                $d->date = date('F', strtotime($filter_year . '-' . $i . '-01'));
                foreach ($ms as $m) {
                    $kol = $m->name;
                    $id = $m->id;
                    $d->$kol = $collectNeedle
                        ->whereBetween('created_at', [$start . ' 00:00:00', $end  . ' 23:59:59'])
                        ->where('master_status_id', $id)
                        ->count();
                }
                $in = $collectStock->whereBetween('created_at', [$start . ' 00:00:00', $end  . ' 23:59:59'])->sum('in');
                $out = $collectStock->whereBetween('created_at', [$start . ' 00:00:00', $end  . ' 23:59:59'])->sum('out');
                $d->stock = $in - $out;
                $data[] = $d;
            }
            return datatables()->of($data)
                ->make(true);
        }
    }
}
