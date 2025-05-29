<?php

namespace App\Console\Commands;

use App\Models\Needle;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Console\Command;

class aTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'atest';

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
        $start = Carbon::parse('2025-05-29');
        $end = Carbon::parse('2025-05-29');

        $stock_report = self::generateStockReport($start->subDays(10), $end, 10, 'all');

        print_r($stock_report->value('in'));
    }

    static function generateStockReport($startDate, $endDate, $master_needle_id, $master_status_id)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $report = [];
        $closingKemarin = 0;

        while ($start->lte($end)) {
            $tanggal = $start->toDateString();

            $in = Stock::whereDate('created_at', $tanggal)
                ->where('master_needle_id', $master_needle_id)
                ->sum('in');
            $out = Needle::whereDate('created_at', $tanggal)
                ->where('master_needle_id', $master_needle_id)
                ->when($master_status_id == 'all', function ($q) {
                    $q->whereIn('master_status_id', [1, 2, 3, 4]);
                })
                ->when($master_status_id != 'all', function ($q) use ($master_status_id) {
                    $q->where('master_status_id', $master_status_id);
                })
                ->count();

            $opening = $closingKemarin;
            $closing = $opening + $in - $out;

            $report[] = [
                'tanggal' => $tanggal,
                'opening' => $opening,
                'out' => $out,
                'in' => $in,
                'closing' => $closing,
            ];

            // Set closing hari ini untuk jadi opening besok
            $closingKemarin = $closing;
            $start->addDay();
        }

        return collect($report);
    }
}
