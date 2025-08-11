<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\Adjustment;
use App\Models\ApprovalAdjustment;
use App\Models\DetailAdjustment;
use App\Models\MasterApproval;
use App\Models\Stock;
use App\Models\User;
use App\Notifications\ApprovalNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use stdClass;

class AdjustmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'user_adjustment';
        $title = 'USER ADJUSTMENT';

        HelperController::activityLog('OPEN USER ADJUSTMENT', 'adjustments', 'read', $request->ip(), $request->userAgent());

        if (Config::get('app.env') == 'local') {
            $master_approval = MasterApproval::with(['user'])->get();
        } else {
            $master_approval = MasterApproval::with(['user'])->where('user_id', '!=', '1')->get();
        }

        return view('User.Adjustment.index', compact('title', 'page', 'master_approval'));
    }

    public function data(Request $request)
    {
        $tahun = $request->tahun;
        $data = [];
        $adjustment = Adjustment::when($tahun != 'all', function ($q) use ($tahun) {
            $q->where('tahun', $tahun);
        })
            ->get();
        foreach ($adjustment as $a) {
            $d = new stdClass;
            $d->period = '<a href="#" class="text-center" title="Detail" onclick="detail(\'' . route('user.adjustment.get', ['id' => $a->id]) . '\')">' . $a->tahun . ' - ' . $a->bulan . '</a>';
            $d->before = $a->before;
            $d->after = $a->after;
            $d->remark = $a->remark;
            $d->status = $a->status;
            if ($a->status == 'WAITING') {
                $onclick1 = 'onclick="edit(\'' . route('user.adjustment.get', ['id' => $a->id]) . '\')"';
                $color1 = 'text-info';
                $onclick2 = 'onclick="hapus(\'' . route('user.adjustment.hapus', ['id' => $a->id]) . '\')"';
                $color2 = 'text-danger';
            } else {
                $onclick1 = '';
                $color1 = 'text-secondary';
                $onclick2 = '';
                $color2 = 'text-secondary';
            }
            $h = '';
            $h .= '<a href="#" class="text-center" title="Recalculate" onclick="recalculate(\'' . route('user.adjustment.recalculate', ['id' => $a->id]) . '\')"><i class="fa fa-sync-alt text-success mr-3"></i></a>';
            $h .= '<a href="#" class="text-center" title="Edit" ' . $onclick1 . '><i class="fa fa-edit ' . $color1 . ' mr-3"></i></a>';
            $h .= '<a href="#" class="text-center" title="Delete" ' . $onclick2 . '><i class="fa fa-trash-alt ' . $color2 . ' mr-3"></i></a>';
            $d->action = $h;
            $data[] = $d;
        }

        return datatables()->of($data)
            ->rawColumns(['period', 'action'])
            ->make(true);
    }

    public function item(Request $request)
    {
        $id = $request->id;
        $tipe = $request->tipe;
        $period = $request->period;
        if ($period == '') {
            $data = [];
            return datatables()->of($data)
                ->make(true);
        }
        $tahun = substr($period, 0, 4);
        $bulan = substr($period, 5, 2);
        $sebelumnya = Carbon::parse($tahun . '-' . $bulan . '-01');
        if ($sebelumnya < '2025-09-01') {
            $previous = true;
        } else {
            $previous = false;
        }
        $adjustment = Adjustment::where('id', $id)->first();
        if ($adjustment) {
            $status = $adjustment->status;
        } else {
            $status = 'WAITING';
        }
        $detail_adjustment = DetailAdjustment::where('adjustment_id', $id)->get();
        $collect_detail_adjustment = collect($detail_adjustment);
        $data = [];
        $stock = Stock::with(['area', 'counter', 'box', 'needle'])
            ->selectRaw('sum(`in`) as `in`, sum(`out`) as `out`, master_area_id, master_counter_id, master_box_id, master_needle_id, id')
            ->when($previous, function ($q) use ($sebelumnya) {
                $q->whereDate('created_at', '<', '2025-09-01');
            })
            ->when(!$previous, function ($q) use ($tahun, $bulan) {
                $q->whereYear('created_at', $tahun)
                    ->whereMonth('created_at', $bulan);
            })
            ->when($status == 'APPROVED', function ($q) {
                $q->where('status', 'adjustment');
            })
            ->when($status == 'REJECTED' || $status == 'WAITING', function ($q) {
                $q->whereNull('status');
            })
            ->where('is_clear', 'not')
            ->whereHas('needle', fn($q) => $q->where('is_sample', '1'))
            ->groupBy('master_area_id', 'master_counter_id', 'master_box_id', 'master_needle_id')
            ->get();
        foreach ($stock as $k => $s) {
            $qty = $s->in - $s->out;
            $d = new stdClass;
            $d->area = $s->area->name;
            $d->counter = $s->counter->name;
            $d->box = $s->box->name;
            $d->brand = $s->needle->brand;
            $d->type = $s->needle->tipe;
            $d->size = $s->needle->size;
            $d->code = $s->needle->code;
            $d->machine = $s->needle->machine;

            $da = $collect_detail_adjustment->where('master_area_id', $s->area->id)
                ->where('master_counter_id', $s->counter->id)
                ->where('master_box_id', $s->box->id)
                ->where('master_needle_id', $s->needle->id);
            if ($tipe == 'add' || $tipe == 'edit') {
                $after = '';
                $balance = '';
                if ($tipe == 'edit') {
                    $after = $da->value('after') ?? 0;
                    $balance = $qty - $after;
                }
                $d->system = '<input readonly style="min-width: 85px;" type="number" name="system[]" id="system' . $s->id . '" class="form-control p-2 no-spin" value="' . $qty . '" /><input type="hidden" id="stockX' . $s->id . '" value="' . $s->id . '" />';
                $d->actual = '<input style="min-width: 85px;" type="number" name="actual[]" id="actual' . $s->id . '" class="form-control p-2 no-spin input-actual" value="' . $after . '" />';
                $d->balance = '<input readonly style="min-width: 85px;" type="number" name="balance[]" id="balance' . $s->id . '" class="form-control p-2 no-spin" value="' . $balance . '" />';
                $d->remark = '<input style="min-width: 100px;text-transform:uppercase;" type="text" name="remark[]" id="remark' . $s->id . '" class="form-control p-2" autocomplete="off" value="' . $da->value('remark') . '" />';
            } else if ($tipe == 'detail') {
                $d->system = $qty;
                $d->actual = $da->value('after') ?? 0;
                $d->balance = $qty - $da->value('after');
                $d->remark = $da->value('remark');
            }
            $data[] = $d;
        }

        return datatables()->of($data)
            ->rawColumns(['system', 'actual', 'balance', 'remark'])
            ->make(true);
    }

    public function crup(Request $request)
    {
        $id = $request->id;
        $period = $request->period;
        $approval = $request->approval;
        $remark = strtoupper($request->remark);
        $detailAdj = $request->detailAdj;
        $now = Carbon::now();

        $tahun = substr($period, 0, 4);
        $bulan = substr($period, 5, 2);

        try {
            DB::beginTransaction();

            if ($id == 0) {
                $mode = 'add';
                $a = Adjustment::where('tahun', $tahun)->where('bulan', $bulan)->where('status', '!=', 'REJECTED')->first();
                if ($a) {
                    return response()->json('Already have Adjustment', 422);
                }
                $i = Adjustment::create([
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                    'before' => '0',
                    'after' => '0',
                    'remark' => $remark,
                    'status' => 'WAITING',
                    'created_by' => Auth::user()->username,
                    'created_at' => $now,
                ]);
                HelperController::activityLog("CREATE ADJUSTMENT", 'adjustments', 'create', $request->ip(), $request->userAgent(), json_encode([
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                    'before' => '0',
                    'after' => '0',
                    'remark' => $remark,
                    'status' => 'WAITING',
                    'created_by' => Auth::user()->username,
                    'created_at' => $now,
                ]));

                $id = $i->id;
            } else {
                $mode = 'edit';
                Adjustment::where('id', $id)->update([
                    'remark' => $remark,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => $now,
                ]);
                HelperController::activityLog("UPDATE ADJUSTMENT", 'adjustments', 'update', $request->ip(), $request->userAgent(), json_encode([
                    'remark' => $remark,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => $now,
                ]));
            }

            $da = DetailAdjustment::where('adjustment_id', $id)->get();
            $cda = collect($da);

            $before = 0;
            $after = 0;
            foreach ($detailAdj as $da) {
                $before += $da['before'];
                $after += $da['after'];
                $s = Stock::where('id', $da['stock_id'])->first();
                $x = $cda->where('master_area_id', $s->master_area_id)
                    ->where('master_counter_id', $s->master_counter_id)
                    ->where('master_box_id', $s->master_box_id)
                    ->where('master_needle_id', $s->master_needle_id);
                if ($x->value('id')) {
                    DetailAdjustment::where('id', $x->value('id'))->update([
                        'after' => $da['after'],
                        'remark' => strtoupper($da['remark']),
                        'updated_by' => Auth::user()->username,
                        'updated_at' => $now,
                    ]);
                    HelperController::activityLog("UPDATE DETAIL ADJUSTMENT", 'detail_adjustments', 'update', $request->ip(), $request->userAgent(), json_encode([
                        'id' => $x->value('id'),
                        'adjustment_id' => $id,
                        'before' => $da['before'],
                        'after' => $da['after'],
                        'remark' => strtoupper($da['remark']),
                        'updated_by' => Auth::user()->username,
                        'updated_at' => $now,
                    ]), $x->value('id'));
                } else {
                    DetailAdjustment::create([
                        'adjustment_id' => $id,
                        'master_area_id' => $s->master_area_id,
                        'master_counter_id' => $s->master_counter_id,
                        'master_box_id' => $s->master_box_id,
                        'master_needle_id' => $s->master_needle_id,
                        'before' => $da['before'],
                        'after' => $da['after'],
                        'remark' => strtoupper($da['remark']),
                        'created_by' => Auth::user()->username,
                        'created_at' => $now,
                    ]);
                    HelperController::activityLog("CREATE DETAIL ADJUSTMENT", 'detail_adjustments', 'create', $request->ip(), $request->userAgent(), json_encode([
                        'adjustment_id' => $id,
                        'master_area_id' => $s->master_area_id,
                        'master_counter_id' => $s->master_counter_id,
                        'master_box_id' => $s->master_box_id,
                        'master_needle_id' => $s->master_needle_id,
                        'before' => $da['before'],
                        'after' => $da['after'],
                        'remark' => strtoupper($da['remark']),
                        'created_by' => Auth::user()->username,
                        'created_at' => $now,
                    ]));
                }
            }

            Adjustment::where('id', $id)->update([
                'before' => $before,
                'after' => $after,
                'updated_by' => Auth::user()->username,
                'updated_at' => $now,
            ]);

            if ($mode == 'add') {
                self::createApproval($now, $approval, $id, $request->ip(), $request->userAgent());
            } else {
                self::resetApproval($now, $id, $request->ip(), $request->userAgent());
            }

            HelperController::reload();

            DB::commit();
            return response()->json('Saved Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 422);
        }
    }

    public function get($id)
    {
        $adj = Adjustment::where('id', $id)->first();
        $period = $adj->tahun . '-' . $adj->bulan;
        $remark = $adj->remark;

        $approval = ApprovalAdjustment::where('reff_id', $id)->first();

        return response()->json([
            'id' => $id,
            'period' => $period,
            'remark' => $remark,
            'approval' => $approval->master_approval_id,
        ], 200);
    }

    public function hapus(Request $request)
    {
        $id = $request->id;
        $now = Carbon::now();
        try {
            DB::beginTransaction();
            DetailAdjustment::where('adjustment_id', $id)
                ->update([
                    'deleted_by' => Auth::user()->username,
                    'deleted_at' => $now,
                ]);
            HelperController::activityLog("DELETE DETAIL ADJUSTMENT", 'detail_adjustments', 'delete', $request->ip(), $request->userAgent(), json_encode([
                'adjustment_id' => $id,
                'deleted_by' => Auth::user()->username,
                'deleted_at' => $now,
            ]), $id);
            Adjustment::where('id', $id)
                ->update([
                    'deleted_by' => Auth::user()->username,
                    'deleted_at' => $now,
                ]);
            HelperController::activityLog("DELETE ADJUSTMENT", 'adjustments', 'delete', $request->ip(), $request->userAgent(), json_encode([
                'id' => $id,
                'deleted_by' => Auth::user()->username,
                'deleted_at' => $now,
            ]), $id);
            ApprovalAdjustment::where('reff_id', $id)
                ->update([
                    'deleted_by' => Auth::user()->username,
                    'deleted_at' => $now,
                ]);
            HelperController::activityLog("DELETE APPROVAL ADJUSTMENT", 'approval_adjustments', 'delete', $request->ip(), $request->userAgent(), json_encode([
                'reff_id' => $id,
                'deleted_by' => Auth::user()->username,
                'deleted_at' => $now,
            ]), $id);
            HelperController::reload();
            DB::commit();
            return response()->json(__('global.delete-success'), 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(__('global.delete-failed'), 422);
        }
    }

    public function recalculate(Request $request)
    {
        $id = $request->id;
        $adj = Adjustment::where('id', $id)->first();

        $before = 0;
        $after = 0;
        foreach ($adj->detail_adjustment as $da) {
            $before += $da->before;
            $after += $da->after;
        }

        return response()->json('Recalculate Successfully', 200);
    }

    static function createApproval($now, $approval, $id, $ip, $userAgent)
    {
        ApprovalAdjustment::create([
            'tanggal' => $now->today(),
            'user_id' => Auth::user()->id,
            'master_approval_id' => $approval,
            'tipe' => 'adjustment',
            'reff_id' => $id,
            'status' => 'WAITING',
            'created_by' => Auth::user()->username,
            'created_at' => $now,
        ]);
        HelperController::activityLog("CREATE APPROVAL ADJUSTMENT", 'approval_adjustments', 'create', $ip, $userAgent, json_encode([
            'tanggal' => $now->today(),
            'user_id' => Auth::user()->id,
            'master_approval_id' => $approval,
            'tipe' => 'adjustment',
            'reff_id' => $id,
            'status' => 'WAITING',
            'created_by' => Auth::user()->username,
            'created_at' => $now,
        ]));

        $t = 'Adjustment';

        $user = Auth::user();

        $title = 'New Approval';
        $message = "You have a new Outstanding Approval $t. \nWith data:\n Requester: {$user->name}\n Division: {$user->division->name}\n Position: {$user->position->name}\n DateTime: {$now}";
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
    }

    static function resetApproval($now, $id, $ip, $userAgent)
    {
        ApprovalAdjustment::where('id', $id)->update([
            'status' => 'WAITING',
            'updated_by' => Auth::user()->username,
            'updated_at' => $now,
        ]);

        HelperController::activityLog("UPDATE APPROVAL ADJUSTMENT", 'approval_adjustments', 'update', $ip, $userAgent, json_encode([
            'id' => $id,
            'status' => 'WAITING',
            'updated_by' => Auth::user()->username,
            'updated_at' => $now,
        ]), $id);
    }
}
