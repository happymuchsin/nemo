<?php

namespace App\Http\Controllers\User\Report;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\MasterHoliday;
use App\Models\Needle;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use stdClass;

class WipNeedleController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'user_report_wip_needle';
        $title = 'USER REPORT WIP NEEDLE';

        HelperController::activityLog('OPEN USER REPORT WIP NEEDLE', null, 'read', $request->ip(), $request->userAgent());

        return view('User.Report.WipNeedle.index', compact('title', 'page'));
    }

    public function data(Request $request)
    {
        $mode = $request->mode;
        $filter_period = $request->filter_period;

        $data = [];

        $xx = HelperController::period($filter_period, $request->filter_range_date, $request->filter_daily, $request->filter_weekly, $request->filter_month, $request->filter_year, 'WIP Needle');

        $needle = Needle::whereBetween('created_at', $xx->range)
            ->whereIn('master_status_id', [1, 2, 3, 4]);
        $cn = collect($needle->get());

        if ($mode == 'kiri') {
            foreach ($needle->groupByRaw('user_id, DATE(created_at), master_needle_id')->get() as $n) {
                $d = new stdClass;
                $d->username = $n->user->username;
                $d->name = $n->user->name;
                $d->division = $n->user->division->name;
                $d->position = $n->user->position->name;
                $tipe = '';
                $location = '';
                $counter = '';
                if ($n->user->placement) {
                    $tipe = strtoupper($n->user->placement->reff);
                    if ($n->user->placement->line) {
                        $location = $n->user->placement->line->area->name . ' - ' . $n->user->placement->line->name;
                    }
                    if ($n->user->placement->counter) {
                        $counter = $n->user->placement->counter->area->name . ' - ' . $n->user->placement->counter->name;
                    }
                }
                $d->tipe = $tipe;
                $d->location = $location;
                $d->counter = $counter;
                $tanggal = date('Y-m-d', strtotime($n->created_at));
                $d->date = $tanggal;
                $d->needle = $n->needle->tipe;
                $d->size = $n->needle->size;
                $d->machine = $n->needle->machine;
                $holiday = MasterHoliday::whereBetween('tanggal', [$tanggal, $xx->end])->count();
                $cum = Carbon::parse($tanggal)->diffInDays($xx->end) - $holiday;
                $d->cum = $cum < 0 ? 0 : $cum;
                $d->qty = $cn->whereBetween('created_at', [$tanggal . ' 00:00:00', $tanggal . ' 23:59:59'])->where('user_id', $n->user_id)->where('master_needle_id', $n->master_needle_id)->count();
                $data[] = $d;
            }
        } else if ($mode == 'kanan') {
            $mh = MasterHoliday::get();
            $cmh = collect($mh);
            $ys = Carbon::parse($xx->start);
            $ye = Carbon::parse($xx->end);

            while ($ys->lte($ye)) {
                $d = new stdClass;
                $tanggal = $ys->toDateString();
                $x = $cmh->where('tanggal', $tanggal);
                if (!$x->value('id')) {
                    $wip = $cn->whereBetween('created_at', [$tanggal . ' 00:00:00', $tanggal . ' 23:59:59'])->count();
                    $d->date = $tanggal;
                    $d->wip = $wip;
                    $data[] = $d;
                }
                $ys->addDay();
            }
        }

        return datatables()->of($data)
            ->make(true);
    }

    public function unduh(Request $request)
    {
        $filter_period = $request->filter_period;

        $xx = HelperController::period($filter_period, $request->filter_range_date, $request->filter_daily, $request->filter_weekly, $request->filter_month, $request->filter_year, 'WIP Needle');

        try {
            $needle = Needle::whereBetween('created_at', $xx->range)
                ->whereIn('master_status_id', [1, 2, 3, 4]);
            $cn = collect($needle->get());

            $sp = new Spreadsheet;
            $ws = $sp->getActiveSheet();

            $ws->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $ws->mergeCells('A1:R1')->getCell('A1')->setValue($xx->judul)->getStyle()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $ws->getStyle("A4:N4")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $ws->getStyle("A4:N4")->getFont()->setBold(true);
            $ws->getStyle("A4:N4")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $ws->getCell("A4")->setValue('No');
            $ws->getCell("B4")->setValue('Username');
            $ws->getCell("C4")->setValue('Name');
            $ws->getCell("D4")->setValue('Division');
            $ws->getCell("E4")->setValue('Position');
            $ws->getCell("F4")->setValue('Type');
            $ws->getCell("G4")->setValue('Location');
            $ws->getCell("H4")->setValue('Counter');
            $ws->getCell("I4")->setValue('Date Issued');
            $ws->getCell("J4")->setValue('Type Needle');
            $ws->getCell("K4")->setValue('Size');
            $ws->getCell("L4")->setValue('Machine');
            $ws->getCell("M4")->setValue('Cumulative Days After Issued');
            $ws->getCell("N4")->setValue('Qty');

            $k = 4;
            $i = 0;
            foreach ($needle->groupByRaw('user_id, DATE(created_at), master_needle_id')->get() as $n) {
                $i++;
                $k++;
                $ws->getStyle("A$k:N$k")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $ws->getCell("A$k")->setValue($i);
                $ws->getCell("B$k")->setValue($n->user->username);
                $ws->getCell("C$k")->setValue($n->user->name);
                $ws->getCell("D$k")->setValue($n->user->division->name);
                $ws->getCell("E$k")->setValue($n->user->position->name);
                $tipe = '';
                $location = '';
                $counter = '';
                if ($n->user->placement) {
                    $tipe = strtoupper($n->user->placement->reff);
                    if ($n->user->placement->line) {
                        $location = $n->user->placement->line->area->name . ' - ' . $n->user->placement->line->name;
                    }
                    if ($n->user->placement->counter) {
                        $counter = $n->user->placement->counter->area->name . ' - ' . $n->user->placement->counter->name;
                    }
                }
                $ws->getCell("F$k")->setValue($tipe);
                $ws->getCell("G$k")->setValue($location);
                $ws->getCell("H$k")->setValue($counter);
                $tanggal = date('Y-m-d', strtotime($n->created_at));
                $ws->getCell("I$k")->setValue($tanggal);
                $ws->getCell("J$k")->setValue($n->needle->tipe);
                $ws->getCell("K$k")->setValue($n->needle->size);
                $ws->getCell("L$k")->setValue($n->needle->machine);
                $holiday = MasterHoliday::whereBetween('tanggal', [$tanggal, $xx->end])->count();
                $cum = Carbon::parse($tanggal)->diffInDays($xx->end) - $holiday;
                $ws->getCell("M$k")->setValue($cum < 0 ? 0 : $cum);
                $ws->getCell("N$k")->setValue($cn->whereBetween('created_at', [$tanggal . ' 00:00:00', $tanggal . ' 23:59:59'])->where('user_id', $n->user_id)->where('master_needle_id', $n->master_needle_id)->count());
            }

            $ws->mergeCells("Q3:R3")->getCell("Q3")->setValue('Summary - WIP by Date');
            $ws->getCell("Q4")->setValue('Date');
            $ws->getCell("R4")->setValue('WIP Needle');
            $ws->getStyle("Q3:R4")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $ws->getStyle("Q3:R4")->getFont()->setBold(true);
            $ws->getStyle('Q4:R4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            $k = 4;

            $mh = MasterHoliday::get();
            $cmh = collect($mh);
            $ys = Carbon::parse($xx->start);
            $ye = Carbon::parse($xx->end);

            while ($ys->lte($ye)) {
                $tanggal = $ys->toDateString();
                $x = $cmh->where('tanggal', $tanggal);
                if (!$x->value('id')) {
                    $k++;
                    $wip = $cn->whereBetween('created_at', [$tanggal . ' 00:00:00', $tanggal . ' 23:59:59'])->count();
                    $ws->getCell("Q$k")->setValue($tanggal);
                    $ws->getCell("R$k")->setValue($wip);
                }
                $ys->addDay();
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
