<?php

namespace App\Console\Commands;

use App\Models\DailyClosing;
use App\Models\MasterNeedle;
use App\Models\Needle;
use App\Models\Stock;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoClosing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AutoClosing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $start = '2025-05-01';
        $end = '2025-05-31';
        // $start = $now->today();
        // $end = $now->today();

        $x = self::generateStockReport($now, $start, $end);

        echo $x;
    }

    static function generateStockReport($now, $startDate, $endDate)
    {
        try {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);

            DB::beginTransaction();

            $master_needle = MasterNeedle::get();
            while ($start->lte($end)) {
                $tanggal = $start->toDateString();
                $kemarin = Carbon::parse($tanggal)->subDay()->toDateString();
                foreach ($master_needle as $m) {
                    $in = Stock::whereDate('created_at', $tanggal)
                        ->where('master_needle_id', $m->id)
                        ->sum('in');
                    $out = Needle::whereDate('created_at', $tanggal)
                        ->where('master_needle_id', $m->id)
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
