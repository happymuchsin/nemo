<?php

namespace App\Http\Controllers\User\Report;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
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

class IntervalUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'user_report_interval_user';
        $title = 'USER REPORT INTERVAL USER';

        HelperController::activityLog('OPEN USER REPORT INTERVAL USER', null, 'read', $request->ip(), $request->userAgent());

        return view('User.Report.IntervalUser.index', compact('title', 'page'));
    }

    public function data(Request $request)
    {
        $filter_period = $request->filter_period;

        $data = [];

        $xx = HelperController::period($filter_period, $request->filter_range_date, $request->filter_daily, $request->filter_weekly, $request->filter_month, $request->filter_year, 'Interval User');

        $date = [];

        $needle = Needle::whereBetween('created_at', $xx->range)
            ->whereIn('master_status_id', [1, 2, 3, 4])
            ->get();
        $cn = collect($needle);

        $ys = Carbon::parse($xx->start);
        $ye = Carbon::parse($xx->end);

        while ($ys->lte($ye)) {
            $tanggal = $ys->toDateString();
            $date[] = $tanggal;
            $ys->addDay();
        }

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
        foreach ($user as $k => $u) {
            $d = new stdClass;
            $d->nomor = $k + 1;
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
            foreach ($date as $i) {
                $n = $cn->whereBetween('created_at', [$i . ' 00:00:00', $i . ' 23:59:59'])->where('user_id', $u->id)->count();
                $out = 'x' . str_replace('-', '', $i);
                $d->$out = $n;
                $total += $n;
            }
            $d->date = $date;
            $d->total = $total;
            $json = htmlspecialchars(json_encode($d), ENT_QUOTES, 'UTF-8');
            $d->graph = '<a href="#" class="text-center" title="Graph" onclick="graph(\'' . $json . '\')"><i class="fa fa-chart-line text-warning mr-3"></i></a>';
            $data[] = $d;
        }

        return response()->json([
            'date' => $date,
            'data' => $data,
        ], 200);
    }

    public function unduh(Request $request)
    {
        $filter_period = $request->filter_period;

        $xx = HelperController::period($filter_period, $request->filter_range_date, $request->filter_daily, $request->filter_weekly, $request->filter_month, $request->filter_year, 'Interval User');

        try {
            $date = [];

            $needle = Needle::whereBetween('created_at', $xx->range)
                ->whereIn('master_status_id', [1, 2, 3, 4])
                ->get();
            $cn = collect($needle);

            $ys = Carbon::parse($xx->start);
            $ye = Carbon::parse($xx->end);

            while ($ys->lte($ye)) {
                $tanggal = $ys->toDateString();
                $date[] = $tanggal;
                $ys->addDay();
            }

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

            $sp = new Spreadsheet;
            $ws = $sp->getActiveSheet();

            $ws->getCell('A3')->setValue('No');
            $ws->getCell('B3')->setValue('Username');
            $ws->getCell('C3')->setValue('Name');
            $ws->getCell('D3')->setValue('Division');
            $ws->getCell('E3')->setValue('Position');
            $ws->getCell('F3')->setValue('Type');
            $ws->getCell('G3')->setValue('Location');
            $ws->getCell('H3')->setValue('Counter');
            $ws->getCell('I3')->setValue('Total');
            $last = 9;
            foreach ($date as $i) {
                $last++;
                $col = HelperController::numberToLetters($last);
                $ws->getCell($col . '3')->setValue($i);
            }

            $col = HelperController::numberToLetters($last);
            $ws->getStyle('A3:' . $col . '3')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $ws->getStyle('A3:' . $col . '3')->getFont()->setBold(true)->setSize(14);
            $ws->getStyle('A3:' . $col . '3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $ws->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $ws->mergeCells('A1:' . $col . '1')->getCell('A1')->setValue($xx->judul)->getStyle()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $k = 3;
            $i = 0;
            foreach ($user as $u) {
                $i++;
                $k++;
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
                $ws->getCell("A$k")->setValue($i);
                $ws->getCell("B$k")->setValue($u->username);
                $ws->getCell("C$k")->setValue($u->name);
                $ws->getCell("D$k")->setValue($u->division->name);
                $ws->getCell("E$k")->setValue($u->position->name);
                $ws->getCell("F$k")->setValue($tipe);
                $ws->getCell("G$k")->setValue($location);
                $ws->getCell("H$k")->setValue($counter);
                $last = 9;
                $total = 0;
                foreach ($date as $d) {
                    $last++;
                    $col = HelperController::numberToLetters($last);
                    $n = $cn->whereBetween('created_at', [$d . ' 00:00:00', $d . ' 23:59:59'])->where('user_id', $u->id)->count();
                    $ws->getCell($col . $k)->setValue($n);
                    $total += $n;
                }
                $ws->getCell("I$k")->setValue($total);
                $col = HelperController::numberToLetters($last);
                $ws->getStyle("A$k:{$col}{$k}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            }

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
