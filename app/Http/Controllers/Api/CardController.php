<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ClosingController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Http\Resources\ApiResource;
use App\Models\Approval;
use App\Models\HistoryOutStock;
use App\Models\MasterBox;
use App\Models\MasterLine;
use App\Models\MasterPlacement;
use App\Models\MasterStatus;
use App\Models\Needle;
use App\Models\Stock;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class CardController extends Controller
{
    public function person(Request $request)
    {
        $rfid = $request->rfid;
        $area_id = $request->area_id;
        $lokasi_id = $request->lokasi_id;
        $approval = $request->approval;
        $now = Carbon::now();

        if ($rfid) {
            $u = User::where('rfid', $rfid)->first();
            if ($u) {
                $tipe = $request->tipe;
                if ($tipe == 'approval') {
                    $s = Approval::with(['approval'])->where('id', $approval)->first();
                    if ($s) {
                        if ($s->approval->user_id != $u->id) {
                            return new ApiResource(422, 'User Approval not registered in this Data', '');
                        } else {
                            $s->status = 'APPROVE';
                            $s->approve = $now;
                            $s->updated_by = $u->username;
                            $s->updated_at = $now;
                            $s->save();
                            return new ApiResource(200, 'success', '');
                        }
                    } else {
                        return new ApiResource(422, 'Approval not found', '');
                    }
                } else {
                    $placement = MasterPlacement::where('user_id', $u->id)->first();
                    if (!$placement) {
                        return new ApiResource(422, 'Please assign Placement First !!!', '');
                    }
                    if ($placement->reff == 'line') {
                        if ($lokasi_id != $placement->counter_id) {
                            return new ApiResource(422, 'Please go to the listed Counter', '');
                        } else {
                            $lokasi = MasterLine::where('id', $placement->location_id)->first();
                            $x = new stdClass;
                            $x->username = $u->username;
                            $x->name = $u->name;
                            $x->rfid = $u->rfid;
                            $x->line = $lokasi->name;

                            $history = [];
                            $needle = Needle::with(['needle'])
                                ->where('user_id', $u->id)
                                ->orderBy('created_at', 'desc')
                                ->limit(10)
                                ->get();
                            foreach ($needle as $n) {
                                $history[] = [
                                    'created_at' => date('Y-m-d H:i:s', strtotime($n->created_at)),
                                    'brand' => $n->needle->brand,
                                    'tipe' => $n->needle->tipe,
                                    'size' => $n->needle->size,
                                    'code' => $n->needle->code,
                                ];
                            }
                            $x->history = $history;
                            return new ApiResource(200, 'success', $x);
                        }
                    } else {
                        return new ApiResource(422, 'For Placement Line Only', '');
                    }
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
        $now = Carbon::now();

        if ($rfid) {
            $tipe = $request->tipe;
            $b = MasterBox::where('rfid', $rfid)->first();
            if ($b) {
                if ($tipe == 'approval') {
                    $mode = $request->mode;
                    $approval = $request->approval;
                    $username = $request->username;
                    $scan_rfid = $request->scan_rfid;
                    $scan_box = $request->scan_box;
                    $s = Approval::where('id', $approval)->first();
                    if ($s) {
                        if ($s->status == 'APPROVE') {
                            DB::beginTransaction();

                            $stat = MasterStatus::where('name', 'BROKEN MISSING FRAGMENT')->first();

                            if ($s->master_needle_id && $mode == 'tetap') {
                                $master_needle_id = $s->master_needle_id;
                                $in = Stock::where('master_box_id', $b->id)->where('master_needle_id', $master_needle_id)->where('is_clear', 'not')->sum('in');
                                $out = Stock::where('master_box_id', $b->id)->where('master_needle_id', $master_needle_id)->where('is_clear', 'not')->sum('out');

                                if ($in <= $out) {
                                    return new ApiResource(422, 'Stock in Box is empty !!!', '');
                                }

                                $stock = Stock::where('master_box_id', $b->id)->where('master_needle_id', $master_needle_id)->whereRaw('`in` > `out`')->where('is_clear', 'not')->orderBy('created_at')->first();
                            } else {
                                $master_needle_id = null;
                                $in = Stock::where('master_box_id', $b->id)->where('is_clear', 'not')->sum('in');
                                $out = Stock::where('master_box_id', $b->id)->where('is_clear', 'not')->sum('out');

                                if ($in <= $out) {
                                    return new ApiResource(422, 'Stock in Box is empty !!!', '');
                                }

                                $stock = Stock::where('master_box_id', $b->id)->whereRaw('`in` > `out`')->where('is_clear', 'not')->orderBy('created_at')->first();
                                if ($mode == 'ubah') {
                                    $master_needle_id = $stock->master_needle_id;
                                }
                            }

                            Needle::create([
                                'user_id' => $s->user_id,
                                'master_line_id' => $s->master_line_id,
                                'master_style_id' => $s->master_style_id,
                                'master_box_id' => $b->id,
                                'master_needle_id' => $master_needle_id,
                                'master_status_id' => $stat->id,
                                'scan_rfid' => $scan_rfid,
                                'scan_box' => $scan_box,
                                'status' => '',
                                'remark' => '',
                                'created_by' => $username,
                                'created_at' => $now,
                            ]);
                            HelperController::activityLog("ANDROID CREATE NEEDLE", 'needles', 'create', $request->ip(), $request->userAgent(), json_encode([
                                'user_id' => $s->user_id,
                                'master_line_id' => $s->master_line_id,
                                'master_style_id' => $s->master_style_id,
                                'master_box_id' => $b->id,
                                'master_needle_id' => $master_needle_id,
                                'master_status_id' => $stat->id,
                                'scan_rfid' => $scan_rfid,
                                'scan_box' => $scan_box,
                                'status' => '',
                                'remark' => '',
                                'created_by' => $username,
                                'created_at' => $now,
                            ]), null, $username);

                            $x = Stock::where('id', $stock->id)->first();

                            Stock::where('id', $stock->id)->update([
                                'out' => $x->out + 1,
                                'updated_by' => $username,
                                'updated_at' => $now,
                            ]);
                            HelperController::activityLog("ANDROID UPDATE STOCK", 'stocks', 'update', $request->ip(), $request->userAgent(), json_encode([
                                'id' => $stock->id,
                                'out' => $x->out + 1,
                                'updated_by' => $username,
                                'updated_at' => $now,
                            ]), $stock->id, $username);

                            HistoryOutStock::create([
                                'stock_id' => $stock->id,
                                'stock_before' => $x->in - $x->out,
                                'qty' => 1,
                                'stock_after' => $x->in - $x->out - 1,
                                'created_by' => $username,
                                'created_at' => $now,
                            ]);
                            HelperController::activityLog("ANDROID CREATE HISTORY OUT STOCK", 'history_out_stocks', 'create', $request->ip(), $request->userAgent(), json_encode([
                                'stock_id' => $stock->id,
                                'stock_before' => $x->in - $x->out,
                                'qty' => 1,
                                'stock_after' => $x->in - $x->out - 1,
                                'created_by' => $username,
                                'created_at' => $now,
                            ]), null, $username);

                            $closing = ClosingController::generateStockReport($now, $now->today(), $now->today(), $master_needle_id);
                            if ($closing != 'sukses') {
                                DB::rollBack();
                                return new ApiResource(422, 'Sync closing failed', '');
                            }

                            $s->status = 'DONE';
                            $s->updated_at = $now;
                            $s->updated_by = $username;
                            $s->save();

                            DB::commit();

                            return new ApiResource(200, 'Successfully', '');
                        } else {
                            return new ApiResource(422, 'Need Approval', '');
                        }
                    } else {
                        return new ApiResource(422, 'Approval not found', '');
                    }
                } else if ($tipe == 'return') {
                    if ($b->tipe != 'RETURN') {
                        return new ApiResource(422, 'This is not Box Return', '');
                    }

                    return new ApiResource(200, 'success', [
                        'box' => $b,
                    ]);
                } else {
                    if ($b->tipe == 'RETURN') {
                        return new ApiResource(422, 'This is not Box Normal', '');
                    }

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
