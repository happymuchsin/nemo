<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ListApp;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use stdClass;

class HelperController extends Controller
{
    static function period($filter_period, $filter_range_date, $filter_daily, $filter_weekly, $filter_month, $filter_year, $txt)
    {
        $d = new stdClass;
        if ($filter_period == 'range') {
            $range_date = explode(' - ', $filter_range_date);
            $d->start = $range_date[0] ? $range_date[0] : Carbon::today()->subMonth();
            $d->end = $range_date[1] ? $range_date[1] : Carbon::today();
            $d->range = ["$d->start 00:00:00", "$d->end 23:59:59"];
            $d->judul = $txt . ' ' . $d->start . ' - ' . $d->end;
        } else if ($filter_period == 'daily') {
            $filter_daily = $filter_daily;
            $d->range = ["$filter_daily 00:00:00", "$filter_daily 23:59:59"];
            $d->start = Carbon::parse($filter_daily);
            $d->end = Carbon::parse($filter_daily);
            $d->judul = $txt . ' ' . $filter_daily;
        } else if ($filter_period == 'weekly') {
            $filter_weekly = $filter_weekly;
            $x = explode('-W', $filter_weekly);
            $year = $x[0];
            $week = $x[1];
            $d->start = Carbon::now()->setISODate($year, $week)->startOfWeek();
            $d->end = Carbon::now()->setISODate($year, $week)->endOfWeek();
            $d->range = [$d->start . ' 00:00:00', $d->end . ' 23:59:59'];
            $d->judul = $txt . ' ' . $filter_weekly;
        } else if ($filter_period == 'monthly') {
            $filter_monthly = $filter_month;
            $x = explode('-', $filter_monthly);
            $tahun = $x[0];
            $bulan = $x[1];
            $lastDay = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
            $d->range = ["$tahun-$bulan-01 00:00:00", "$tahun-$bulan-$lastDay 23:59:59"];
            $d->start = Carbon::parse("$tahun-$bulan-01");
            $d->end = Carbon::parse("$tahun-$bulan-$lastDay");
            $d->judul = $txt . ' ' . $filter_month;
        } else if ($filter_period == 'yearly') {
            $filter_yearly = $filter_year;
            $d->range = ["$filter_yearly-01-01 00:00:00", "$filter_yearly-12-31 23:59:59"];
            $d->start = Carbon::parse("$filter_yearly-01-01");
            $d->end = Carbon::parse("$filter_yearly-12-31");
            $d->judul = $txt . ' ' . $filter_year;
        }

        return $d;
    }

    static function version()
    {
        $listApp = ListApp::where('app', 'nemo')->orderByRaw("CAST(SUBSTRING_INDEX(version, '.', 1) AS UNSIGNED) DESC,
                CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(version, '.', -2), '.', 1) AS UNSIGNED) DESC,
                CAST(SUBSTRING_INDEX(version, '.', -1) AS UNSIGNED) DESC")->first();

        if ($listApp) {
            $version = $listApp->version;
        } else {
            $version = '1.0.0';
        }
        return $version;
    }

    static function numberToLetters($num)
    {
        $numeric = $num - 1;
        $letter = '';
        while ($numeric >= 0) {
            $letter = chr($numeric % 26 + 65) . $letter;
            $numeric = intval($numeric / 26) - 1;
        }
        return $letter;
    }

    static function emitEvent($event, $data)
    {
        $client = new Client();

        $d = new stdClass;

        try {
            $r = $client->request('POST', 'http://localhost:3000/emit', [
                'json' => [
                    'event' => $event,
                    'data' => $data,
                ]
            ]);

            if ($r->getStatusCode() == 200) {
                $d->status = true;
                $d->result = 'success';
                $d->analisis = 'done';
            } else {
                $d->status = false;
                $d->result = 'error';
                $d->analisis = $r->getBody();
            }
        } catch (BadResponseException $e) {
            $d->status = false;
            $d->result = 'error';
            $d->analisis = $e->getMessage();
        }

        return $d;
    }

    static function activityLog(
        $activity,
        $reff_name,
        $event,
        $ip = null,
        $agent = null,
        $properties = null,
        $reff_id = null,
        $username = null
    ) {
        if ($username == null) {
            $username = Auth::user()->username;
        }
        ActivityLog::create([
            'activity' => $activity,
            'reff_name' => $reff_name,
            'reff_id' => $reff_id,
            'event' => $event,
            'username' => $username,
            'properties' => $properties,
            'ip_address' => $ip,
            'user_agent' => $agent,
            'created_at' => Carbon::now(),
        ]);
    }
}
