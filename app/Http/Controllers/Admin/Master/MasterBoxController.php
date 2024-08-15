<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\MasterCounter;
use App\Models\MasterBox;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MasterBoxController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'admin_master_box';
        $title = 'ADMIN MASTER BOX';

        HelperController::activityLog('OPEN ADMIN MASTER BOX', 'master_boxes', 'read', $request->ip(), $request->userAgent());

        $admin_master = 'menu-open';
        $counter = MasterCounter::with(['area'])->get();

        return view('Admin.Master.Box.index', compact('title', 'page', 'admin_master', 'counter'));
    }

    public function data(Request $request)
    {
        $data = MasterBox::with(['counter' => function ($q) {
            $q->with(['area']);
        }])->get();
        return datatables()->of($data)
            ->addColumn('counter', function ($q) {
                return $q->counter->area->name . ' - ' .  $q->counter->name;
            })
            ->addColumn('action', function ($q) {
                return view('includes.admin.action', [
                    'edit' => route('admin.master.box.edit', ['id' => $q->id]),
                    'hapus' => route('admin.master.box.hapus', ['id' => $q->id]),
                ]);
            })
            ->make(true);
    }

    public function crup(Request $request)
    {
        $id = $request->id;
        $master_counter_id = $request->master_counter_id;
        $name = strtoupper($request->name);
        $tipe = strtoupper($request->tipe);
        $status = strtoupper($request->status);
        $rfid = $request->rfid;

        try {
            DB::beginTransaction();

            if ($id == 0) {
                $s = MasterBox::where('master_counter_id', $master_counter_id)->where('name', $name)->first();
                if ($s) {
                    return response()->json('Box already used', 422);
                } else {
                    MasterBox::create([
                        'master_counter_id' => $master_counter_id,
                        'name' => $name,
                        'rfid' => $rfid,
                        'tipe' => $tipe,
                        'status' => $status,
                        'created_by' => Auth::user()->username,
                        'created_at' => Carbon::now(),
                    ]);
                    HelperController::activityLog("CREATE MASTER BOX", 'master_boxes', 'create', $request->ip(), $request->userAgent(), json_encode([
                        'master_counter_id' => $master_counter_id,
                        'name' => $name,
                        'rfid' => $rfid,
                        'tipe' => $tipe,
                        'status' => $status,
                        'created_by' => Auth::user()->username,
                        'created_at' => Carbon::now(),
                    ]));
                }
            } else {
                $c = 0;
                $s = MasterBox::where('id', $id)->first();
                if ($s->name != $name) {
                    $u = MasterBox::where('master_counter_id', $master_counter_id)->where('name', $name)->first();
                    if ($u) {
                        return response()->json('Box already used', 422);
                    } else {
                        $c = 1;
                    }
                } else {
                    $c = 1;
                }

                if ($c == 1) {
                    MasterBox::where('id', $id)->update([
                        'master_counter_id' => $master_counter_id,
                        'name' => $name,
                        'rfid' => $rfid,
                        'tipe' => $tipe,
                        'status' => $status,
                        'updated_by' => Auth::user()->username,
                        'updated_at' => Carbon::now(),
                    ]);
                    HelperController::activityLog("UPDATE MASTER BOX", 'master_boxes', 'update', $request->ip(), $request->userAgent(), json_encode([
                        'id' => $id,
                        'master_counter_id' => $master_counter_id,
                        'name' => $name,
                        'rfid' => $rfid,
                        'tipe' => $tipe,
                        'status' => $status,
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
        $s = MasterBox::find($id);
        return response()->json($s, 200);
    }

    public function hapus(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            MasterBox::where('id', $id)->update([
                'deleted_by' => Auth::user()->username,
                'deleted_at' => Carbon::now(),
            ]);
            HelperController::activityLog("DELETE MASTER BOX", 'master_boxes', 'delete', $request->ip(), $request->userAgent(), null, $id);
            DB::commit();
            return response()->json('Delete Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json('Delete Failed', 422);
        }
    }
}
