<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\MasterApproval;
use App\Models\MasterBuyer;
use App\Models\MasterLine;
use App\Models\MasterNeedle;
use App\Models\MasterPlacement;
use App\Models\MasterStyle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use stdClass;

class SpinnerController extends Controller
{
    public function spinner(Request $request)
    {
        $area_id = $request->area_id;
        $tipe = $request->tipe;
        $x = $request->x;
        if ($tipe == 'line') {
            $data = MasterLine::selectRaw('id, name')->where('master_area_id', $area_id)->get();
        } else if ($tipe == 'buyer') {
            $data = MasterBuyer::selectRaw('id, name')->get();
        } else if ($tipe == 'style') {
            $data = MasterStyle::selectRaw('id, CONCAT(srf, " - ", name) as name')->where('master_buyer_id', $x)->get();
        } else if ($tipe == 'approval') {
            $data = [];
            if (Config::get('app.env') == 'local') {
                $s = MasterApproval::with(['user'])->get();
            } else {
                $s = MasterApproval::with(['user'])->where('user_id', '!=', '1')->get();
            }
            foreach ($s as $s) {
                $d = new stdClass;
                $d->id = $s->user_id;
                $d->name = $s->user->username . ' - ' . $s->user->name;
                $data[] = $d;
            }
        } else if ($tipe == 'brand') {
            $data = [];
            $s = MasterNeedle::select('brand')->groupBy('brand')->get();
            foreach ($s as $s) {
                $d = new stdClass;
                $d->id = $s->brand;
                $d->name = $s->brand;
                $data[] = $d;
            }
        } else if ($tipe == 'tipe') {
            $data = [];
            $s = MasterNeedle::select('tipe')->where('brand', $x)->groupBy('tipe')->get();
            foreach ($s as $s) {
                $d = new stdClass;
                $d->id = $s->tipe;
                $d->name = $s->tipe;
                $data[] = $d;
            }
        } else if ($tipe == 'size') {
            $brand = $request->brand;
            $data = [];
            $s = MasterNeedle::select('size')->where('brand', $brand)->where('tipe', $x)->groupBy('size')->get();
            foreach ($s as $s) {
                $d = new stdClass;
                $d->id = $s->size;
                $d->name = $s->size;
                $data[] = $d;
            }
        } else if ($tipe == 'code') {
            $brand = $request->brand;
            $type = $request->type;
            $data = [];
            $s = MasterNeedle::select('id', 'code')->where('brand', $brand)->where('tipe', $type)->where('size', $x)->groupBy('code')->get();
            foreach ($s as $s) {
                $d = new stdClass;
                $d->id = $s->id;
                $d->name = $s->code;
                $data[] = $d;
            }
        }
        return new ApiResource(200, 'success', $data);
    }
}
