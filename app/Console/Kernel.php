<?php

namespace App\Console;

use App\Console\Commands\AutoClosing;
use App\Console\Commands\DailySendAlertStock;
use App\Console\Commands\SyncHoliday;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command(AutoClosing::class)->everyFiveMinutes()->withoutOverlapping()->runInBackground();
        $schedule->command(DailySendAlertStock::class)->dailyAt('07:00')->withoutOverlapping()->runInBackground();
        $schedule->command(DailySendAlertStock::class)->dailyAt('12:00')->withoutOverlapping()->runInBackground();
        $schedule->command(SyncHoliday::class)->dailyAt('00:00')->withoutOverlapping()->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
