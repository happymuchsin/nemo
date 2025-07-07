<?php

namespace App\Console\Commands;

use App\Models\MasterHoliday;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncHoliday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SyncHoliday';

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
        $bulan = $now->month;
        $tahun = $now->year;

        // self::syncYear($now, $tahun);
        self::syncMonth($now, $tahun, $bulan);
    }

    static function syncYear($now, $tahun)
    {
        for ($i = 1; $i <= 12; $i++) {
            self::syncMonth($now, $tahun, $i);
        }
    }

    static function syncMonth($now, $tahun, $bulan)
    {
        $master_holiday = MasterHoliday::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->first();
        if (!$master_holiday) {
            for ($i = 1; $i <= cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun); $i++) {
                $tanggal = date('Y-m-d', strtotime($tahun . '-' . $bulan . '-' . $i));
                $day = date('D', strtotime($tanggal));
                if ($day == 'Sat' || $day == 'Sun') {
                    MasterHoliday::create([
                        'tanggal' => $tanggal,
                        'description' => 'Day Off',
                        'created_by' => 'developer',
                        'created_at' => $now,
                    ]);
                }
            }
        }
    }
}
