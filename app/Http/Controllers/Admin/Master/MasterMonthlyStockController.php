<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\HistoryMasterMonthlyStock;
use App\Models\MasterMonthlyStock;
use App\Models\MasterNeedle;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MasterMonthlyStockController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'admin_master_monthly_stock';
        $title = 'ADMIN MASTER MONTHLY STOCK';

        HelperController::activityLog('OPEN ADMIN MASTER MONTHLY STOCK', 'master_monthly_stocks', 'read', $request->ip(), $request->userAgent());

        $master_needle = MasterNeedle::where('is_sample', 1)->get();

        $admin_master = 'menu-open';
        return view('Admin.Master.MonthlyStock.index', compact('title', 'page', 'master_needle', 'admin_master'));
    }

    public function data(Request $request)
    {
        $data = MasterMonthlyStock::with(['master_needle'])
            ->whereHas('master_needle', function ($q) {
                $q->where('is_sample', 1);
            })
            ->where('tahun', date('Y', strtotime($request->filter_month)))
            ->where('bulan', date('m', strtotime($request->filter_month)))
            ->get();
        return datatables()->of($data)
            ->addColumn('brand', function ($q) {
                return $q->master_needle->brand;
            })
            ->addColumn('tipe', function ($q) {
                return $q->master_needle->tipe;
            })
            ->addColumn('size', function ($q) {
                return $q->master_needle->size;
            })
            ->addColumn('code', function ($q) {
                return $q->master_needle->code;
            })
            ->addColumn('machine', function ($q) {
                return $q->master_needle->machine;
            })
            ->addColumn('action', function ($q) {
                return view('includes.admin.action', [
                    'edit' => route('admin.master.monthly-stock.edit', ['id' => $q->id]),
                    'hapus' => route('admin.master.monthly-stock.hapus', ['id' => $q->id]),
                ]);
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function crup(Request $request)
    {
        $id = $request->id;
        $tahun = date('Y', strtotime($request->month));
        $bulan = date('m', strtotime($request->month));
        $master_needle_id = $request->master_needle_id;
        $min_stock = $request->min_stock;
        $max_stock = $request->max_stock;
        $now = Carbon::now();

        try {
            DB::beginTransaction();

            if ($id == 0) {
                $s = MasterMonthlyStock::where('tahun', $tahun)->where('bulan', $bulan)->where('master_needle_id', $master_needle_id)->first();
                if ($s) {
                    return response()->json('This Needle already have Monthly Stock', 422);
                } else {
                    MasterMonthlyStock::create([
                        'master_needle_id' => $master_needle_id,
                        'tahun' => $tahun,
                        'bulan' => $bulan,
                        'min_stock' => $min_stock,
                        'max_stock' => $max_stock,
                        'created_by' => Auth::user()->username,
                        'created_at' => $now,
                    ]);
                    HelperController::activityLog("CREATE MONTHLY STOCK", 'master_monthly_stocks', 'create', $request->ip(), $request->userAgent(), json_encode([
                        'master_needle_id' => $master_needle_id,
                        'tahun' => $tahun,
                        'bulan' => $bulan,
                        'min_stock' => $min_stock,
                        'max_stock' => $max_stock,
                        'created_by' => Auth::user()->username,
                        'created_at' => $now,
                    ]));
                }
            } else {
                $c = 0;
                $s = MasterMonthlyStock::where('id', $id)->first();
                if ($s->tahun != $tahun && $s->bulan != $bulan && $s->master_needle_id != $master_needle_id) {
                    $u = MasterMonthlyStock::where('tahun', $tahun)->where('bulan', $bulan)->where('master_needle_id', $master_needle_id)->first();
                    if ($u) {
                        return response()->json('This Needle already have Monthly Stock', 422);
                    } else {
                        $c = 1;
                    }
                } else {
                    $c = 1;
                }

                if ($c == 1) {
                    HistoryMasterMonthlyStock::create([
                        'reff_id' => $id,
                        'master_needle_id' => $s->master_needle_id,
                        'tahun' => $s->tahun,
                        'bulan' => $s->bulan,
                        'min_stock' => $s->min_stock,
                        'max_stock' => $s->max_stock,
                        'created_by' => Auth::user()->username,
                        'created_at' => $now,
                    ]);
                    MasterMonthlyStock::where('id', $id)->update([
                        'master_needle_id' => $master_needle_id,
                        'tahun' => $tahun,
                        'bulan' => $bulan,
                        'min_stock' => $min_stock,
                        'max_stock' => $max_stock,
                        'updated_by' => Auth::user()->username,
                        'updated_at' => $now,
                    ]);
                    HelperController::activityLog("UPDATE MONTHLY STOCK", 'master_monthly_stocks', 'update', $request->ip(), $request->userAgent(), json_encode([
                        'id' => $id,
                        'master_needle_id' => $master_needle_id,
                        'tahun' => $tahun,
                        'bulan' => $bulan,
                        'min_stock' => $min_stock,
                        'max_stock' => $max_stock,
                        'updated_by' => Auth::user()->username,
                        'updated_at' => $now,
                    ]), $id);
                }
            }

            DB::commit();
            return response()->json('Save Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json('Save Failed', 422);
        }
    }

    public function edit($id)
    {
        $s = MasterMonthlyStock::find($id);
        return response()->json($s, 200);
    }

    public function hapus(Request $request, $id)
    {
        $now = Carbon::now();
        try {
            DB::beginTransaction();
            MasterMonthlyStock::where('id', $id)->update([
                'deleted_by' => Auth::user()->username,
                'deleted_at' => $now,
            ]);
            HelperController::activityLog("DELETE MASTER MONTHLY STOCK", 'master_monthly_stocks', 'delete', $request->ip(), $request->userAgent(), null, $id);
            DB::commit();
            return response()->json('Delete Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json('Delete Failed', 422);
        }
    }
}
