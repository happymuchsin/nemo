<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\DailyClosing;
use App\Models\MasterNeedle;
use App\Models\Needle;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use stdClass;

class DailyStockController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'user_daily_stock';
        $title = 'USER DAILY STOCK';

        HelperController::activityLog('OPEN USER DAILY STOCK', null, 'read', $request->ip(), $request->userAgent());

        $master_needle = MasterNeedle::orderBy('tipe')->orderBy('size')->get();

        return view('User.DailyStock.index', compact('title', 'page', 'master_needle'));
    }

    public function data(Request $request)
    {
        $filter_period = $request->filter_period;
        $filter_status = $request->filter_status;
        $filter_daily = $request->filter_daily;
        $filter_weekly = $request->filter_weekly;
        $filter_monthly = $request->filter_month;
        $filter_yearly = $request->filter_year;

        $data = [];

        if ($filter_period == 'daily') {
            $range = ["$filter_daily 00:00:00", "$filter_daily 23:59:59"];
            $start = Carbon::parse($filter_daily);
            $end = Carbon::parse($filter_daily);
        } else if ($filter_period == 'weekly') {
            $x = explode('-W', $filter_weekly);
            $year = $x[0];
            $week = $x[1];
            $start = Carbon::now()->setISODate($year, $week)->startOfWeek();
            $end = Carbon::now()->setISODate($year, $week)->endOfWeek();
            $range = [$start . ' 00:00:00', $end . ' 23:59:59'];
        } else if ($filter_period == 'monthly') {
            $x = explode('-', $filter_monthly);
            $tahun = $x[0];
            $bulan = $x[1];
            $lastDay = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
            $range = ["$tahun-$bulan-01 00:00:00", "$tahun-$bulan-$lastDay 23:59:59"];
            $start = Carbon::parse("$tahun-$bulan-01");
            $end = Carbon::parse("$tahun-$bulan-$lastDay");
        } else if ($filter_period == 'yearly') {
            $range = ["$filter_yearly-01-01 00:00:00", "$filter_yearly-12-31 23:59:59"];
            $start = Carbon::parse("$filter_yearly-01-01");
            $end = Carbon::parse("$filter_yearly-12-31");
        }

        $daily_closing = DailyClosing::whereBetween('tanggal', [$start, $end])->get();
        $collect_daily_closing = collect($daily_closing);

        if ($collect_daily_closing->where('out', '!=', '0')->count() == 0) {
            return response()->json(['found' => 'not'], 200);
        }

        $issue = [];
        $add = [];

        $master_needle = MasterNeedle::orderBy('tipe')->orderBy('size')->get();
        foreach ($master_needle as $k => $m) {
            $d = new stdClass;
            $d->nomor = $k + 1;
            $d->brand = $m->brand;
            $d->tipe = $m->tipe;
            $d->size = $m->size;
            $d->code = $m->code;
            $dc = $collect_daily_closing->where('master_needle_id', $m->id);
            foreach ($dc->all() as $r) {
                $cout = 'xout' . str_replace('-', '', $r->tanggal);
                $cin = 'xin' . str_replace('-', '', $r->tanggal);
                $out = '';
                $in = '';
                if ($r->out) {
                    $issue[] = $r->tanggal;
                    $out = $r->out;
                }
                if ($r->in) {
                    $add[] = $r->tanggal;
                    $in = $r->in;
                }
                $d->$cout = $out;
                $d->$cin = $in;
            }
            $d->opening = $dc->where('tanggal', $start->toDateString())->value('opening') ?? 0;
            $d->closing = $dc->where('tanggal', $end->toDateString())->value('closing') ?? 0;
            $data[] = $d;
        }

        return response()->json([
            'found' => 'yes',
            'issue' => $issue,
            'add' => $add,
            'data' => $data,
        ], 200);
    }

    public function unduh(Request $request)
    {
        $filter_period = $request->filter_period;
        $filter_status = $request->filter_status;
        $filter_daily = $request->filter_daily;
        $filter_weekly = $request->filter_weekly;
        $filter_monthly = $request->filter_month;
        $filter_yearly = $request->filter_year;

        if ($filter_period == 'daily') {
            $range = ["$filter_daily 00:00:00", "$filter_daily 23:59:59"];
            $start = Carbon::parse($filter_daily);
            $end = Carbon::parse($filter_daily);
            $judul = 'Usage Needle ' . $filter_daily;
        } else if ($filter_period == 'weekly') {
            $x = explode('-W', $filter_weekly);
            $year = $x[0];
            $week = $x[1];
            $start = Carbon::now()->setISODate($year, $week)->startOfWeek();
            $end = Carbon::now()->setISODate($year, $week)->endOfWeek();
            $range = [$start . ' 00:00:00', $end . ' 23:59:59'];
            $judul = 'Usage Needle ' . $filter_weekly;
        } else if ($filter_period == 'monthly') {
            $x = explode('-', $filter_monthly);
            $tahun = $x[0];
            $bulan = $x[1];
            $lastDay = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
            $range = ["$tahun-$bulan-01 00:00:00", "$tahun-$bulan-$lastDay 23:59:59"];
            $start = Carbon::parse("$tahun-$bulan-01");
            $end = Carbon::parse("$tahun-$bulan-$lastDay");
            $judul = 'Usage Needle ' . $filter_monthly;
        } else if ($filter_period == 'yearly') {
            $range = ["$filter_yearly-01-01 00:00:00", "$filter_yearly-12-31 23:59:59"];
            $start = Carbon::parse("$filter_yearly-01-01");
            $end = Carbon::parse("$filter_yearly-12-31");
            $judul = 'Usage Needle ' . $filter_yearly;
        }

        try {
            $daily_closing = DailyClosing::whereBetween('tanggal', [$start, $end])->get();
            $collect_daily_closing = collect($daily_closing);

            if ($collect_daily_closing->where('out', '!=', '0')->count() == 0) {
                return response()->json(['found' => 'not'], 200);
            }

            $issue = [];
            $add = [];
            $data = [];

            $master_needle = MasterNeedle::orderBy('tipe')->orderBy('size')->get();
            foreach ($master_needle as $k => $m) {
                $d = new stdClass;
                $d->nomor = $k + 1;
                $d->brand = $m->brand;
                $d->tipe = $m->tipe;
                $d->size = $m->size;
                $d->code = $m->code;
                $dc = $collect_daily_closing->where('master_needle_id', $m->id);
                foreach ($dc->all() as $r) {
                    $cout = 'xout' . str_replace('-', '', $r->tanggal);
                    $cin = 'xin' . str_replace('-', '', $r->tanggal);
                    $out = '';
                    $in = '';
                    if ($r->out) {
                        $issue[] = $r->tanggal;
                        $out = $r->out;
                    }
                    if ($r->in) {
                        $add[] = $r->tanggal;
                        $in = $r->in;
                    }
                    $d->$cout = $out;
                    $d->$cin = $in;
                }
                $d->opening = $dc->where('tanggal', $start->toDateString())->value('opening') ?? 0;
                $d->closing = $dc->where('tanggal', $end->toDateString())->value('closing') ?? 0;
                $data[] = $d;
            }

            $issue_length = count($issue);
            $add_length = count($add);

            $sp = new Spreadsheet;
            $ws = $sp->getActiveSheet();

            $ws->mergeCells('A3:A5')->getCell('A3')->setValue('No');
            $ws->mergeCells('B3:B5')->getCell('B3')->setValue('Brand');
            $ws->mergeCells('C3:C5')->getCell('C3')->setValue('Type');
            $ws->mergeCells('D3:D5')->getCell('D3')->setValue('Size');
            $ws->mergeCells('E3:E5')->getCell('E3')->setValue('Code');
            $ws->mergeCells('F3:F3')->getCell('F3')->setValue('A');
            $ws->mergeCells('F4:F5')->getCell('F4')->setValue('QTY Opening Stock');
            $col = HelperController::numberToLetters($issue_length);
            $ws->mergeCells('G3:' . $col . '3')->getCell('G3')->setValue('B');

            $last = 3;
            foreach ($master_needle as $m) {
                $col1 = HelperController::numberToLetters($last);
                $ws->getCell($col1 . '4')->setValue($m->tipe . ' (' . $m->size . ')');
                $last++;
            }
            $ws->mergeCells('C3:' . $col1 . '3')->getCell('C3')->setValue('Needle Type');
            $ws->getStyle('A3:' . $col1 . '4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $ws->getStyle('A3:' . $col1 . '4')->getFont()->setBold(true)->setSize(14);
            $ws->getStyle('A3:' . $col1 . '4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $k = 4;
            $xz = ['Opening', 'Issue', 'Add', 'Closing'];
            for ($i = 0; $i < 4; $i++) {
                $k++;
                $last = 3;
                $ws->getCell("A$k")->setValue($xz[$i]);
                $total = 0;
                foreach ($collect_master_needle->all() as $n) {
                    $col2 = HelperController::numberToLetters($last);
                    $t = 0;
                    $dc = $collect_daily_closing->where('master_needle_id', $n->id);
                    if ($i == 0) {
                        $t = $dc->value('opening') ?? 0;
                    } else if ($i == 1) {
                        $t = $cn->where('master_needle_id', $n->id)->count();
                    } else if ($i == 2) {
                        $t = $dc->value('in') ?? 0;
                    } else if ($i == 3) {
                        $t = $dc->value('opening') + $dc->value('in') - $cn->where('master_needle_id', $n->id)->count();
                    }
                    $ws->getCell("{$col2}{$k}")->setValue($t);
                    $total += $t;
                    $last++;
                }
                $ws->getCell("B$k")->setValue($total);
            }
            $ws->getStyle("A5:{$col1}{$k}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $ws->getStyle("A5:{$col1}{$k}")->getFont()->setBold(true)->setSize(12);

            $ws->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $ws->mergeCells('A1:' . $col1 . '1')->getCell('A1')->setValue($judul)->getStyle()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);

            foreach ($ws->getColumnIterator() as $column) {
                $ws->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }

            $writer = new Xlsx($sp);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' .  $judul . '.xlsx"');
            $writer->save('php://output');
            exit();
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 422);
        }
    }
}
