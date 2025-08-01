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

class HighUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'user_report_high_user';
        $title = 'USER REPORT HIGH USER';

        HelperController::activityLog('OPEN USER REPORT HIGH USER', null, 'read', $request->ip(), $request->userAgent());

        return view('User.Report.HighUser.index', compact('title', 'page'));
    }

    public function data(Request $request)
    {
        $filter_period = $request->filter_period;

        $data = [];

        $xx = HelperController::period($filter_period, $request->filter_range_date, $request->filter_daily, $request->filter_weekly, $request->filter_month, $request->filter_year, 'High User');

        $needle = Needle::whereBetween('created_at', $xx->range)
            ->whereIn('master_status_id', [1, 2, 3, 4])
            ->get();
        $cn = collect($needle);

        $diff = Carbon::parse($xx->end)->diffInDays(Carbon::parse($xx->start));

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

            $deformed = $cn->where('user_id', $u->id)->where('master_status_id', 1)->count();
            $routine = $cn->where('user_id', $u->id)->where('master_status_id', 2)->count();
            $change = $cn->where('user_id', $u->id)->where('master_status_id', 3)->count();
            $broken = $cn->where('user_id', $u->id)->where('master_status_id', 4)->count();
            $total = $deformed + $routine + $change + $broken;

            $d->total = $total;
            $d->deformed = $deformed;
            $d->routine = $routine;
            $d->change = $change;
            $d->broken = $broken;
            $d->graph = "
                <div class='progress'>
                    <div class='progress-bar bg-warning' style='width: " . ($total == 0 ? 0 : $deformed / $total * 100) . "%'></div>
                    <div class='progress-bar bg-success' style='width: " . ($total == 0 ? 0 : $routine / $total * 100) . "%'></div>
                    <div class='progress-bar bg-primary' style='width: " . ($total == 0 ? 0 : $change / $total * 100) . "%'></div>
                    <div class='progress-bar bg-black' style='width: " . ($total == 0 ? 0 : $broken / $total * 100) . "%'></div>
                </div>
            ";
            $d->average = number_format($total / $diff, 3, ',', '.');
            $data[] = $d;
        }

        return datatables()->of($data)
            ->rawColumns(['graph'])
            ->make(true);
    }

    public function unduh(Request $request)
    {
        $filter_period = $request->filter_period;

        $xx = HelperController::period($filter_period, $request->filter_range_date, $request->filter_daily, $request->filter_weekly, $request->filter_month, $request->filter_year, 'High User');

        try {
            $needle = Needle::whereBetween('created_at', $xx->range)
                ->whereIn('master_status_id', [1, 2, 3, 4])
                ->get();
            $cn = collect($needle);

            $diff = Carbon::parse($xx->end)->diffInDays(Carbon::parse($xx->start));

            $sp = new Spreadsheet;
            $ws = $sp->getActiveSheet();

            $ws->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $ws->mergeCells('A1:N1')->getCell('A1')->setValue($xx->judul)->getStyle()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $ws->getStyle("A3:N3")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $ws->getStyle("A3:N3")->getFont()->setBold(true);
            $ws->getStyle("A3:N3")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $ws->getCell("A3")->setValue('No');
            $ws->getCell("B3")->setValue('Username');
            $ws->getCell("C3")->setValue('Name');
            $ws->getCell("D3")->setValue('Division');
            $ws->getCell("E3")->setValue('Position');
            $ws->getCell("F3")->setValue('Type');
            $ws->getCell("G3")->setValue('Location');
            $ws->getCell("H3")->setValue('Counter');
            $ws->getCell("I3")->setValue('Total');
            $ws->getCell("J3")->setValue('Deformed');
            $ws->getCell("K3")->setValue('Routine Change');
            $ws->getCell("L3")->setValue('Change Style or Material');
            $ws->getCell("M3")->setValue('Broken Missing Fragment');
            $ws->getCell("N3")->setValue('Average Use / Days');

            $k = 3;

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

            $i = 0;
            foreach ($user as $u) {
                $i++;
                $k++;
                $ws->getStyle("A$k:N$k")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $ws->getCell("A$k")->setValue($i);
                $ws->getCell("B$k")->setValue($u->username);
                $ws->getCell("C$k")->setValue($u->name);
                $ws->getCell("D$k")->setValue($u->division->name);
                $ws->getCell("E$k")->setValue($u->position->name);
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
                $ws->getCell("F$k")->setValue($tipe);
                $ws->getCell("G$k")->setValue($location);
                $ws->getCell("H$k")->setValue($counter);

                $deformed = $cn->where('user_id', $u->id)->where('master_status_id', 1)->count();
                $routine = $cn->where('user_id', $u->id)->where('master_status_id', 2)->count();
                $change = $cn->where('user_id', $u->id)->where('master_status_id', 3)->count();
                $broken = $cn->where('user_id', $u->id)->where('master_status_id', 4)->count();
                $total = $deformed + $routine + $change + $broken;

                $ws->getCell("I$k")->setValue($total);
                $ws->getCell("J$k")->setValue($deformed);
                $ws->getCell("K$k")->setValue($routine);
                $ws->getCell("L$k")->setValue($change);
                $ws->getCell("M$k")->setValue($broken);
                $ws->getCell("N$k")->setValue(number_format($total / $diff, 3, ',', '.'));
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
