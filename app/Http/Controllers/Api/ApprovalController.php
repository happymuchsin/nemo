<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Http\Resources\ApiResource;
use App\Models\Approval;
use Carbon\Carbon;
use Illuminate\Http\Request;
use stdClass;

class ApprovalController extends Controller
{
    public function data(Request $request)
    {
        $username = $request->username;
        $area_id = $request->area_id;
        $lokasi_id = $request->lokasi_id;

        HelperController::activityLog('ANDROID OPEN APPROVAL', 'approvals', 'read', $request->ip(), $request->userAgent(), null, null, $username);

        $data = [];
        $s = Approval::with(['user', 'needle' => function ($q) {
            $q->with(['box', 'needle']);
        }, 'approval' => function ($q) {
            $q->with(['user']);
        }, 'master_line', 'master_style' => function ($q) {
            $q->with(['buyer']);
        }])
            ->where('master_area_id', $area_id)
            ->where('master_counter_id', $lokasi_id)
            ->where('status', '!=', 'DONE')
            ->get();
        foreach ($s as $s) {
            $d = new stdClass;
            $d->id = $s->id;
            $d->username = $s->user->username;
            $d->name = $s->user->name;
            $d->status = $s->status;
            $d->idCard = $s->user->rfid;
            $d->line = $s->master_line->name;
            $d->lineId = $s->master_line->id;
            $d->buyer = $s->master_style->buyer->name;
            $d->srf = $s->master_style->srf;
            $d->style = $s->master_style->name;
            $d->styleId = $s->master_style->id;
            if ($s->needle) {
                $d->brand = $s->needle->needle->brand;
                $d->tipe = $s->needle->needle->tipe;
                $d->size = $s->needle->needle->size;
                $d->boxCard = $s->needle->box->rfid;
                $d->needleId = $s->needle->needle->id;
            } else {
                $d->brand = '';
                $d->tipe = '';
                $d->size = '';
                $d->boxCard = '';
                $d->needleId = 0;
            }
            $d->requestDate = date('Y-m-d', strtotime($s->created_at));
            $d->requestTime = date('H:i:s', strtotime($s->created_at));
            $d->approvalName = $s->approval->user->name;
            $d->approvalUsername = $s->approval->user->username;
            $created_at = Carbon::parse($s->created_at);
            if (strlen($created_at->month) == 1) {
                $month = '0' . $created_at->month;
            } else {
                $month = $created_at->month;
            }
            $d->gambar = base64_encode(file_get_contents("assets/uploads/needle/$created_at->year/$month/$s->id.$s->ext"));
            $data[] = $d;
        }

        return new ApiResource(200, 'success', $data);
    }
}
