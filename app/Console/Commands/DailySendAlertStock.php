<?php

namespace App\Console\Commands;

use App\Mail\DailyAlertStock;
use App\Models\MasterNeedle;
use App\Models\Warehouse;
use Illuminate\Console\Command;
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
        $to = [
            'adm.sample@anggunkreasi.com'
        ];

        $data  = [];

        $master_needle = MasterNeedle::orderBy('tipe')->orderBy('size')->get();
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
            Mail::to($to)->send(new DailyAlertStock($data));
        }
    }
}
