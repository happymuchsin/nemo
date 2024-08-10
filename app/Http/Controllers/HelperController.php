<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use stdClass;

class HelperController extends Controller
{
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
}
