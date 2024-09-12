<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\MasterNeedle;
use App\Models\Needle;
use App\Models\Stock;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        try {
            DB::beginTransaction();

            if ($id == 0) {
                $s = MasterNeedle::where('brand', $brand)->where('tipe', $tipe)->where('size', $size)->where('code', $code)->where('machine', $machine)->first();
                if ($s) {
                    return response()->json('Brand Tipe Size Code Machine already used', 422);
                } else {
                    MasterNeedle::create([
                        'brand' => $brand,
                        'tipe' => $tipe,
                        'size' => $size,
                        'code' => $code,
                        'machine' => $machine,
                        'min_stock' => $min_stock,
                        'created_by' => Auth::user()->username,
                        'created_at' => Carbon::now(),
                    ]);
                    HelperController::activityLog("CREATE MASTER NEEDLE", 'master_needles', 'create', $request->ip(), $request->userAgent(), json_encode([
                        'brand' => $brand,
                        'tipe' => $tipe,
                        'size' => $size,
                        'code' => $code,
                        'machine' => $machine,
                        'min_stock' => $min_stock,
                        'created_by' => Auth::user()->username,
                        'created_at' => Carbon::now(),
                    ]));
                }
            } else {
                $c = 0;
                $s = MasterNeedle::where('id', $id)->first();
                if ($s->brand != $brand && $s->tipe != $tipe && $s->size != $size && $s->code != $code && $s->machine != $machine) {
                    $u =
                        MasterNeedle::where('brand', $brand)->where('tipe', $tipe)->where('size', $size)->where('code', $code)->where('machine', $machine)->first();
                    if ($u) {
                        return response()->json('Brand Tipe Size Code Machine already used', 422);
                    } else {
                        $c = 1;
                    }
                } else {
                    $c = 1;
                }

                if ($c == 1) {
                    MasterNeedle::where('id', $id)->update([
                        'brand' => $brand,
                        'tipe' => $tipe,
                        'size' => $size,
                        'code' => $code,
                        'machine' => $machine,
                        'min_stock' => $min_stock,
                        'updated_by' => Auth::user()->username,
                        'updated_at' => Carbon::now(),
                    ]);
                    HelperController::activityLog("UPDATE MASTER NEEDLE", 'master_needles', 'update', $request->ip(), $request->userAgent(), json_encode([
                        'id' => $id,
                        'brand' => $brand,
                        'tipe' => $tipe,
                        'size' => $size,
                        'code' => $code,
                        'machine' => $machine,
                        'min_stock' => $min_stock,
                        'updated_by' => Auth::user()->username,
                        'updated_at' => Carbon::now(),
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
        $s = MasterNeedle::find($id);
        return response()->json($s, 200);
    }

    public function hapus(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            Stock::where('master_needle_id', $id)->update([
                'deleted_by' => Auth::user()->username,
                'deleted_at' => Carbon::now(),
            ]);
            Needle::where('master_needle_id', $id)->update([
                'deleted_by' => Auth::user()->username,
                'deleted_at' => Carbon::now(),
            ]);
            MasterNeedle::where('id', $id)->update([
                'deleted_by' => Auth::user()->username,
                'deleted_at' => Carbon::now(),
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
