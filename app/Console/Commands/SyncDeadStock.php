<?php

namespace App\Console\Commands;

use App\Models\DeadStock;
use App\Models\MasterArea;
use App\Models\MasterNeedle;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncDeadStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SyncDeadStock';

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
        $master_area = MasterArea::get();
        $master_needle = MasterNeedle::get();
        foreach ($master_area as $ma) {
            foreach ($master_needle as $mn) {
                $d = DeadStock::where('master_area_id', $ma->id)->where('master_needle_id', $mn->id)->first();
                if (!$d) {
                    DeadStock::create([
                        'master_area_id' => $ma->id,
                        'master_needle_id' => $mn->id,
                        'in' => 0,
                        'out' => 0,
                        'created_by' => 1,
                        'created_at' => $now,
                    ]);
                }
            }
        }
    }
}
