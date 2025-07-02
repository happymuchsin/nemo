<?php

namespace App\Http\Controllers\User\Report;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\DailyClosing;
use App\Models\MasterNeedle;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
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
        $page = 'user_report_daily_stock';
        $title = 'USER REPORT NEEDLE STOCK';

        HelperController::activityLog('OPEN USER REPORT NEEDLE STOCK', null, 'read', $request->ip(), $request->userAgent());

        $master_needle = MasterNeedle::orderBy('tipe')->orderBy('size')->get();

        return view('User.Report.DailyStock.index', compact('title', 'page', 'master_needle'));
    }

    public function data(Request $request)
    {
        $filter_period = $request->filter_period;

        $data = [];

        if ($filter_period == 'range') {
            $range_date = explode(' - ', $request->filter_range_date);
            $start = $range_date[0] ? Carbon::parse($range_date[0]) : Carbon::today()->subMonth();
            $end = $range_date[1] ? Carbon::parse($range_date[1]) : Carbon::today();
            $range = ["$start 00:00:00", "$end 23:59:59"];
            $ys = $range_date[0] ? Carbon::parse($range_date[0]) : Carbon::today()->subMonth();
            $ye = $range_date[1] ? Carbon::parse($range_date[1]) : Carbon::today();
        } else if ($filter_period == 'daily') {
            $filter_daily = $request->filter_daily;
            $range = ["$filter_daily 00:00:00", "$filter_daily 23:59:59"];
            $start = Carbon::parse($filter_daily);
            $end = Carbon::parse($filter_daily);
            $ys = Carbon::parse($filter_daily);
            $ye = Carbon::parse($filter_daily);
        } else if ($filter_period == 'weekly') {
            $filter_weekly = $request->filter_weekly;
            $x = explode('-W', $filter_weekly);
            $year = $x[0];
            $week = $x[1];
            $start = Carbon::now()->setISODate($year, $week)->startOfWeek();
            $end = Carbon::now()->setISODate($year, $week)->endOfWeek();
            $ys = Carbon::now()->setISODate($year, $week)->startOfWeek();
            $ye = Carbon::now()->setISODate($year, $week)->endOfWeek();
            $range = [$start . ' 00:00:00', $end . ' 23:59:59'];
        } else if ($filter_period == 'monthly') {
            $filter_monthly = $request->filter_month;
            $x = explode('-', $filter_monthly);
            $tahun = $x[0];
            $bulan = $x[1];
            $lastDay = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
            $range = ["$tahun-$bulan-01 00:00:00", "$tahun-$bulan-$lastDay 23:59:59"];
            $start = Carbon::parse("$tahun-$bulan-01");
            $end = Carbon::parse("$tahun-$bulan-$lastDay");
            $ys = Carbon::parse("$tahun-$bulan-01");
            $ye = Carbon::parse("$tahun-$bulan-$lastDay");
        } else if ($filter_period == 'yearly') {
            $filter_yearly = $request->filter_year;
            $range = ["$filter_yearly-01-01 00:00:00", "$filter_yearly-12-31 23:59:59"];
            $start = Carbon::parse("$filter_yearly-01-01");
            $end = Carbon::parse("$filter_yearly-12-31");
            $ys = Carbon::parse("$filter_yearly-01-01");
            $ye = Carbon::parse("$filter_yearly-12-31");
        }

        $daily_closing = DailyClosing::whereBetween('tanggal', [$start, $end])->get();
        $collect_daily_closing = collect($daily_closing);

        $issue = [];
        $add = [];

        while ($ys->lte($ye)) {
            $tanggal = $ys->toDateString();
            $issue[] = $tanggal;
            $add[] = $tanggal;
            $ys->addDay();
        }

        $master_needle = MasterNeedle::orderBy('tipe')->orderBy('size')->get();
        foreach ($master_needle as $k => $m) {
            $d = new stdClass;
            $d->nomor = $k + 1;
            $d->brand = $m->brand;
            $d->tipe = $m->tipe;
            $d->size = $m->size;
            $d->code = $m->code;
            $dc = $collect_daily_closing->where('master_needle_id', $m->id);
            foreach ($issue as $i) {
                $dc2 = $dc->where('tanggal', $i);
                $cout = 'xout' . str_replace('-', '', $i);
                $cin = 'xin' . str_replace('-', '', $i);
                $out = '';
                $in = '';
                foreach ($dc2->all() as $r) {
                    if ($r->out > 0) {
                        $out = $r->out;
                    }
                    if ($r->in > 0) {
                        $in = $r->in;
                    }
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

        if ($filter_period == 'range') {
            $range_date = explode(' - ', $request->filter_range_date);
            $start = $range_date[0] ? Carbon::parse($range_date[0]) : Carbon::today()->subMonth();
            $end = $range_date[1] ? Carbon::parse($range_date[1]) : Carbon::today();
            $range = ["$start 00:00:00", "$end 23:59:59"];
            $ys = $range_date[0] ? Carbon::parse($range_date[0]) : Carbon::today()->subMonth();
            $ye = $range_date[1] ? Carbon::parse($range_date[1]) : Carbon::today();
            $judul = 'Needle Stock ' . $start->format('Y-m-d') . ' - ' . $end->format('Y-m-d');
        } else if ($filter_period == 'daily') {
            $filter_daily = $request->filter_daily;
            $range = ["$filter_daily 00:00:00", "$filter_daily 23:59:59"];
            $start = Carbon::parse($filter_daily);
            $end = Carbon::parse($filter_daily);
            $ys = Carbon::parse($filter_daily);
            $ye = Carbon::parse($filter_daily);
            $judul = 'Needle Stock ' . $filter_daily;
        } else if ($filter_period == 'weekly') {
            $filter_weekly = $request->filter_weekly;
            $x = explode('-W', $filter_weekly);
            $year = $x[0];
            $week = $x[1];
            $start = Carbon::now()->setISODate($year, $week)->startOfWeek();
            $end = Carbon::now()->setISODate($year, $week)->endOfWeek();
            $ys = Carbon::now()->setISODate($year, $week)->startOfWeek();
            $ye = Carbon::now()->setISODate($year, $week)->endOfWeek();
            $range = [$start . ' 00:00:00', $end . ' 23:59:59'];
            $judul = 'Needle Stock ' . $filter_weekly;
        } else if ($filter_period == 'monthly') {
            $filter_monthly = $request->filter_month;
            $x = explode('-', $filter_monthly);
            $tahun = $x[0];
            $bulan = $x[1];
            $lastDay = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
            $range = ["$tahun-$bulan-01 00:00:00", "$tahun-$bulan-$lastDay 23:59:59"];
            $start = Carbon::parse("$tahun-$bulan-01");
            $end = Carbon::parse("$tahun-$bulan-$lastDay");
            $ys = Carbon::parse("$tahun-$bulan-01");
            $ye = Carbon::parse("$tahun-$bulan-$lastDay");
            $judul = 'Needle Stock ' . $filter_monthly;
        } else if ($filter_period == 'yearly') {
            $filter_yearly = $request->filter_year;
            $range = ["$filter_yearly-01-01 00:00:00", "$filter_yearly-12-31 23:59:59"];
            $start = Carbon::parse("$filter_yearly-01-01");
            $end = Carbon::parse("$filter_yearly-12-31");
            $ys = Carbon::parse("$filter_yearly-01-01");
            $ye = Carbon::parse("$filter_yearly-12-31");
            $judul = 'Needle Stock ' . $filter_yearly;
        }

        try {
            $daily_closing = DailyClosing::whereBetween('tanggal', [$start, $end])->get();
            $collect_daily_closing = collect($daily_closing);

            $issue = [];
            $add = [];
            $data = [];

            while ($ys->lte($ye)) {
                $tanggal = $ys->toDateString();
                $issue[] = $tanggal;
                $add[] = $tanggal;
                $ys->addDay();
            }

            $total = [];
            $master_needle = MasterNeedle::orderBy('tipe')->orderBy('size')->get();
            foreach ($master_needle as $k => $m) {
                $d = new stdClass;
                $d->nomor = $k + 1;
                $d->brand = $m->brand;
                $d->tipe = $m->tipe;
                $d->size = $m->size;
                $d->code = $m->code;
                $dc = $collect_daily_closing->where('master_needle_id', $m->id);
                foreach ($issue as $i) {
                    $dc2 = $dc->where('tanggal', $i);
                    $cout = 'xout' . str_replace('-', '', $i);
                    $cin = 'xin' . str_replace('-', '', $i);
                    $out = '';
                    $in = '';
                    foreach ($dc2->all() as $r) {
                        if ($r->out > 0) {
                            $out = $r->out;
                        }
                        if ($r->in > 0) {
                            $in = $r->in;
                        }
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
            $last = 0;
            if ($issue_length > 0) {
                $last = 6;
                $colIssue = HelperController::numberToLetters($last + $issue_length);
                $ws->mergeCells('G3:' . $colIssue . '3')->getCell('G3')->setValue('B');
                $ws->mergeCells('G4:' . $colIssue . '4')->getCell('G4')->setValue('Issue to Operator');
                foreach ($issue as $i) {
                    $last++;
                    $col = HelperController::numberToLetters($last);
                    $ws->getCell($col . '5')->setValue($i);
                }
            }
            if ($add_length > 0) {
                $alast = HelperController::numberToLetters($last + 1);
                if ($issue_length == 0) {
                    $last = 6;
                    $alast = 'G';
                }
                $colAdd = HelperController::numberToLetters($last + $add_length);
                $ws->mergeCells($alast . '3:' . $colAdd . '3')->getCell($alast . '3')->setValue('C');
                $ws->mergeCells($alast . '4:' . $colAdd . '4')->getCell($alast . '4')->setValue('Add');
                foreach ($add as $a) {
                    $last++;
                    $col = HelperController::numberToLetters($last);
                    $ws->getCell($col . '5')->setValue($a);
                }
            }

            $col = HelperController::numberToLetters($last + 1);
            $ws->mergeCells($col . '3:' . $col . '3')->getCell($col . '3')->setValue('(A - B + C)');
            $ws->mergeCells($col . '4:' . $col . '5')->getCell($col . '4')->setValue('QTY Closing Stock');
            $ws->getStyle('A3:' . $col . '5')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $ws->getStyle('A3:' . $col . '5')->getFont()->setBold(true)->setSize(14);
            $ws->getStyle('A3:' . $col . '5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $ws->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $ws->mergeCells('A1:' . $col . '1')->getCell('A1')->setValue($judul)->getStyle()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $kStart = 5;
            $k = 5;
            foreach ($data as $d) {
                $k++;
                $ws->getCell("A$k")->setValue($d->nomor);
                $ws->getCell("B$k")->setValue($d->brand);
                $ws->getCell("C$k")->setValue($d->tipe);
                $ws->getCell("D$k")->setValue($d->size);
                $ws->getCell("E$k")->setValue($d->code);
                $ws->getCell("F$k")->setValue($d->opening);
                $last = 0;
                if ($issue_length > 0) {
                    $last = 6;
                    foreach ($issue as $i) {
                        $cout = 'xout' . str_replace('-', '', $i);
                        $last++;
                        $col = HelperController::numberToLetters($last);
                        $ws->getCell($col . $k)->setValue($d->$cout);
                    }
                }
                if ($add_length > 0) {
                    if ($issue_length == 0) {
                        $last = 6;
                    }
                    foreach ($add as $a) {
                        $cin = 'xin' . str_replace('-', '', $a);
                        $last++;
                        $col = HelperController::numberToLetters($last);
                        $ws->getCell($col . $k)->setValue($d->$cin);
                    }
                }
                $col = HelperController::numberToLetters($last + 1);
                $ws->getCell($col . $k)->setValue($d->closing);
                $ws->getStyle("A$k:{$col}{$k}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            }

            $colTotal = HelperController::numberToLetters($last + 1);
            $kEnd = $k + 1;
            $ws->getStyle("A$kEnd:{$colTotal}{$kEnd}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $ws->mergeCells("A$kEnd:E$kEnd")->getCell("A$kEnd")->setValue('Total')->getStyle()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $ws->getCell("F$kEnd")->setValue("=SUM(F$kStart:F{$kEnd})");
            $ws->getCell("{$colTotal}{$kEnd}")->setValue("=SUM({$colTotal}{$kStart}:{$colTotal}{$kEnd})");
            $startIndex = Coordinate::columnIndexFromString('G');
            $endIndex = Coordinate::columnIndexFromString($colTotal);

            for ($colIndex = $startIndex; $colIndex <= $endIndex; $colIndex++) {
                $colLetter = HelperController::numberToLetters($colIndex);
                $ws->getCell("{$colLetter}{$kEnd}")->setValue("=SUM({$colLetter}{$kStart}:{$colLetter}{$kEnd})");
            }

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
