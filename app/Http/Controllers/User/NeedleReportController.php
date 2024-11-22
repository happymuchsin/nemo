<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\Approval;
use App\Models\MasterCounter;
use App\Models\MasterLine;
use App\Models\Needle;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use stdClass;

class NeedleReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'user_needle_report';
        $title = 'USER NEEDLE REPORT';

        HelperController::activityLog('OPEN USER NEEDLE REPORT', null, 'read', $request->ip(), $request->userAgent());

        $user_needle_report = 'active';

        $line = MasterLine::with(['area'])->get();
        $counter = MasterCounter::with(['area'])->get();

        return view('User.NeedleReport.index', compact('title', 'page', 'user_needle_report', 'line', 'counter'));
    }

    public function data(Request $request)
    {
        $id = $request->id;
        if ($id == 'needle_report_exchange') {
            $filter_date = $request->filter_date;
            $filter_line = $request->filter_line;
            $data = Needle::with(['style.buyer'])->join('master_statuses as ms', 'ms.id', 'needles.master_status_id')
                ->join('users as u', 'u.id', 'needles.user_id')
                ->join('master_lines as ml', 'ml.id', 'needles.master_line_id')
                ->leftJoin('master_needles as mn', 'mn.id', 'needles.master_needle_id')
                ->selectRaw('needles.created_at as created_at, ml.name as line, u.username as username, u.name as name, mn.brand as brand, mn.tipe as tipe, mn.size as size, ms.name as remark, needles.filename as filename, needles.ext as ext, needles.id as id, needles.user_id as user_id, master_line_id, master_style_id')
                ->whereDate('needles.created_at', $filter_date)
                ->whereIn('needles.master_status_id', [1, 2, 3, 4])
                ->when($filter_line != 'all', function ($q) use ($filter_line) {
                    $q->where('ml.id', $filter_line);
                })
                ->whereNull('ms.deleted_at')
                ->whereNull('u.deleted_at')
                ->whereNull('ml.deleted_at')
                ->whereNull('mn.deleted_at')
                ->orderBy('needles.created_at')
                ->get();
            return datatables()->of($data)
                ->addColumn('time', function ($q) {
                    return Carbon::parse($q->created_at)->format('H:i:s');
                })
                ->addColumn('user', function ($q) {
                    return $q->username . ' - ' . $q->name;
                })
                ->addColumn('buyer', function ($q) {
                    return $q->style ? $q->style->buyer->name : '';
                })
                ->addColumn('style', function ($q) {
                    return $q->style ? $q->style->name : '';
                })
                ->addColumn('gambar', function ($q) {
                    $c = Carbon::parse($q->created_at);
                    if (strlen($c->month) == 1) {
                        $month = '0' . $c->month;
                    } else {
                        $month = $c->month;
                    }
                    $h = '';
                    if ($q->filename) {
                        $gambar = asset("assets/uploads/needle/$c->year/$month/$q->id.$q->ext");
                    } else {
                        $a = Approval::where('needle_id', $q->id)->first();
                        if ($a) {
                            $gambar = asset("assets/uploads/needle/$c->year/$month/$a->id.$a->ext");
                        } else {
                            $aa = Approval::where('user_id', $q->user_id)->where('master_line_id', $q->master_line_id)->where('master_style_id', $q->master_style_id)->where('updated_at', $q->created_at)->first();
                            if ($aa) {
                                $gambar = asset("assets/uploads/needle/$c->year/$month/$aa->id.$aa->ext");
                            } else {
                                $gambar = asset('assets/img/altgambar.jpeg');
                            }
                        }
                    }
                    $h .= '<a href="#" onclick="poto(\'' . $gambar . '\')"><img src="' . $gambar . '" width="75px" /></a>';
                    return $h;
                })
                ->rawColumns(['gambar'])
                ->make(true);
        } else if ($id == 'needle_report_counter') {
            $filter_counter = $request->filter_counter;
            $data = [];
            $s = Stock::join('master_needles as mn', 'mn.id', 'stocks.master_needle_id')
                ->join('master_boxes as mb', 'mb.id', 'stocks.master_box_id')
                ->selectRaw('mb.name as box, brand, mn.tipe, size, sum(`in`) as `in`, sum(`out`) as `out`')
                ->where('stocks.master_counter_id', $filter_counter)
                ->where('stocks.is_clear', 'not')
                ->groupBy('master_box_id')
                ->get();
            foreach ($s as $s) {
                $d = new stdClass;
                $d->boxName = $s->box;
                $d->brand = $s->brand;
                $d->tipe = $s->tipe;
                $d->size = $s->size;
                $d->qty = $s->in - $s->out;
                $data[] = $d;
            }

            return datatables()->of($data)
                ->make(true);
        }
    }
}
