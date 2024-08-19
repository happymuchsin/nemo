<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Http\Resources\ApiResource;
use App\Models\Approval;
use App\Models\Needle;
use App\Models\MasterBox;
use App\Models\MasterCounter;
use App\Models\MasterLine;
use App\Models\MasterPlacement;
use App\Models\MasterStatus;
use App\Models\NeedleDetail;
use App\Models\Stock;
use App\Models\User;
use App\Notifications\ApprovalNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use stdClass;

class NeedleController extends Controller
{
    public function save(Request $request)
    {
        $tipe = $request->tipe;
        $idCard = $request->idCard;
        $line = $request->line;
        $style = $request->style;
        $boxCard = $request->boxCard;
        $needle = $request->needle;
        $username = $request->username;
        $status = strtoupper($request->status);
        $filename = $request->filename;
        $ext = $request->ext;
        $img = $request->gambar;
        if ($img) {
            $gambar = base64_decode($img);
        }
        $now = Carbon::now();

        $approvalId = $request->approvalId;

        try {
            $user = User::where('rfid', $idCard)->first();
            $box = MasterBox::where('rfid', $boxCard)->first();
            if ($status == 'RETURN') {
                if ($box->tipe == 'RETURN') {
                    $ss = $box->status;
                }
                $stat = MasterStatus::where('name', $status . ' ' . $ss)->first();
            } else {
                $stat = MasterStatus::where('name', $status)->first();
            }

            if ($status == 'RETURN') {
                $needle_id = null;
            } else {
                $s = Needle::where('user_id', $user->id)->where('status', 'new')->first();
                if ($s) {
                    if ($status == 'REQUEST NEW') {
                        $a = Approval::where('user_id', $user->id)->where('tipe', 'request-new')->first();
                        if ($a) {
                            if ($a->status == 'WAITING') {
                                return new ApiResource(422, 'Waiting Approval Request', '');
                            } else if ($a->status == 'REJECT') {
                                return new ApiResource(422, 'Previous Request Rejected, Create New Approval Request', 'approval');
                            } else {
                                return new ApiResource(422, 'User cannot request again, Create Approval Request', 'approval');
                            }
                        } else {
                            return new ApiResource(422, 'User cannot request again, Create Approval Request', 'approval');
                        }
                    }

                    $needle_id = $s->id;
                } else {
                    if ($status != 'REQUEST NEW') {
                        return new ApiResource(422, 'User does not have a needle !!!', '');
                    }

                    $needle_id = null;
                }
            }

            DB::beginTransaction();

            if ($status != 'RETURN') {
                $in = Stock::where('master_box_id', $box->id)->where('master_needle_id', $needle)->where('is_clear', 'not')->sum('in');
                $out = Stock::where('master_box_id', $box->id)->where('master_needle_id', $needle)->where('is_clear', 'not')->sum('out');

                if ($in <= $out) {
                    return new ApiResource(422, 'Stock in Box is empty !!!', '');
                }
            }

            if ($status == 'RETURN') {
                $x = 'return';
            } else if ($status == 'REQUEST NEW') {
                $x = 'new';
            } else {
                $x = null;
            }

            if (!$needle_id) {
                $ins = Needle::create([
                    'user_id' => $user->id,
                    'master_line_id' => $line,
                    'master_style_id' => $style,
                    'master_box_id' => $box->id,
                    'master_needle_id' => $needle,
                    'status' => $x,
                    'created_by' => $username,
                    'created_at' => $now,
                ]);
                HelperController::activityLog("ANDROID CREATE NEEDLE", 'needles', 'create', $request->ip(), $request->userAgent(), json_encode([
                    'user_id' => $user->id,
                    'master_line_id' => $line,
                    'master_style_id' => $style,
                    'master_box_id' => $box->id,
                    'master_needle_id' => $needle,
                    'status' => $x,
                    'created_by' => $username,
                    'created_at' => $now,
                ]), null, $username);

                $needle_id = $ins->id;
            }

            $nd = NeedleDetail::create([
                'needle_id' => $needle_id,
                'master_status_id' => $stat->id,
                'filename' => $filename,
                'ext' => $ext,
                'created_by' => $username,
                'created_at' => $now,
            ]);

            HelperController::activityLog("ANDROID CREATE NEEDLE DETAIL", 'needle_details', 'create', $request->ip(), $request->userAgent(), json_encode([
                'needle_id' => $needle_id,
                'master_status_id' => $stat->id,
                'filename' => $filename,
                'ext' => $ext,
                'created_by' => $username,
                'created_at' => $now,
            ]), null, $username);

            $stock = Stock::where('master_box_id', $box->id)->where('master_needle_id', $needle)->whereRaw('`in` > `out`')->where('is_clear', 'not')->orderBy('created_at')->first();

            if ($status == 'RETURN') {
                // $a = Approval::where('needle_id', $needle_id)->where('status', '!=', 'DONE')->first();
                // if ($a) {
                //     DB::rollBack();
                //     return new ApiResource(422, 'There are needles that are still in the REPLACEMENT process', '');
                // }

                // Needle::where('id', $needle_id)->where('status', 'new')->update([
                //     'status' => 'return',
                //     'updated_by' => $username,
                //     'updated_at' => $now,
                // ]);

                // HelperController::activityLog("ANDROID UPDATE NEEDLE", 'needles', 'update', $request->ip(), $request->userAgent(), json_encode([
                //     'id' => $needle_id,
                //     'status' => 'return',
                //     'updated_by' => $username,
                //     'updated_at' => $now,
                // ]), $needle_id, $username);

                // Stock::where('id', $stock->id)->update([
                //     'out' => DB::raw("`out` - 1"),
                //     'updated_by' => $username,
                //     'updated_at' => $now,
                // ]);
                // HelperController::activityLog("ANDROID UPDATE STOCK", 'stocks', 'update', $request->ip(), $request->userAgent(), json_encode([
                //     'id' => $stock->id,
                //     'out' => 'out - 1',
                //     'updated_by' => $username,
                //     'updated_at' => $now,
                // ]), $stock->id, $username);
            } else {
                Stock::where('id', $stock->id)->update([
                    'out' => DB::raw("`out` + 1"),
                    'updated_by' => $username,
                    'updated_at' => $now,
                ]);
                HelperController::activityLog("ANDROID UPDATE STOCK", 'stocks', 'update', $request->ip(), $request->userAgent(), json_encode([
                    'id' => $stock->id,
                    'out' => 'out + 1',
                    'updated_by' => $username,
                    'updated_at' => $now,
                ]), $stock->id, $username);

                if ($status == 'REPLACEMENT') {
                    Approval::where('id', $approvalId)->update([
                        'status' => 'DONE',
                        'updated_by' => $username,
                        'updated_at' => $now,
                    ]);
                    HelperController::activityLog("ANDROID UPDATE APPROVAL", 'approvals', 'update', $request->ip(), $request->userAgent(), json_encode([
                        'id' => $approvalId,
                        'status' => 'DONE',
                        'updated_by' => $username,
                        'updated_at' => $now,
                    ]), $approvalId, $username);
                }
            }

            if ($img) {
                if (strlen($now->month) == 1) {
                    $month = '0' . $now->month;
                } else {
                    $month = $now->month;
                }

                $path = "assets/uploads/needle/$now->year/$month/$needle_id";
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                file_put_contents("$path/$nd->id.$ext", $gambar);
            }

            HelperController::emitEvent('nemo', [
                'event' => 'nemoReload',
                'tipe' => 'reload',
            ]);

            DB::commit();
            return new ApiResource(200, 'Submit Successfully', '');
        } catch (Exception $e) {
            DB::rollBack();
            // return new ApiResource(422, 'Submit Failed', '');
            return new ApiResource(422, $e->getMessage(), '');
        }
    }

