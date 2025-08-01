<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\HistoryMasterMorningStock;
use App\Models\MasterMorningStock;
use App\Models\MasterNeedle;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;

class MasterMorningStockController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'admin_master_morning_stock';
        $title = 'ADMIN MASTER MORNING STOCK';

        HelperController::activityLog('OPEN ADMIN MASTER MORNING STOCK', 'master_morning_stocks', 'read', $request->ip(), $request->userAgent());

        $master_needle = MasterNeedle::where('is_sample', 1)->get();

        $admin_master = 'menu-open';
        return view('Admin.Master.MorningStock.index', compact('title', 'page', 'master_needle', 'admin_master'));
    }

    public function data(Request $request)
    {
        $data = MasterNeedle::where('is_sample', 1)->get();
        return datatables()->of($data)
            ->addColumn('value', function ($q) {
                $master_morning_stock = MasterMorningStock::where('master_needle_id', $q->id)->first();
                if ($master_morning_stock) {
                    return $master_morning_stock->value;
                } else {
                    return '';
                }
            })
            ->addColumn('action', function ($q) {
                return view('includes.admin.action', [
                    'edit' => route('admin.master.morning-stock.edit', ['id' => $q->id]),
                    'hapus' => route('admin.master.morning-stock.hapus', ['id' => $q->id]),
                ]);
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function crup(Request $request)
    {
        $master_needle_id = $request->id;
        $value = $request->value;
        $now = Carbon::now();

        try {
            DB::beginTransaction();

            $s = MasterMorningStock::where('master_needle_id', $master_needle_id)->first();
            if ($s) {
                HistoryMasterMorningStock::create([
                    'reff_id' => $s->id,
                    'master_needle_id' => $s->master_needle_id,
                    'value' => $s->value,
                    'created_by' => Auth::user()->username,
                    'created_at' => $now,
                ]);
                MasterMorningStock::where('id', $s->id)->update([
                    'master_needle_id' => $master_needle_id,
                    'value' => $value,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => $now,
                ]);
                HelperController::activityLog("UPDATE MORNING STOCK", 'master_morning_stocks', 'update', $request->ip(), $request->userAgent(), json_encode([
                    'id' => $s->id,
                    'master_needle_id' => $master_needle_id,
                    'value' => $value,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => $now,
                ]), $s->id);
            } else {
                MasterMorningStock::create([
                    'master_needle_id' => $master_needle_id,
                    'value' => $value,
                    'created_by' => Auth::user()->username,
                    'created_at' => $now,
                ]);
                HelperController::activityLog("CREATE MORNING STOCK", 'master_morning_stocks', 'create', $request->ip(), $request->userAgent(), json_encode([
                    'master_needle_id' => $master_needle_id,
                    'value' => $value,
                    'created_by' => Auth::user()->username,
                    'created_at' => $now,
                ]));
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
        $s = MasterMorningStock::where('master_needle_id', $id)->first();
        $d = new stdClass;
        $d->id = $id;
        $d->value = $s->value ?? '';
        return response()->json($d, 200);
    }

    public function hapus(Request $request, $id)
    {
        $now = Carbon::now();
        try {
            DB::beginTransaction();
            MasterMorningStock::where('master_needle_id', $id)->update([
                'deleted_by' => Auth::user()->username,
                'deleted_at' => $now,
            ]);
            HelperController::activityLog("DELETE MASTER MORNING STOCK", 'master_morning_stocks', 'delete', $request->ip(), $request->userAgent(), null, $id);
            DB::commit();
            return response()->json('Delete Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json('Delete Failed', 422);
        }
    }
}
