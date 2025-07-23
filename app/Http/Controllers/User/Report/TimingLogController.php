<?php

namespace App\Http\Controllers\User\Report;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\Needle;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use stdClass;

class TimingLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'user_report_timing_log';
        $title = 'USER REPORT TIMING LOG';

        HelperController::activityLog('OPEN USER REPORT TIMING LOG', null, 'read', $request->ip(), $request->userAgent());

        return view('User.Report.TimingLog.index', compact('title', 'page'));
    }

    public function data(Request $request)
    {
        $range_date = explode(' - ', $request->filter_range_date);
        $start = $range_date[0] ? $range_date[0] : Carbon::today();
        $end = $range_date[1] ? $range_date[1] : Carbon::today();

        $data = [];

        $durations = [];
        $needle = Needle::with(['user'])->whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])->get();
        foreach ($needle as $n) {
            $duration = $n->scan_rfid && $n->scan_box ? Carbon::parse($n->scan_rfid)->diff(Carbon::parse($n->scan_box))->format('%H:%i:%s') : '-';
            $d = new stdClass;
            $d->name = $n->user->name;
            $d->rfid = $n->scan_rfid;
            $d->box = $n->scan_box;
            $d->duration = $duration;
            $d->note = $n->note;
            $data[] = $d;
            if ($duration !== '-') {
                $durations[] = $duration;
            } else {
                $durations[] = '00:00:00'; // Default to zero duration if not available
            }
        }

        if (count($durations) > 0) {
            $totalSeconds = 0;

            foreach ($durations as $time) {
                [$hours, $minutes, $seconds] = explode(':', $time);
                $totalSeconds += ($hours * 3600) + ($minutes * 60) + $seconds;
            }

            $averageSeconds = $totalSeconds / count($durations);
            $averageFormatted = gmdate('H:i:s', (int) round($averageSeconds));
        } else {
            $averageFormatted = '-';
        }

        return datatables()->of($data)
            ->with([
                'average' => $averageFormatted
            ])
            ->make(true);
    }

    public function unduh(Request $request)
    {
        $filter_range_date = $request->filter_range_date;

        try {
            $sp = new Spreadsheet;
            $ws = $sp->getActiveSheet();

            $judul = 'Timing Log ' . $filter_range_date;

            $range_date = explode(' - ', $filter_range_date);
            $start = $range_date[0] ? $range_date[0] : Carbon::today();
            $end = $range_date[1] ? $range_date[1] : Carbon::today();

            $ws->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $ws->mergeCells('A1:F1')->getCell('A1')->setValue(strtoupper($judul))->getStyle()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $ws->getStyle('A3:F5')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $ws->getStyle('A3:F5')->getFont()->setBold(true);
            $ws->getStyle('A3:F5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $ws->mergeCells('A3:A5')->getCell('A3')->setValue('No');
            $ws->mergeCells('B3:B5')->getCell('B3')->setValue('Name');
            $ws->mergeCells('C3:D3')->getCell('C3')->setValue('RFID');
            $ws->mergeCells('C4:D4')->getCell('C4')->setValue('Date Time Scan');
            $ws->getCell('C5')->setValue('Scan RFID Operator');
            $ws->getCell('D5')->setValue('Scan Box Needle');
            $ws->mergeCells('E3:E5')->getCell('E3')->setValue('Duration');
            $ws->mergeCells('F3:F5')->getCell('F3')->setValue('Remarks');

            $durations = [];
            $k = 5;
            $i = 0;
            $needle = Needle::with(['user'])->whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])->get();
            foreach ($needle as $n) {
                $k++;
                $i++;
                $duration = $n->scan_rfid && $n->scan_box ? Carbon::parse($n->scan_rfid)->diff(Carbon::parse($n->scan_box))->format('%H:%i:%s') : '-';
                $ws->getStyle("A$k:F$k")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $ws->getCell("A$k")->setValue($i);
                $ws->getCell("B$k")->setValue($n->user->name);
                $ws->getCell("C$k")->setValue($n->scan_rfid);
                $ws->getCell("D$k")->setValue($n->scan_box);
                $ws->getCell("E$k")->setValue($duration);
                $ws->getCell("F$k")->setValue($n->note);
                if ($duration !== '-') {
                    $durations[] = $duration;
                } else {
                    $durations[] = '00:00:00'; // Default to zero duration if not available
                }
            }

            if (count($durations) > 0) {
                $totalSeconds = 0;

                foreach ($durations as $time) {
                    [$hours, $minutes, $seconds] = explode(':', $time);
                    $totalSeconds += ($hours * 3600) + ($minutes * 60) + $seconds;
                }

                $averageSeconds = $totalSeconds / count($durations);
                $averageFormatted = gmdate('H:i:s', (int) round($averageSeconds));
            } else {
                $averageFormatted = '-';
            }

            $ws->mergeCells("A$k:D$k")->getCell("A$k")->setValue('Average');
            $ws->getCell("E$k")->setValue($averageFormatted);
            $ws->getStyle("A$k:F$k")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $ws->getStyle("A$k:F$k")->getFont()->setBold(true);
            $ws->getStyle("A$k:F$k")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);

            foreach ($ws->getColumnIterator() as $column) {
                $ws->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }

            $writer = new Xlsx($sp);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $judul . '.xlsx"');
            $writer->save('php://output');
            exit();
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 422);
        }
    }
}
