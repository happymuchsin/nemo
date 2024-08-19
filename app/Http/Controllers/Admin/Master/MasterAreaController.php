<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\MasterArea;
use App\Models\MasterBox;
use App\Models\MasterCounter;
use App\Models\MasterLine;
use App\Models\MasterPlacement;
use App\Models\Stock;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MasterAreaController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'admin_master_area';
        $title = 'ADMIN MASTER AREA';

        HelperController::activityLog('OPEN ADMIN MASTER AREA', 'master_areas', 'read', $request->ip(), $request->userAgent());

        $admin_master = 'menu-open';

        return view('Admin.Master.Area.index', compact('title', 'page', 'admin_master'));
    }

    public function data(Request $request)
    {
        $data = MasterArea::get();
        return datatables()->of($data)
            ->addColumn('action', function ($q) {
                return view('includes.admin.action', [
                    'edit' => route('admin.master.area.edit', ['id' => $q->id]),
                    'hapus' => route('admin.master.area.hapus', ['id' => $q->id]),
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
                $s = MasterArea::where('name', $name)->first();
                if ($s) {
                    return response()->json('Area already used', 422);
                } else {
                    MasterArea::create([
                        'name' => $name,
                        'created_by' => Auth::user()->username,
                        'created_at' => Carbon::now(),
                    ]);

                    HelperController::activityLog("CREATE MASTER AREA", 'master_areas', 'create', $request->ip(), $request->userAgent(), json_encode([
                        'name' => $name,
                        'created_by' => Auth::user()->username,
                        'created_at' => Carbon::now(),
                    ]));
                }
            } else {
                $c = 0;
                $s = MasterArea::where('id', $id)->first();
                if ($s->name != $name) {
                    $u = MasterArea::where('name', $name)->first();
                    if ($u) {
                        return response()->json('Area already used', 422);
                    } else {
                        $c = 1;
                    }
                } else {
                    $c = 1;
                }

                if ($c == 1) {
                    MasterArea::where('id', $id)->update([
                        'name' => $name,
                        'updated_by' => Auth::user()->username,
                        'updated_at' => Carbon::now(),
                    ]);

                    HelperController::activityLog("UPDATE MASTER AREA", 'master_areas', 'update', $request->ip(), $request->userAgent(), json_encode([
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
        $s = MasterArea::find($id);
        return response()->json($s, 200);
    }

    public function hapus(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $s = MasterLine::where('master_area_id', $id)->get();
            foreach ($s as $s) {
                MasterPlacement::where('reff', 'line')->where('location_id', $s->id)->update([
                    'deleted_by' => Auth::user()->username,
                    'deleted_at' => Carbon::now(),
                ]);
            }
            MasterLine::where('master_area_id', $id)->update([
                'deleted_by' => Auth::user()->username,
                'deleted_at' => Carbon::now(),
            ]);
            $s = MasterCounter::where('master_area_id', $id)->get();
            foreach ($s as $s) {
                MasterBox::where('master_counter_id', $s->id)->update([
                    'deleted_by' => Auth::user()->username,
                    'deleted_at' => Carbon::now(),
                ]);
                MasterPlacement::where('reff', 'counter')->where('location_id', $s->id)->update([
                    'deleted_by' => Auth::user()->username,
                    'deleted_at' => Carbon::now(),
                ]);
            }
            MasterCounter::where('master_area_id', $id)->update([
                'deleted_by' => Auth::user()->username,
                'deleted_at' => Carbon::now(),
            ]);
            Stock::where('master_area_id', $id)->update([
                'deleted_by' => Auth::user()->username,
                'deleted_at' => Carbon::now(),
            ]);
            MasterArea::where('id', $id)->update([
                'deleted_by' => Auth::user()->username,
                'deleted_at' => Carbon::now(),
            ]);
            HelperController::activityLog("DELETE MASTER AREA", 'master_areas', 'delete', $request->ip(), $request->userAgent(), null, $id);
            DB::commit();
            return response()->json('Delete Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json('Delete Failed', 422);
        }
    }
}
