<?php

namespace App\Console\Commands;

use App\Models\DetailAdjustment;
use App\Models\HistoryAddStock as HAS;
use App\Models\Stock;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class HistoryAddStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'HistoryAddStock';

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
        try {
            DB::beginTransaction();
            $now = Carbon::now();
            $recent = Carbon::parse('2025-08-15 09:03:15');
            $detail_adjustment = DetailAdjustment::where('adjustment_id', '9fa2ee70-e0c3-4549-8453-ef8f82b47573')->get();
            foreach ($detail_adjustment as $da) {
                $stock = Stock::where('master_area_id', $da->master_area_id)
                    ->where('master_counter_id', $da->master_counter_id)
                    ->where('master_box_id', $da->master_box_id)
                    ->where('master_needle_id', $da->master_needle_id)
                    ->where('created_at', '2025-08-15 09:03:15')
                    ->get();
                foreach ($stock as $s) {
                    HAS::create([
                        'stock_id' => $s->id,
                        'stock_before' => 0,
                        'qty' => $da->after,
                        'stock_after' => $da->after,
                        'created_by' => '10094',
                        'created_at' => $recent,
                        'updated_at' => $now,
                    ]);
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage() . PHP_EOL;
        }
    }
}
