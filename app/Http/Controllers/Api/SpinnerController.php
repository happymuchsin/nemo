<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\MasterApproval;
use App\Models\MasterLine;
use App\Models\MasterPlacement;
use App\Models\MasterStyle;
use App\Models\User;
use Illuminate\Http\Request;
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
        } else if ($tipe == 'style') {
            $data = MasterStyle::selectRaw('id, name')->get();
        } else if ($tipe == 'approval') {
            $data = [];
            $s = MasterApproval::with(['user'])->get();
            foreach ($s as $s) {
                $d = new stdClass;
                $d->id = $s->user_id;
                $d->name = $s->user->username . ' - ' . $s->user->name;
                $data[] = $d;
            }
        }
        return new ApiResource(200, 'success', $data);
    }
}
