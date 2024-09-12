<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\MasterStyle;
use Illuminate\Http\Request;
use stdClass;

class StyleController extends Controller
{
    public function get(Request $request)
    {
        $srf = $request->srf;

        $s = MasterStyle::with(['buyer'])->where('srf', $srf)->first();
        if ($s) {
            $d = new stdClass;
            $d->buyer = $s->buyer->name;
            $d->season = $s->season;
            $d->style = $s->name;
            $d->id = $s->id;
            return new ApiResource(200, 'success', $d);
        } else {
            return new ApiResource(422, 'SRF not found', '');
        }
    }
}
