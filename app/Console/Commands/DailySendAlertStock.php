<?php

namespace App\Console\Commands;

use App\Mail\DailyAlertStock;
use App\Models\MasterNeedle;
use App\Models\Warehouse;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use stdClass;

class DailySendAlertStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DailySendAlertStock';

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
            Log::channel('DailySendAlertStock')->info('START DAILY SEND ALERT STOCK ' . Carbon::now()->toDateTimeString());
            $to = [
                'adm.sample@anggunkreasi.com'
            ];

            $cc = [
                'arasu@anggunkreasi.com'
            ];

            $data  = [];

            $master_needle = MasterNeedle::where('is_sample', 1)->orderBy('tipe')->orderBy('size')->get();
            foreach ($master_needle as $m) {
                $d = new stdClass;
                $d->brand = $m->brand;
                $d->tipe = $m->tipe;
                $d->size = $m->size;
                $d->code = $m->code;
                $d->min = $m->min_stock;

                $in = Warehouse::where('master_needle_id', $m->id)->sum('in');
                $out = Warehouse::where('master_needle_id', $m->id)->sum('out');

                if ($in - $out <= $m->min_stock) {
                    $d->stock = $in - $out;
                } else {
                    continue;
                }

                $data[] = $d;
            }

            if (count($data) > 0) {
                Mail::to($to)->cc($cc)->send(new DailyAlertStock($data));
            }
            Log::channel('DailySendAlertStock')->info('END DAILY SEND ALERT STOCK ' . Carbon::now()->toDateTimeString());
        } catch (Exception $e) {
            Log::channel('DailySendAlertStock')->info('ERROR DAILY SEND ALERT STOCK ' . $e->getMessage() . ' ' . Carbon::now()->toDateTimeString());
            Log::channel('DailySendAlertStock')->info('END DAILY SEND ALERT STOCK ' . Carbon::now()->toDateTimeString());
        }
    }
}
