<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

        $data = [];
        $s = Approval::with(['user', 'needle' => function ($q) {
            $q->with(['line', 'style', 'box', 'needle']);
        }])
            ->where('master_area_id', $area_id)
            ->where('master_counter_id', $lokasi_id)
            ->whereNotIn('needle_id', function ($q) {
                $q->from('needle_details')
                    ->select('needle_id')
                    ->whereIn('master_status_id', function ($q1) {
                        $q1->from('master_statuses')
                            ->select('id')
                            ->where('name', 'REPLACEMENT');
                    });
            })
            ->orWhere('status', '!=', 'DONE')
            ->get();
        foreach ($s as $s) {
            $d = new stdClass;
            $d->id = $s->id;
            $d->username = $s->user->username;
            $d->name = $s->user->name;
            $d->status = $s->status;
            $d->idCard = $s->user->rfid;
            $d->line = $s->needle->line->name;
            $d->lineId = $s->needle->line->id;
            $d->style = $s->needle->style->name;
            $d->styleId = $s->needle->style->id;
            $d->brand = $s->needle->needle->brand;
            $d->tipe = $s->needle->needle->tipe;
            $d->size = $s->needle->needle->size;
            $d->boxCard = $s->needle->box->rfid;
            $d->needleId = $s->needle->needle->id;
            $created_at = Carbon::parse($s->created_at);
            if (strlen($created_at->month) == 1) {
                $month = '0' . $created_at->month;
            } else {
                $month = $created_at->month;
            }
            $d->gambar = base64_encode(file_get_contents("assets/uploads/needle/$created_at->year/$month/$s->needle_id/$s->id.$s->ext"));
            $data[] = $d;
        }

        return new ApiResource(200, 'success', $data);
    }
}
