<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\Approval;
use App\Models\MasterBox;
use App\Models\Needle;
use App\Models\Stock;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use stdClass;

class CardController extends Controller
{
    public function person(Request $request)
    {
        $rfid = $request->rfid;

        if ($rfid) {
            $u = User::where('rfid', $rfid)->first();
            if ($u) {
                $tipe = $request->tipe;
                // if ($tipe == 'return') {
                //     $s = Needle::with(['line', 'style', 'box', 'needle'])
                //         ->where('user_id', $u->id)
                //         ->where('status', 'new')
                //         ->orderBy('created_at', 'desc')
                //         ->first();
                //     if ($s) {
                //         return new ApiResource(200, 'success', [
                //             'user' => $u,
                //             'needle' => $s,
                //         ]);
                //     } else {
                //         return new ApiResource(422, 'No returnable needles', '');
                //     }
                // } 
                if ($tipe == 'approval') {
                    $area_id = $request->area_id;
                    $lokasi_id = $request->lokasi_id;
                    $s = Approval::with(['user', 'needle' => function ($q) {
                        $q->with(['line', 'style', 'box', 'needle']);
                    }])
                        ->whereNotNull('needle_id')
                        ->where('user_id', $u->id)
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
                        ->where('status', '!=', 'DONE')
                        ->first();
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

                    return new ApiResource(200, 'success', $d);
                } else {
                    return new ApiResource(200, 'success', $u);
                }
            } else {
                return new ApiResource(422, 'RFID not found', '');
            }
        } else {
            return new ApiResource(422, 'Please scan ID Card', '');
        }
    }

    public function box(Request $request)
    {
        $rfid = $request->rfid;

        if ($rfid) {
            $tipe = $request->tipe;
            $b = MasterBox::where('rfid', $rfid)->first();
            if ($b) {
                if ($tipe == 'return') {
                    if ($b->tipe != 'RETURN') {
                        return new ApiResource(422, 'This is not Box Return', '');
                    }

                    return new ApiResource(200, 'success', [
                        'box' => $b,
                    ]);
                } else {
                    $s = Stock::with(['needle'])->where('master_box_id', $b->id)->where('is_clear', 'not')->first();
                    if ($s) {
                        $in = Stock::where('master_box_id', $b->id)->where('is_clear', 'not')->sum('in');
                        $out = Stock::where('master_box_id', $b->id)->where('is_clear', 'not')->sum('out');
                        if ($in <= $out) {
                            return new ApiResource(422, 'Box is Empty', '');
                        }

                        return new ApiResource(200, 'success', [
                            'box' => $b,
                            'stock' => $s,
                        ]);
                    } else {
                        return new ApiResource(422, 'Stock in Box is Empty', '');
                    }
                }
            } else {
                return new ApiResource(422, 'RFID not found', '');
            }
        } else {
            return new ApiResource(422, 'Please scan Box Card', '');
        }
    }
}