    public function approval(Request $request)
    {
        $tipe = $request->tipe;
        $needle_status = $request->needleStatus;
        $idCard = $request->idCard;
        $approval = $request->approval;
        $username = $request->username;
        $reff = $request->reff;
        $area_id = $request->area_id;
        $lokasi_id = $request->lokasi_id;
        $filename = $request->filename;
        $ext = $request->ext;
        $gambar = base64_decode($request->gambar);
        $line = $request->line;
        $style = $request->style;
        $boxCard = $request->boxCard;
        $remark = $request->remark;
        $now = Carbon::now();

        try {
            $requester = User::with(['division', 'position'])->where('rfid', $idCard)->first();
            $user_id = $requester->id;
            $name = $requester->name;
            $division = $requester->division->name;
            $position = $requester->position->name;

            $mp = MasterPlacement::where('user_id', $requester->id)->first();
            if (!$mp) {
                return new ApiResource(422, 'Please set User Placement First !!!', '');
            }
            if ($mp->reff == 'line') {
                $s = MasterLine::where('id', $mp->location_id)->first();
                $lokasi = $s->name;
            } else if ($mp->reff == 'counter') {
                $s = MasterCounter::where('id', $mp->location_id)->first();
                $lokasi = $s->name;
            }

            if ($tipe == 'missing-fragment') {
                $needle = Needle::with(['line', 'style'])->where('user_id', $user_id)->where('status', 'new')->first();
                if (!$needle) {
                    return new ApiResource(422, 'User does not have a needle !!!', '');
                }
                $needle_id = $needle->id;
                $master_needle_id = $needle->master_needle_id;
                $master_line_id = $needle->master_line_id;
                $master_style_id = $needle->master_style_id;
            } else if ($tipe == 'request-new') {
                $box = MasterBox::where('rfid', $boxCard)->first();
                $stock = Stock::where('master_box_id', $box->id)->where('is_clear', 'not')->first();
                if (!$stock) {
                    return new ApiResource(422, 'Stock in Box is Empty !!!', '');
                }
                $needle_id = null;
                $master_needle_id = $stock->master_needle_id;
                $master_line_id = $line;
                $master_style_id = $style;
            }

            if ($reff == 'line') {
                return new ApiResource(422, 'Area Line cannot request !!!', '');
            }

            DB::beginTransaction();

            $id = Str::orderedUuid();
            Approval::insert([
                'id' => $id,
                'tanggal' => $now->today(),
                'user_id' => $user_id,
                'master_needle_id' => $master_needle_id,
                'master_line_id' => $master_line_id,
                'master_style_id' => $master_style_id,
                'needle_id' => $needle_id,
                'approval_id' => $approval,
                'master_area_id' => $area_id,
                'master_counter_id' => $lokasi_id,
                'needle_status' => $needle_status,
                'tipe' => $tipe,
                'remark' => $remark,
                'status' => 'WAITING',
                'filename' => $filename,
                'ext' => $ext,
                'created_by' => $username,
                'created_at' => $now,
            ]);
            HelperController::activityLog("ANDROID CREATE APPROVAL", 'approvals', 'create', $request->ip(), $request->userAgent(), json_encode([
                'id' => $id,
                'tanggal' => $now->today(),
                'user_id' => $user_id,
                'master_needle_id' => $master_needle_id,
                'master_line_id' => $master_line_id,
                'master_style_id' => $master_style_id,
                'needle_id' => $needle_id,
                'approval_id' => $approval,
                'master_area_id' => $area_id,
                'master_counter_id' => $lokasi_id,
                'needle_status' => $needle_status,
                'tipe' => $tipe,
                'remark' => $remark,
                'status' => 'WAITING',
                'filename' => $filename,
                'ext' => $ext,
                'created_by' => $username,
                'created_at' => $now,
            ]), null, $username);

            if (strlen($now->month) == 1) {
                $month = '0' . $now->month;
            } else {
                $month = $now->month;
            }

            if ($tipe == 'missing-fragment') {
                $t = 'Missing Fragment';
            } else if ($tipe == 'request-new') {
                $t = 'Request New Needle';
            }

            $title = 'New Approval';
            $message = "You have a new Outstanding Approval $t. \nWith data:\n Requester: {$name}\n Division: {$division}\n Position: {$position}\n Location: {$lokasi}\n DateTime: {$now}";
            $link = route('notif-clicked', ['tipe' => 'approval']);

            $data = [
                'title' => $title,
                'message' => $message,
                'link' => $link,
            ];

            $user = User::where('id', $approval)->first();
            $user->notify(new ApprovalNotification($data));

            HelperController::emitEvent('nemo', [
                'kategori' => 'username',
                'untuk' => $user->username,
                'event' => 'nemoNewNotification',
                'tipe' => 'notif',
                'title' => 'You Have ' . $title,
                'message' => $message,
                'link' => $link,
            ]);

            HelperController::emitEvent('nemo', [
                'event' => 'nemoReload',
                'tipe' => 'reload',
            ]);

            if ($tipe == 'missing-fragment') {
                $path = "assets/uploads/needle/$now->year/$month/$needle->id";
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                file_put_contents("$path/$id.$ext", $gambar);
            }

            DB::commit();
            return new ApiResource(200, 'Save Successfully', '');
        } catch (Exception $e) {
            DB::rollBack();
            // return new ApiResource(422, 'Save Failed', '');
            return new ApiResource(422, $e->getMessage(), '');
        }
    }

    public function stock(Request $request)
    {
        $username = $request->username;
        $area_id = $request->area_id;
        $lokasi_id = $request->lokasi_id;

        HelperController::activityLog('ANDROID STOCK', 'stocks', 'read', $request->ip(), $request->userAgent(), null, null, $username);

        $data = [];
        $s = Stock::join('master_needles as mn', 'mn.id', 'stocks.master_needle_id')
            ->join('master_boxes as mb', 'mb.id', 'stocks.master_box_id')
            ->selectRaw('mb.name as box, brand, mn.tipe, size, sum(`in`) as `in`, sum(`out`) as `out`')
            ->where('stocks.master_area_id', $area_id)
            ->where('stocks.master_counter_id', $lokasi_id)
            ->where('stocks.is_clear', 'not')
            ->groupBy('master_box_id')
            ->get();
        foreach ($s as $s) {
            $d = new stdClass;
            $d->boxName = $s->box;
            $d->brand = $s->brand;
            $d->tipe = $s->tipe;
            $d->size = $s->size;
            $d->qty = $s->in - $s->out;
            $data[] = $d;
        }

        return new ApiResource(200, 'success', $data);
    }
}
