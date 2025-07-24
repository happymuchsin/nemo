<?php

namespace App\Http\Controllers\User\Approval;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\Adjustment;
use App\Models\ApprovalAdjustment;
use App\Models\HistoryAddStock;
use App\Models\HistoryEditStock;
use App\Models\HistoryOutStock;
use App\Models\MasterApproval;
use App\Models\Stock;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class ApprovalAdjustmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'user_approval_adjustment';
        $title = 'USER APPROVAL ADJUSTMENT';

        HelperController::activityLog('OPEN USER APPROVAL ADJUSTMENT', 'approval_adjustments', 'read', $request->ip(), $request->userAgent());

        if (Config::get('app.env') == 'local') {
            $master_approval = MasterApproval::with(['user'])->get();
        } else {
            $master_approval = MasterApproval::with(['user'])->where('user_id', '!=', '1')->get();
        }

        return view('User.Approval.Adjustment.index', compact('title', 'page', 'master_approval'));
    }

    public function data(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $filter_status = $request->filter_status;

        $master_approval = MasterApproval::where('user_id', Auth::user()->id)->first();
        if ($master_approval) {
            $master_approval_id = $master_approval->id;
        } else {
            $master_approval_id = null;
        }

        $data = ApprovalAdjustment::with(['user', 'adjustment'])
            ->when(Auth::user()->username != 'developer', function ($q) use ($master_approval_id) {
                $q->where('master_approval_id', $master_approval_id);
            })
            ->whereYear('tanggal', $tahun)
            ->when($bulan != 'all', function ($q) use ($bulan) {
                $q->whereMonth('tanggal', $bulan);
            })
            ->where('status', $filter_status)
            ->orderBy('created_at', 'desc')
            ->get();
        return datatables()->of($data)
            ->addColumn('period', function ($q) {
                return '<a href="#" class="text-center" title="Detail" onclick="detail(\'' . route('user.approval.adjustment.get', ['id' => $q->reff_id]) . '\')">' . $q->adjustment->tahun . ' - ' . $q->adjustment->bulan . '</a>';
            })
            ->addColumn('before', function ($q) {
                return $q->adjustment->before;
            })
            ->addColumn('after', function ($q) {
                return $q->adjustment->after;
            })
            ->addColumn('remark', function ($q) {
                return $q->adjustment->remark;
            })
            ->addColumn('requestor', function ($q) {
                return $q->user->username . ' - ' . $q->user->name;
            })
            ->rawColumns(['period'])
            ->make(true);
    }

    public function get($id)
    {
        $adj = Adjustment::where('id', $id)->first();
        $period = $adj->tahun . '-' . $adj->bulan;
        $remark = $adj->remark;

        $approval = ApprovalAdjustment::where('reff_id', $id)->first();

        return response()->json([
            'id_adjustment' => $id,
            'id_approval' => $approval->id,
            'period' => $period,
            'remark' => $remark,
            'approval' => $approval->master_approval_id,
            'status' => $approval->status,
        ], 200);
    }

    public function approval(Request $request)
    {
        try {
            $id_adjustment = $request->id_adjustment;
            $id_approval = $request->id_approval;
            $status = strtolower($request->status);

            DB::beginTransaction();

            $now = Carbon::now();
            ApprovalAdjustment::where('id', $id_approval)->update([
                'status' => strtoupper($status),
                $status => $now,
                'updated_by' => Auth::user()->username,
                'updated_at' => $now,
            ]);

            HelperController::activityLog("UPDATE APPROVAL ADJUSTMENT", 'approval_adjustments', 'update', $request->ip(), $request->userAgent(), json_encode([
                'id' => $id_approval,
                'status' => strtoupper($status),
                $status => $now,
                'updated_by' => Auth::user()->username,
                'updated_at' => $now,
            ]), $id_approval);

            if ($status == 'approve') {
                $adjustment = Adjustment::with(['detail_adjustment'])->where('id', $id_adjustment)->first();

                foreach ($adjustment->detail_adjustment as $da) {
                    $stock = Stock::where('master_area_id', $da->master_area_id)
                        ->where('master_counter_id', $da->master_counter_id)
                        ->where('master_box_id', $da->master_box_id)
                        ->where('master_needle_id', $da->master_needle_id)
                        ->whereNull('status')
                        ->get();
                    foreach ($stock as $s) {
                        HistoryAddStock::where('stock_id', $s->id)->update([
                            'deleted_by' => Auth::user()->username,
                            'deleted_at' => $now,
                        ]);
                        HistoryEditStock::where('stock_id', $s->id)->update([
                            'deleted_by' => Auth::user()->username,
                            'deleted_at' => $now,
                        ]);
                        HistoryOutStock::where('stock_id', $s->id)->update([
                            'deleted_by' => Auth::user()->username,
                            'deleted_at' => $now,
                        ]);
                        $s->status = 'adjustment';
                        $s->updated_by = Auth::user()->username;
                        $s->updated_at = $now;
                        $s->save();
                    }
                    Stock::create([
                        'master_area_id' => $da->master_area_id,
                        'master_counter_id' => $da->master_counter_id,
                        'master_box_id' => $da->master_box_id,
                        'master_needle_id' => $da->master_needle_id,
                        'in' => $da->after,
                        'out' => 0,
                        'is_clear' => 'not',
                        'created_by' => Auth::user()->username,
                        'created_at' => $now,
                    ]);
                    HelperController::activityLog("CREATE STOCK", 'stocks', 'create', $request->ip(), $request->userAgent(), json_encode([
                        'master_area_id' => $da->master_area_id,
                        'master_counter_id' => $da->master_counter_id,
                        'master_box_id' => $da->master_box_id,
                        'master_needle_id' => $da->master_needle_id,
                        'in' => $da->after,
                        'out' => 0,
                        'is_clear' => 'not',
                        'created_by' => Auth::user()->username,
                        'created_at' => $now,
                    ]));
                }
            }

            if ($status == 'approve') {
                $xstatus = 'APPROVED';
            } else if ($status == 'reject') {
                $xstatus = 'REJECTED';
            }
            $adjustment->status = $xstatus;
            $adjustment->updated_by = Auth::user()->username;
            $adjustment->updated_at = $now;
            $adjustment->save();

            HelperController::reload();

            DB::commit();
            return response()->json(ucwords($status) . ' Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(ucwords($status) . ' Failed', 422);
        }
    }
}
