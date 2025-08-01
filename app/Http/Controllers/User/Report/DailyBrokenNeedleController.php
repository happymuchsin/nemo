<?php

namespace App\Http\Controllers\User\Report;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\HistoryAddStock;
use App\Models\MasterLine;
use App\Models\MasterMonthlyStock;
use App\Models\MasterMorningStock;
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

class DailyBrokenNeedleController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'user_report_daily_broken_needle';
        $title = 'USER REPORT DAILY BROKEN NEEDLE';

        HelperController::activityLog('OPEN USER REPORT DAILY BROKEN NEEDLE', null, 'read', $request->ip(), $request->userAgent());

        return view('User.Report.DailyBrokenNeedle.index', compact('title', 'page'));
    }

    public function data(Request $request)
    {
        $filter_date = $request->filter_date;
        $data = self::theData($filter_date);

        return datatables()->of($data)
            ->make(true);
    }

    public function unduh(Request $request)
    {
        $filter_date = $request->filter_date;
        $data = self::theData($filter_date);

        try {
            $judul = 'Daily Broken Needle Sample Room ' . date('l, Y-m-d', strtotime($filter_date));

            $sp = new Spreadsheet;
            $ws = $sp->getActiveSheet();

            $ws->mergeCells('A3:A5')->getCell('A3')->setValue('Needle Code');
            $ws->mergeCells('B3:B5')->getCell('B3')->setValue('Needle Type');
            $ws->mergeCells('C3:C5')->getCell('C3')->setValue('Size');
            $ws->mergeCells('D3:E3')->getCell('D3')->setValue('Stock');
            $ws->mergeCells('D4:D5')->getCell('D4')->setValue('Min');
            $ws->mergeCells('E4:E5')->getCell('E4')->setValue('Max');
            $ws->mergeCells('F3:F5')->getCell('F3')->setValue('Morning Stock Update');
            $ws->mergeCells('G3:G5')->getCell('G3')->setValue('Incoming Stock');

            $ws->mergeCells('H3:J3')->getCell('H3')->setValue('Sample Line 1 (ACENG)');
            $ws->mergeCells('H4:H4')->getCell('H4')->setValue('Deformed');
            $ws->mergeCells('H5:H5')->getCell('H5')->setValue('Broken');
            $ws->mergeCells('I4:I5')->getCell('I4')->setValue('Operator');
            $ws->mergeCells('J4:J4')->getCell('J4')->setValue('Change');
            $ws->mergeCells('J5:J5')->getCell('J5')->setValue('Tumpul');

            $ws->mergeCells('K3:M3')->getCell('K3')->setValue('Sample Line 2 (OTONG)');
            $ws->mergeCells('K4:K4')->getCell('K4')->setValue('Deformed');
            $ws->mergeCells('K5:K5')->getCell('K5')->setValue('Broken');
            $ws->mergeCells('L4:L5')->getCell('L4')->setValue('Operator');
            $ws->mergeCells('M4:M4')->getCell('M4')->setValue('Change');
            $ws->mergeCells('M5:M5')->getCell('M5')->setValue('Tumpul');

            $ws->mergeCells('N3:P3')->getCell('N3')->setValue('Sample Line 3 (YANTI)');
            $ws->mergeCells('N4:N4')->getCell('N4')->setValue('Deformed');
            $ws->mergeCells('N5:N5')->getCell('N5')->setValue('Broken');
            $ws->mergeCells('O4:O5')->getCell('O4')->setValue('Operator');
            $ws->mergeCells('P4:P4')->getCell('P4')->setValue('Change');
            $ws->mergeCells('P5:P5')->getCell('P5')->setValue('Tumpul');

            $ws->mergeCells('Q3:S3')->getCell('Q3')->setValue('Sample Line 4 (SUNARI)');
            $ws->mergeCells('Q4:Q4')->getCell('Q4')->setValue('Deformed');
            $ws->mergeCells('Q5:Q5')->getCell('Q5')->setValue('Broken');
            $ws->mergeCells('R4:R5')->getCell('R4')->setValue('Operator');
            $ws->mergeCells('S4:S4')->getCell('S4')->setValue('Change');
            $ws->mergeCells('S5:S5')->getCell('S5')->setValue('Tumpul');

            $ws->mergeCells('T3:V3')->getCell('T3')->setValue('Sample Line 5 (SMS MURNI)');
            $ws->mergeCells('T4:T4')->getCell('T4')->setValue('Deformed');
            $ws->mergeCells('T5:T5')->getCell('T5')->setValue('Broken');
            $ws->mergeCells('U4:U5')->getCell('U4')->setValue('Operator');
            $ws->mergeCells('V4:V4')->getCell('V4')->setValue('Change');
            $ws->mergeCells('V5:V5')->getCell('V5')->setValue('Tumpul');

            $ws->mergeCells('W3:Y3')->getCell('W3')->setValue('Sample Line 6 (SMS YANTI)');
            $ws->mergeCells('W4:W4')->getCell('W4')->setValue('Deformed');
            $ws->mergeCells('W5:W5')->getCell('W5')->setValue('Broken');
            $ws->mergeCells('X4:X5')->getCell('X4')->setValue('Operator');
            $ws->mergeCells('Y4:Y4')->getCell('Y4')->setValue('Change');
            $ws->mergeCells('Y5:Y5')->getCell('Y5')->setValue('Tumpul');

            $ws->mergeCells('Z3:AB3')->getCell('Z3')->setValue('Sample Line 7 (SRIWIJAYANING)');
            $ws->mergeCells('Z4:Z4')->getCell('Z4')->setValue('Deformed');
            $ws->mergeCells('Z5:Z5')->getCell('Z5')->setValue('Broken');
            $ws->mergeCells('AA4:AA5')->getCell('AA4')->setValue('Operator');
            $ws->mergeCells('AB4:AB4')->getCell('AB4')->setValue('Change');
            $ws->mergeCells('AB5:AB5')->getCell('AB5')->setValue('Tumpul');

            $ws->mergeCells('AC3:AE3')->getCell('AC3')->setValue('Sample Line 8 (CNC)');
            $ws->mergeCells('AC4:AC4')->getCell('AC4')->setValue('Deformed');
            $ws->mergeCells('AC5:AC5')->getCell('AC5')->setValue('Broken');
            $ws->mergeCells('AD4:AD5')->getCell('AD4')->setValue('Operator');
            $ws->mergeCells('AE4:AE4')->getCell('AE4')->setValue('Change');
            $ws->mergeCells('AE5:AE5')->getCell('AE5')->setValue('Tumpul');

            $ws->mergeCells('AF3:AH3')->getCell('AF3')->setValue('Sample Line 9 (FINISHING)');
            $ws->mergeCells('AF4:AF4')->getCell('AF4')->setValue('Deformed');
            $ws->mergeCells('AF5:AF5')->getCell('AF5')->setValue('Broken');
            $ws->mergeCells('AG4:AG5')->getCell('AG4')->setValue('Operator');
            $ws->mergeCells('AH4:AH4')->getCell('AH4')->setValue('Change');
            $ws->mergeCells('AH5:AH5')->getCell('AH5')->setValue('Tumpul');

            $ws->mergeCells('AI3:AI5')->getCell('AI3')->setValue('Sub Total');
            $ws->mergeCells('AJ3:AK3')->getCell('AJ3')->setValue('Broken Missing Fragment');
            $ws->mergeCells('AJ4:AJ5')->getCell('AJ4')->setValue('Line Sample');
            $ws->mergeCells('AK4:AK5')->getCell('AK4')->setValue('Operator');
            $ws->mergeCells('AL3:AL5')->getCell('AL3')->setValue('Grand Total');
            $ws->mergeCells('AM3:AM5')->getCell('AM3')->setValue('Broken Total');
            $ws->mergeCells('AN3:AN5')->getCell('AN3')->setValue('Tumpul Total');
            $ws->mergeCells('AO3:AO5')->getCell('AO3')->setValue('Missing Fragment Total');
            $ws->mergeCells('AP3:AP5')->getCell('AP3')->setValue('End of Stock Update');

            $ws->getStyle('A3:AP5')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $ws->getStyle('A3:AP5')->getFont()->setBold(true)->setSize(14);
            $ws->getStyle('A3:AP5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $ws->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $ws->mergeCells('A1:AP1')->getCell('A1')->setValue($judul)->getStyle()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $k = 5;
            $i = 0;
            foreach ($data as $d) {
                $i++;
                $k++;
                $ws->getCell("A$k")->setValue($d->code);
                $ws->getCell("B$k")->setValue($d->tipe);
                $ws->getCell("C$k")->setValue($d->size);
                $ws->getCell("D$k")->setValue($d->min_stock);
                $ws->getCell("E$k")->setValue($d->max_stock);
                $ws->getCell("F$k")->setValue($d->morning_stock);
                $ws->getCell("G$k")->setValue($d->incoming_stock);
                $ws->getCell("H$k")->setValue($d->deformed_1);
                $ws->getCell("I$k")->setValue($d->operator_1);
                $ws->getCell("J$k")->setValue($d->change_1);
                $ws->getCell("K$k")->setValue($d->deformed_2);
                $ws->getCell("L$k")->setValue($d->operator_2);
                $ws->getCell("M$k")->setValue($d->change_2);
                $ws->getCell("N$k")->setValue($d->deformed_3);
                $ws->getCell("O$k")->setValue($d->operator_3);
                $ws->getCell("P$k")->setValue($d->change_3);
                $ws->getCell("Q$k")->setValue($d->deformed_4);
                $ws->getCell("R$k")->setValue($d->operator_4);
                $ws->getCell("S$k")->setValue($d->change_4);
                $ws->getCell("T$k")->setValue($d->deformed_5);
                $ws->getCell("U$k")->setValue($d->operator_5);
                $ws->getCell("V$k")->setValue($d->change_5);
                $ws->getCell("W$k")->setValue($d->deformed_6);
                $ws->getCell("X$k")->setValue($d->operator_6);
                $ws->getCell("Y$k")->setValue($d->change_6);
                $ws->getCell("Z$k")->setValue($d->deformed_7);
                $ws->getCell("AA$k")->setValue($d->operator_7);
                $ws->getCell("AB$k")->setValue($d->change_7);
                $ws->getCell("AC$k")->setValue($d->deformed_8);
                $ws->getCell("AD$k")->setValue($d->operator_8);
                $ws->getCell("AE$k")->setValue($d->change_8);
                $ws->getCell("AF$k")->setValue($d->deformed_9);
                $ws->getCell("AG$k")->setValue($d->operator_9);
                $ws->getCell("AH$k")->setValue($d->change_9);
                $ws->getCell("AI$k")->setValue($d->sub_total);
                $ws->getCell("AJ$k")->setValue($d->line_sample);
                $ws->getCell("AK$k")->setValue($d->operator_line_sample);
                $ws->getCell("AL$k")->setValue($d->grand_total);
                $ws->getCell("AM$k")->setValue($d->total_broken);
                $ws->getCell("AN$k")->setValue($d->total_tumpul);
                $ws->getCell("AO$k")->setValue($d->missing_fragment_total);
                $ws->getCell("AP$k")->setValue($d->end_of_stock_update);
                $ws->getStyle("A$k:AP$k")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            }

            $kk = $k + 1;
            $ws->mergeCells("A$kk:C$kk")->getCell("A$kk")->setValue('Total');
            $ws->getCell("D$kk")->setValue(array_sum(array_column($data, 'min_stock')));
            $ws->getCell("E$kk")->setValue(array_sum(array_column($data, 'max_stock')));
            $ws->getCell("F$kk")->setValue(array_sum(array_column($data, 'morning_stock')));
            $ws->getCell("G$kk")->setValue(array_sum(array_column($data, 'incoming_stock')));
            $ws->getCell("H$kk")->setValue(array_sum(array_column($data, 'deformed_1')));
            $ws->getCell("I$kk")->setValue(array_sum(array_column($data, 'operator_1')));
            $ws->getCell("J$kk")->setValue(array_sum(array_column($data, 'change_1')));
            $ws->getCell("K$kk")->setValue(array_sum(array_column($data, 'deformed_2')));
            $ws->getCell("L$kk")->setValue(array_sum(array_column($data, 'operator_2')));
            $ws->getCell("M$kk")->setValue(array_sum(array_column($data, 'change_2')));
            $ws->getCell("N$kk")->setValue(array_sum(array_column($data, 'deformed_3')));
            $ws->getCell("O$kk")->setValue(array_sum(array_column($data, 'operator_3')));
            $ws->getCell("P$kk")->setValue(array_sum(array_column($data, 'change_3')));
            $ws->getCell("Q$kk")->setValue(array_sum(array_column($data, 'deformed_4')));
            $ws->getCell("R$kk")->setValue(array_sum(array_column($data, 'operator_4')));
            $ws->getCell("S$kk")->setValue(array_sum(array_column($data, 'change_4')));
            $ws->getCell("T$kk")->setValue(array_sum(array_column($data, 'deformed_5')));
            $ws->getCell("U$kk")->setValue(array_sum(array_column($data, 'operator_5')));
            $ws->getCell("V$kk")->setValue(array_sum(array_column($data, 'change_5')));
            $ws->getCell("W$kk")->setValue(array_sum(array_column($data, 'deformed_6')));
            $ws->getCell("X$kk")->setValue(array_sum(array_column($data, 'operator_6')));
            $ws->getCell("Y$kk")->setValue(array_sum(array_column($data, 'change_6')));
            $ws->getCell("Z$kk")->setValue(array_sum(array_column($data, 'deformed_7')));
            $ws->getCell("AA$kk")->setValue(array_sum(array_column($data, 'operator_7')));
            $ws->getCell("AB$kk")->setValue(array_sum(array_column($data, 'change_7')));
            $ws->getCell("AC$kk")->setValue(array_sum(array_column($data, 'deformed_8')));
            $ws->getCell("AD$kk")->setValue(array_sum(array_column($data, 'operator_8')));
            $ws->getCell("AE$kk")->setValue(array_sum(array_column($data, 'change_8')));
            $ws->getCell("AF$kk")->setValue(array_sum(array_column($data, 'deformed_9')));
            $ws->getCell("AG$kk")->setValue(array_sum(array_column($data, 'operator_9')));
            $ws->getCell("AH$kk")->setValue(array_sum(array_column($data, 'change_9')));
            $ws->getCell("AI$kk")->setValue(array_sum(array_column($data, 'sub_total')));
            $ws->getCell("AJ$kk")->setValue(array_sum(array_column($data, 'line_sample')));
            $ws->getCell("AK$kk")->setValue(array_sum(array_column($data, 'operator_line_sample')));
            $ws->getCell("AL$kk")->setValue(array_sum(array_column($data, 'grand_total')));
            $ws->getCell("AM$kk")->setValue(array_sum(array_column($data, 'total_broken')));
            $ws->getCell("AN$kk")->setValue(array_sum(array_column($data, 'total_tumpul')));
            $ws->getCell("AO$kk")->setValue(array_sum(array_column($data, 'missing_fragment_total')));
            $ws->getCell("AP$kk")->setValue(array_sum(array_column($data, 'end_of_stock_update')));

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

    static function theData($filter_date)
    {
        $bulan = date('m', strtotime($filter_date));
        $tahun = date('Y', strtotime($filter_date));
        $data = [];

        $master_morning_stock = MasterMorningStock::get();
        $collect_master_morning_stock = collect($master_morning_stock);
        $master_monthly_stock = MasterMonthlyStock::where('tahun', $tahun)->where('bulan', $bulan)->get();
        $collect_master_monthly_stock = collect($master_monthly_stock);

        $needle = Needle::with(['user'])
            ->whereBetween('created_at', [$filter_date . ' 00:00:00', $filter_date . ' 23:59:59'])
            ->whereIn('master_status_id', [1, 2, 3, 4])
            ->get();
        $collect_needle = collect($needle);

        $history_add_stock = HistoryAddStock::with(['stock'])
            ->whereBetween('created_at', [$filter_date . ' 00:00:00', $filter_date . ' 23:59:59'])
            ->get();
        $collect_history_add_stock = collect($history_add_stock);

        $master_line = MasterLine::whereIn('master_area_id', function ($q) {
            $q->select('id')->from('master_areas')->where('name', 'SAMPLE ROOM');
        })
            ->get();
        $collect_master_line = collect($master_line);

        $line = ['LINE ACENG', 'LINE OTONG', 'LINE YANTI', 'LINE SUNARI', 'LINE SMS MURNI', 'LINE SMS YANTI', 'LINE SRIWIJAYANING', 'CNC', 'FINISHING'];

        $master_needle = MasterNeedle::where('is_sample', 1)->get();
        foreach ($master_needle as $mn) {
            $d = new stdClass;
            $d->code = $mn->code;
            $d->tipe = $mn->tipe;
            $d->size = $mn->size;
            $monthly = $collect_master_monthly_stock->where('master_needle_id', $mn->id);
            $d->min_stock = $monthly->value('min_stock') ?? 0;
            $d->max_stock = $monthly->value('max_stock') ?? 0;
            $day = $collect_needle->where('master_needle_id', $mn->id)->count();
            $morning = $collect_master_morning_stock->where('master_needle_id', $mn->id);
            $morning_stock = $morning->value('value') ?? 0;
            $d->morning_stock = $morning_stock - $day;
            $incoming_stock = $collect_history_add_stock->where('stock.master_needle_id', $mn->id)->sum('qty');
            $d->incoming_stock = $incoming_stock;
            $deformed = 0;
            $change = 0;
            for ($i = 1; $i <= 9; $i++) {
                $l = $collect_master_line->where('name', $line[$i - 1])->value('id');
                $n = $collect_needle->where('master_line_id', $l)->where('master_needle_id', $mn->id);
                $d->{'deformed_' . $i} = $n->where('master_status_id', 1)->count();
                $deformed += $n->where('master_status_id', 1)->count();
                $d->{'change_' . $i} = $n->whereIn('master_status_id', [2, 3])->count();
                $change += $n->whereIn('master_status_id', [2, 3])->count();
                $u = [];
                foreach ($n->whereIn('master_status_id', [1, 2, 3])->values()->all() as $v) {
                    if (in_array($v->user->name, $u)) {
                        continue;
                    }

                    $u[] = $v->user->name;
                }
                $d->{'operator_' . $i} = implode(', ', $u);
            }
            $d->sub_total = $deformed + $change;
            $missing_fragment = $collect_needle->where('master_needle_id', $mn->id)->where('master_status_id', 4)->count();
            $line_sample = $missing_fragment;
            $d->line_sample = $line_sample;
            $u = [];
            foreach ($collect_needle->where('master_needle_id', $mn->id)->where('master_status_id', 4)->values()->all() as $v) {
                if (in_array($v->user->name, $u)) {
                    continue;
                }

                $u[] = $v->user->name;
            }
            $d->operator_line_sample = implode(', ', $u);
            $grand_total = $deformed + $change + $missing_fragment;
            $d->grand_total = $grand_total;
            $d->total_broken = $deformed;
            $d->total_tumpul = $change;
            $d->missing_fragment_total = $missing_fragment;
            $d->end_of_stock_update = (($morning_stock - $day) + $incoming_stock) - ($grand_total);
            $data[] = $d;
        }

        return $data;
    }
}
