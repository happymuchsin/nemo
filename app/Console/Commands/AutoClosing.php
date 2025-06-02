<?php

namespace App\Console\Commands;

use App\Http\Controllers\ClosingController;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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
        // $start = '2025-05-01';
        // $end = '2025-05-31';
        $start = $now->today();
        $end = $now->today();

        try {
            Log::channel('AutoDailyClosing')->info('START DAILY CLOSING ' . Carbon::now()->toDateTimeString());
            $x = ClosingController::generateStockReport($now, $start, $end, null);
            if ($x == 'sukses') {
                Log::channel('AutoDailyClosing')->info('END DAILY CLOSING ' . Carbon::now()->toDateTimeString());
            } else {
                Log::channel('AutoDailyClosing')->info('ERROR DAILY CLOSING ' . $x . ' ' . Carbon::now()->toDateTimeString());
                Log::channel('AutoDailyClosing')->info('END DAILY CLOSING ' . Carbon::now()->toDateTimeString());
            }
        } catch (Exception $e) {
            Log::channel('AutoDailyClosing')->info('ERROR DAILY CLOSING ' . $e->getMessage() . ' ' . Carbon::now()->toDateTimeString());
            Log::channel('AutoDailyClosing')->info('END DAILY CLOSING ' . Carbon::now()->toDateTimeString());
        }
    }
}
