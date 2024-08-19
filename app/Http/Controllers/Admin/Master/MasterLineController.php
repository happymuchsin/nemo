<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\MasterArea;
use App\Models\MasterLine;
use App\Models\Needle;
use App\Models\NeedleDetail;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MasterLineController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'admin_master_line';
        $title = 'ADMIN MASTER LINE';

        HelperController::activityLog('OPEN ADMIN MASTER LINE', 'master_lines', 'read', $request->ip(), $request->userAgent());

        $admin_master = 'menu-open';
        $area = MasterArea::get();

        return view('Admin.Master.Line.index', compact('title', 'page', 'admin_master', 'area'));
    }

    public function data(Request $request)
    {
        $data = MasterLine::with(['area'])->get();
        return datatables()->of($data)
            ->addColumn('area', function ($q) {
                return $q->area->name;
            })
            ->addColumn('action', function ($q) {
                return view('includes.admin.action', [
                    'edit' => route('admin.master.line.edit', ['id' => $q->id]),
                    'hapus' => route('admin.master.line.hapus', ['id' => $q->id]),
                ]);
            })
            ->make(true);
    }

    public function crup(Request $request)
    {
        $id = $request->id;
        $master_area_id = $request->master_area_id;
        $name = strtoupper($request->name);

        try {
            DB::beginTransaction();

            if ($id == 0) {
                $s = MasterLine::where('master_area_id', $master_area_id)->where('name', $name)->first();
                if ($s) {
                    return response()->json('Line already used', 422);
                } else {
                    MasterLine::create([
                        'master_area_id' => $master_area_id,
                        'name' => $name,
                        'created_by' => Auth::user()->username,
                        'created_at' => Carbon::now(),
                    ]);
                    HelperController::activityLog("CREATE MASTER LINE", 'master_lines', 'create', $request->ip(), $request->userAgent(), json_encode([
                        'master_area_id' => $master_area_id,
                        'name' => $name,
                        'created_by' => Auth::user()->username,
                        'created_at' => Carbon::now(),
                    ]));
                }
            } else {
                $c = 0;
                $s = MasterLine::where('id', $id)->first();
                if ($s->name != $name) {
                    $u = MasterLine::where('master_area_id', $master_area_id)->where('name', $name)->first();
                    if ($u) {
                        return response()->json('Line already used', 422);
                    } else {
                        $c = 1;
                    }
                } else {
                    $c = 1;
                }

                if ($c == 1) {
                    MasterLine::where('id', $id)->update([
                        'master_area_id' => $master_area_id,
                        'name' => $name,
                        'updated_by' => Auth::user()->username,
                        'updated_at' => Carbon::now(),
                    ]);
                    HelperController::activityLog("UPDATE MASTER LINE", 'master_lines', 'update', $request->ip(), $request->userAgent(), json_encode([
                        'id' => $id,
                        'master_area_id' => $master_area_id,
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
        $s = MasterLine::find($id);
        return response()->json($s, 200);
    }

    public function hapus(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $n = Needle::where('master_line_id', $id)->get();
            foreach ($n as $n) {
                NeedleDetail::where('needle_id', $n->id)->update([
                    'deleted_by' => Auth::user()->username,
                    'deleted_at' => Carbon::now(),
                ]);
            }
            Needle::where('master_line_id', $id)->update([
                'deleted_by' => Auth::user()->username,
                'deleted_at' => Carbon::now(),
            ]);
            MasterLine::where('id', $id)->update([
                'deleted_by' => Auth::user()->username,
                'deleted_at' => Carbon::now(),
            ]);
            HelperController::activityLog("DELETE MASTER LINE", 'master_lines', 'delete', $request->ip(), $request->userAgent(), null, $id);
            DB::commit();
            return response()->json('Delete Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json('Delete Failed', 422);
        }
    }
}
