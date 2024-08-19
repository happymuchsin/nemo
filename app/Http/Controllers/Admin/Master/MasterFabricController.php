<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\MasterFabric;
use App\Models\MasterStyle;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MasterFabricController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'admin_master_fabric';
        $title = 'ADMIN MASTER FABRIC';

        HelperController::activityLog('OPEN ADMIN MASTER FABRIC', 'master_fabrics', 'read', $request->ip(), $request->userAgent());

        $admin_master = 'menu-open';

        return view('Admin.Master.Fabric.index', compact('title', 'page', 'admin_master'));
    }

    public function data(Request $request)
    {
        $data = MasterFabric::get();
        return datatables()->of($data)
            ->addColumn('action', function ($q) {
                return view('includes.admin.action', [
                    'edit' => route('admin.master.fabric.edit', ['id' => $q->id]),
                    'hapus' => route('admin.master.fabric.hapus', ['id' => $q->id]),
                ]);
            })
            ->make(true);
    }

    public function crup(Request $request)
    {
        $id = $request->id;
        $name = strtoupper($request->name);

        try {
            DB::beginTransaction();

            if ($id == 0) {
                $s = MasterFabric::where('name', $name)->first();
                if ($s) {
                    return response()->json('Fabric already used', 422);
                } else {
                    MasterFabric::create([
                        'name' => $name,
                        'created_by' => Auth::user()->username,
                        'created_at' => Carbon::now(),
                    ]);
                    HelperController::activityLog("CREATE MASTER FABRIC", 'master_fabrics', 'create', $request->ip(), $request->userAgent(), json_encode([
                        'name' => $name,
                        'created_by' => Auth::user()->username,
                        'created_at' => Carbon::now(),
                    ]));
                }
            } else {
                $c = 0;
                $s = MasterFabric::where('id', $id)->first();
                if ($s->name != $name) {
                    $u = MasterFabric::where('name', $name)->first();
                    if ($u) {
                        return response()->json('Fabric already used', 422);
                    } else {
                        $c = 1;
                    }
                } else {
                    $c = 1;
                }

                if ($c == 1) {
                    MasterFabric::where('id', $id)->update([
                        'name' => $name,
                        'updated_by' => Auth::user()->username,
                        'updated_at' => Carbon::now(),
                    ]);
                    HelperController::activityLog("UPDATE MASTER FABRIC", 'master_fabrics', 'update', $request->ip(), $request->userAgent(), json_encode([
                        'id' => $id,
                        'name' => $name,
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
        $s = MasterFabric::find($id);
        return response()->json($s, 200);
    }

    public function hapus(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            MasterStyle::where('master_fabric_id', $id)->update([
                'deleted_by' => Auth::user()->username,
                'deleted_at' => Carbon::now(),
            ]);
            MasterFabric::where('id', $id)->update([
                'deleted_by' => Auth::user()->username,
                'deleted_at' => Carbon::now(),
            ]);
            HelperController::activityLog("DELETE MASTER FABRIC", 'master_fabrics', 'delete', $request->ip(), $request->userAgent(), null, $id);
            DB::commit();
            return response()->json('Delete Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json('Delete Failed', 422);
        }
    }
}
