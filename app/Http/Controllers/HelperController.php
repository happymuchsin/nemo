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
