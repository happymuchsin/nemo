<?php

namespace App\Http\Controllers\User\Report;

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

class UsageNeedleController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'user_report_usage_needle';
        $title = 'USER REPORT USAGE NEEDLE ALL OPERATOR';

        HelperController::activityLog('OPEN USER REPORT USAGE NEEDLE ALL OPERATOR', null, 'read', $request->ip(), $request->userAgent());

        $master_needle = MasterNeedle::orderBy('tipe')->orderBy('size')->get();

        return view('User.Report.UsageNeedle.index', compact('title', 'page', 'master_needle'));
    }

    public function data(Request $request)
    {
        $mode = $request->mode;
        $filter_period = $request->filter_period;
        $filter_status = $request->filter_status;

        $data = [];

        $xx = HelperController::period($filter_period, $request->filter_range_date, $request->filter_daily, $request->filter_weekly, $request->filter_month, $request->filter_year, 'Usage Needle All Operator');

        $master_needle = MasterNeedle::orderBy('tipe')->orderBy('size')->get();
        $collect_master_needle = collect($master_needle);

        $daily_closing = DailyClosing::whereBetween('tanggal', [$xx->start, $xx->end])->get();
        $collect_daily_closing = collect($daily_closing);

        $needle = Needle::whereBetween('created_at', $xx->range)
            ->when($filter_status == 'all', function ($q) {
                $q->whereIn('master_status_id', [1, 2, 3, 4]);
            })
            ->when($filter_status != 'all', function ($q) use ($filter_status) {
                $q->where('master_status_id', $filter_status);
            })
            ->get();
        $cn = collect($needle);

        if ($mode == 'data') {
            $user = User::with(['division', 'position', 'placement' => function ($q) {
                $q->with(['line.area', 'counter.area'])->where('reff', 'line');
            }])
                ->when(env('APP_ENV') != 'local', function ($q) {
                    $q->where('name', '!=', 'developer');
                })
                ->whereHas('placement', function ($q) {
                    $q->where('reff', 'line');
                })
                ->get();
            foreach ($user as $u) {
                $d = new stdClass;
                $d->username = $u->username;
                $d->name = $u->name;
                $d->division = $u->division->name;
                $d->position = $u->position->name;
                $tipe = '';
                $location = '';
                $counter = '';
                if ($u->placement) {
                    $tipe = strtoupper($u->placement->reff);
                    if ($u->placement->line) {
                        $location = $u->placement->line->area->name . ' - ' . $u->placement->line->name;
                    }
                    if ($u->placement->counter) {
                        $counter = $u->placement->counter->area->name . ' - ' . $u->placement->counter->name;
                    }
                }
                $d->tipe = $tipe;
                $d->location = $location;
                $d->counter = $counter;
                $total = 0;
                foreach ($collect_master_needle->all() as $n) {
                    $c = 'x' . $n->id;
                    $t = $cn->where('user_id', $u->id)->where('master_needle_id', $n->id)->count();
                    $d->$c = $t;
                    $total += $t;
                }
                $d->total = $total;
                $data[] = $d;
            }
        } else if ($mode == 'summary') {
            $xz = ['Opening', 'Issue', 'Add', 'Closing'];
            for ($i = 0; $i < 4; $i++) {
                $d = new stdClass;
                $d->x = $xz[$i];
                $total = 0;
                foreach ($collect_master_needle->all() as $n) {
                    $c = 'x' . $n->id;
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
                    $d->$c = $t;
                    $total += $t;
                }
                $d->total = $total;
                $data[] = $d;
            }
        }

        return datatables()->of($data)
            ->make(true);
    }

    public function unduh(Request $request)
    {
        $filter_period = $request->filter_period;
        $filter_status = $request->filter_status;

        $xx = HelperController::period($filter_period, $request->filter_range_date, $request->filter_daily, $request->filter_weekly, $request->filter_month, $request->filter_year, 'Usage Needle All Operator');

        try {
            $master_needle = MasterNeedle::orderBy('tipe')->orderBy('size')->get();
            $collect_master_needle = collect($master_needle);

            $master_needle = MasterNeedle::orderBy('tipe')->orderBy('size')->get();

            $daily_closing = DailyClosing::whereBetween('tanggal', [$xx->start, $xx->end])->get();
            $collect_daily_closing = collect($daily_closing);

            $needle = Needle::whereBetween('created_at', $xx->range)
                ->when($filter_status == 'all', function ($q) {
                    $q->whereIn('master_status_id', [1, 2, 3, 4]);
                })
                ->when($filter_status != 'all', function ($q) use ($filter_status) {
                    $q->where('master_status_id', $filter_status);
                })
                ->get();
            $cn = collect($needle);

            $sp = new Spreadsheet;
            $ws = $sp->getActiveSheet();

            $ws->mergeCells('A3:A4')->getCell('A3')->setValue('#');
            $ws->mergeCells('B3:B4')->getCell('B3')->setValue('Total');
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

            $kk = $k += 2;
            $ws->mergeCells("A$kk:A" . $kk + 1)->getCell("A$kk")->setValue('No');
            $ws->mergeCells("B$kk:B" . $kk + 1)->getCell("B$kk")->setValue('Username');
            $ws->mergeCells("C$kk:C" . $kk + 1)->getCell("C$kk")->setValue('Name');
            $ws->mergeCells("D$kk:D" . $kk + 1)->getCell("D$kk")->setValue('Division');
            $ws->mergeCells("E$kk:E" . $kk + 1)->getCell("E$kk")->setValue('Position');
            $ws->mergeCells("F$kk:F" . $kk + 1)->getCell("F$kk")->setValue('Type');
            $ws->mergeCells("G$kk:G" . $kk + 1)->getCell("G$kk")->setValue('Location');
            $ws->mergeCells("H$kk:H" . $kk + 1)->getCell("H$kk")->setValue('Counter');
            $ws->mergeCells("I$kk:I" . $kk + 1)->getCell("I$kk")->setValue('Total');
            $last = 10;
            foreach ($master_needle as $m) {
                $col1 = HelperController::numberToLetters($last);
                $ws->getCell($col1 . $kk + 1)->setValue($m->tipe . ' (' . $m->size . ')');
                $last++;
            }
            $ws->mergeCells("J$kk:" . $col1 . $kk)->getCell("J$kk")->setValue('Needle Type');
            $ws->getStyle("A$kk:" . $col1 . $kk + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $ws->getStyle("A$kk:" . $col1 . $kk + 1)->getFont()->setBold(true)->setSize(14);
            $ws->getStyle("A$kk:" . $col1 . $kk + 1)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $user = User::with(['division', 'position', 'placement' => function ($q) {
                $q->with(['line.area', 'counter.area'])->where('reff', 'line');
            }])
                ->when(env('APP_ENV') != 'local', function ($q) {
                    $q->where('name', '!=', 'developer');
                })
                ->whereHas('placement', function ($q) {
                    $q->where('reff', 'line');
                })
                ->get();

            $kkk = $kk + 1;
            $ii = 0;
            foreach ($user as $u) {
                $ii++;
                $kkk++;
                $ws->getCell("A$kkk")->setValue($ii);
                $ws->getCell("B$kkk")->setValue($u->username);
                $ws->getCell("C$kkk")->setValue($u->name);
                $ws->getCell("D$kkk")->setValue($u->division->name);
                $ws->getCell("E$kkk")->setValue($u->position->name);
                $tipe = '';
                $location = '';
                $counter = '';
                if ($u->placement) {
                    $tipe = strtoupper($u->placement->reff);
                    if ($u->placement->line) {
                        $location = $u->placement->line->area->name . ' - ' . $u->placement->line->name;
                    }
                    if ($u->placement->counter) {
                        $counter = $u->placement->counter->area->name . ' - ' . $u->placement->counter->name;
                    }
                }
                $ws->getCell("F$kkk")->setValue($tipe);
                $ws->getCell("G$kkk")->setValue($location);
                $ws->getCell("H$kkk")->setValue($counter);
                $total = 0;
                $last = 10;
                foreach ($collect_master_needle->all() as $n) {
                    $col2 = HelperController::numberToLetters($last);
                    $t = $cn->where('user_id', $u->id)->where('master_needle_id', $n->id)->count();
                    $ws->getCell("{$col2}{$kkk}")->setValue($t);
                    $last++;
                    $total += $t;
                }
                $ws->getCell("I$kkk")->setValue($total);
            }

            $ws->mergeCells("A" . $kkk + 1 . ':' . "H" . $kkk + 1)->getCell("A" . $kkk + 1)->setValue('Total')->getStyle()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $last = 9;
            foreach ($collect_master_needle->all() as $n) {
                $col2 = HelperController::numberToLetters($last);
                $ws->getCell($col2 . $kkk + 1)->setValue('=SUM(' . $col2 . '12:' . $col2 . $kkk . ')');
                $last++;
            }

            $ws->getStyle("A" . $kk + 1 . ':' . $col1 . $kkk + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            $ws->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $ws->mergeCells('A1:' . $col1 . '1')->getCell('A1')->setValue($xx->judul)->getStyle()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);

            foreach ($ws->getColumnIterator() as $column) {
                $ws->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }

            $writer = new Xlsx($sp);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' .  $xx->judul . '.xlsx"');
            $writer->save('php://output');
            exit();
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 422);
        }
    }
}
