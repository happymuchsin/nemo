<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\DeadStock;
use App\Models\MasterArea;
use App\Models\MasterNeedle;
use App\Models\Needle;
use App\Models\Stock;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;

class MasterNeedleController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'admin_master_needle';
        $title = 'ADMIN MASTER NEEDLE';

        HelperController::activityLog('OPEN ADMIN MASTER NEEDLE', 'master_needles', 'read', $request->ip(), $request->userAgent());

        $admin_master = 'menu-open';

        return view('Admin.Master.Needle.index', compact('title', 'page', 'admin_master'));
    }

    public function data(Request $request)
    {
        $data = MasterNeedle::get();
        return datatables()->of($data)
            ->editColumn('is_sample', function ($q) {
                return $q->is_sample == '1' ? 'Yes' : 'No';
            })
            ->addColumn('action', function ($q) {
                return view('includes.admin.action', [
                    'edit' => route('admin.master.needle.edit', ['id' => $q->id]),
                    'hapus' => route('admin.master.needle.hapus', ['id' => $q->id]),
                ]);
            })
            ->make(true);
    }

    public function crup(Request $request)
    {
        $id = $request->id;
        $brand = strtoupper($request->brand);
        $tipe = strtoupper($request->tipe);
        $size = strtoupper($request->size);
        $code = strtoupper($request->code);
        $machine = strtoupper($request->machine);
        $min_stock = $request->min_stock;
        $max_stock = $request->max_stock;
        $is_sample = $request->is_sample;
        $now = Carbon::now();

        try {
            DB::beginTransaction();

            if ($id == 0) {
                $s = MasterNeedle::where('brand', $brand)->where('tipe', $tipe)->where('size', $size)->where('code', $code)->where('machine', $machine)->first();
                if ($s) {
                    return response()->json('Brand Tipe Size Code Machine already used', 422);
                } else {
                    $x = [];
                    $x['brand'] = $brand;
                    $x['tipe'] = $tipe;
                    $x['size'] = $size;
                    $x['code'] = $code;
                    $x['machine'] = $machine;
                    $x['min_stock'] = $min_stock;
                    $x['max_stock'] = $max_stock;
                    $x['is_sample'] = $is_sample;
                    $x['created_by'] = Auth::user()->username;
                    $x['created_at'] = $now;
                    $mn = MasterNeedle::create($x);
                    HelperController::activityLog("CREATE MASTER NEEDLE", 'master_needles', 'create', $request->ip(), $request->userAgent(), json_encode($x));
                    $master_area = MasterArea::get();
                    foreach ($master_area as $ma) {
                        $x = [];
                        $x['master_area_id'] = $ma->id;
                        $x['master_needle_id'] = $mn->id;
                        $x['in'] = 0;
                        $x['out'] = 0;
                        $x['created_by'] = Auth::user()->username;
                        $x['created_at'] = $now;
                        DeadStock::create($x);
                        HelperController::activityLog("CREATE DEAD STOCK", 'dead_stock', 'create', $request->ip(), $request->userAgent(), json_encode($x));
                    }
                }
            } else {
                $c = 0;
                $s = MasterNeedle::where('id', $id)->first();
                if ($s->brand != $brand && $s->tipe != $tipe && $s->size != $size && $s->code != $code && $s->machine != $machine) {
                    $u = MasterNeedle::where('brand', $brand)->where('tipe', $tipe)->where('size', $size)->where('code', $code)->where('machine', $machine)->first();
                    if ($u) {
                        return response()->json('Brand Tipe Size Code Machine already used', 422);
                    } else {
                        $c = 1;
                    }
                } else {
                    $c = 1;
                }

                if ($c == 1) {
                    $x = [];
                    $x['brand'] = $brand;
                    $x['tipe'] = $tipe;
                    $x['size'] = $size;
                    $x['code'] = $code;
                    $x['machine'] = $machine;
                    $x['min_stock'] = $min_stock;
                    $x['max_stock'] = $max_stock;
                    $x['is_sample'] = $is_sample;
                    $x['updated_by'] = Auth::user()->username;
                    $x['updated_at'] = $now;
                    MasterNeedle::where('id', $id)->update($x);
                    HelperController::activityLog("UPDATE MASTER NEEDLE", 'master_needles', 'update', $request->ip(), $request->userAgent(), json_encode($x), $id);
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
        $s = MasterNeedle::find($id);
        $d = new stdClass;
        $d->id = $s->id;
        $d->brand = $s->brand;
        $d->tipe = $s->tipe;
        $d->size = $s->size;
        $d->code = $s->code;
        $d->machine = $s->machine;
        $d->min_stock = $s->min_stock;
        $d->max_stock = $s->max_stock;
        $d->is_sample = $s->is_sample == '1' ? true : false;
        return response()->json($d, 200);
    }

    public function hapus(Request $request, $id)
    {
        $now = Carbon::now();
        try {
            DB::beginTransaction();
            Stock::where('master_needle_id', $id)
                ->whereNull('status')
                ->update([
                    'deleted_by' => Auth::user()->username,
                    'deleted_at' => $now,
                ]);
            DeadStock::where('master_needle_id', $id)->update([
                'deleted_by' => Auth::user()->username,
                'deleted_at' => $now,
            ]);
            Needle::where('master_needle_id', $id)->update([
                'deleted_by' => Auth::user()->username,
                'deleted_at' => $now,
            ]);
            MasterNeedle::where('id', $id)->update([
                'deleted_by' => Auth::user()->username,
                'deleted_at' => $now,
            ]);
            HelperController::activityLog("DELETE MASTER NEEDLE", 'master_needles', 'delete', $request->ip(), $request->userAgent(), null, $id);
            DB::commit();
            return response()->json('Delete Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json('Delete Failed', 422);
        }
    }
}
