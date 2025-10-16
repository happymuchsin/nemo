<?php

namespace App\Http\Controllers;

use App\Models\DailyClosing;
use App\Models\HistoryAddStock;
use App\Models\MasterNeedle;
use App\Models\MasterStatus;
use App\Models\Needle;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class ClosingController extends Controller
{
    static function generateStockReport($now, $startDate, $endDate, $master_needle_id)
    {
        try {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);

            DB::statement("
                UPDATE needles
                JOIN (
                    SELECT id
                    FROM (
                        SELECT id,
                            ROW_NUMBER() OVER (PARTITION BY created_at ORDER BY id) AS rn
                        FROM needles
                        WHERE deleted_at IS null
                    ) t
                    WHERE t.rn > 1
                ) dups ON needles.id = dups.id
                SET needles.deleted_at = '$now';
            ");

            DB::beginTransaction();

            $stat = MasterStatus::where('name', '!=', 'RETURN')->pluck('id');

            $master_needle = MasterNeedle::when($master_needle_id, function ($q) use ($master_needle_id) {
                $q->where('id', $master_needle_id);
            })
                ->get();
            while ($start->lte($end)) {
                $tanggal = $start->toDateString();
                $kemarin = Carbon::parse($tanggal)->subDay()->toDateString();
                foreach ($master_needle as $m) {
                    $in = HistoryAddStock::whereDate('created_at', $tanggal)
                        ->whereIn('stock_id', fn($q) => $q->select('id')->from('stocks')->where('master_needle_id', $m->id)->whereNull('status'))
                        ->sum('qty');
                    $out = Needle::whereDate('created_at', $tanggal)
                        ->where('master_needle_id', $m->id)
                        ->whereIn('master_status_id', $stat)
                        ->count();

                    $dc = DailyClosing::where('master_needle_id', $m->id)
                        ->whereDate('tanggal', $kemarin)
                        ->first();

                    $opening = $dc ? $dc->closing : 0;
                    $closing = $opening + $in - $out;

                    $dc = DailyClosing::where('master_needle_id', $m->id)
                        ->whereDate('tanggal', $tanggal)
                        ->first();
                    if ($dc) {
                        $dc->opening = $opening;
                        $dc->in = $in;
                        $dc->out = $out;
                        $dc->closing = $closing;
                        $dc->updated_by = 1;
                        $dc->updated_at = $now;
                        $dc->save();
                    } else {
                        DailyClosing::create([
                            'master_needle_id' => $m->id,
                            'tanggal' => $tanggal,
                            'opening' => $opening,
                            'in' => $in,
                            'out' => $out,
                            'closing' => $closing,
                            'created_by' => 1,
                            'created_at' => $now,
                        ]);
                    }
                }
                $start->addDay();
            }

            DB::commit();
            return 'sukses';
        } catch (Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }
}
