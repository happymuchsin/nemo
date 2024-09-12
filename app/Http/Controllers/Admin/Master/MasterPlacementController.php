<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\MasterCounter;
use App\Models\MasterLine;
use App\Models\MasterPlacement;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;

class MasterPlacementController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'admin_master_placement';
        $title = 'ADMIN MASTER PLACEMENT';

        HelperController::activityLog('OPEN ADMIN MASTER PLACEMENT', 'master_placements', 'read', $request->ip(), $request->userAgent());

        $admin_master = 'menu-open';

        return view('Admin.Master.Placement.index', compact('title', 'page', 'admin_master'));
    }

    public function data(Request $request)
    {
        $data = User::with(['division', 'position'])
            ->when(env('APP_ENV') != 'local', function ($q) {
                $q->where('name', '!=', 'developer');
            })
            ->get();
        return datatables()->of($data)
            ->addColumn('lokasi', function ($q) {
                $s = MasterPlacement::where('user_id', $q->id)->first();
                if ($s) {
                    $h = '';
                    if ($s->reff == 'line') {
                        $x = MasterLine::with(['area'])->where('id', $s->location_id)->first();
                        $h = $x->area->name . ' - ' . $x->name;
                    } else if ($s->reff == 'counter') {
                        $x = MasterCounter::with(['area'])->where('id', $s->location_id)->first();
                        $h = $x->area->name . ' - ' . $x->name;
                    }
                    return $h;
                } else {
                    return '';
                }
            })
            ->addColumn('counter', function ($q) {
                $s = MasterPlacement::where('user_id', $q->id)->first();
                if ($s) {
                    $h = '';
                    $x = MasterCounter::with(['area'])->where('id', $s->counter_id)->first();
                    if ($x) {
                        $h = $x->area->name . ' - ' . $x->name;
                    }
                    return $h;
                } else {
                    return '';
                }
            })
            ->addColumn('division', function ($q) {
                return $q->division->name;
            })
            ->addColumn('position', function ($q) {
                return $q->position->name;
            })
            ->addColumn('tipe', function ($q) {
                $s = MasterPlacement::where('user_id', $q->id)->first();
                if ($s) {
                    $h = '';
                    $h = strtoupper($s->reff);
                    return $h;
                } else {
                    return '';
                }
            })
            ->addColumn('action', function ($q) {
                return view('includes.admin.action', [
                    'edit' => route('admin.master.placement.edit', ['id' => $q->id]),
                    'hapus' => route('admin.master.placement.hapus', ['id' => $q->id]),
                ]);
            })
            ->make(true);
    }

    public function spinner(Request $request)
    {
        $tipe = $request->tipe;
        $reff = $request->reff;
        if ($tipe == 'reff') {
            if ($reff == 'line') {
                $item = MasterLine::with(['area'])->get();
            } else if ($reff == 'counter') {
                $item = MasterCounter::with(['area'])->get();
            } else {
                $item = [];
            }
        } else if ($tipe == 'lokasi') {
            $lokasi = $request->lokasi;
            if ($reff == 'line') {
                $line = MasterLine::where('id', $lokasi)->first();
                $item = MasterCounter::with(['area'])->where('master_area_id', $line->master_area_id)->get();
            }
        }
        return response()->json($item, 200);
    }

    public function crup(Request $request)
    {
        $id = $request->id;
        $reff = $request->reff;
        $lokasi = $request->lokasi;
        $counter = $request->counter;

        try {
            DB::beginTransaction();

            $s = MasterPlacement::where('user_id', $id)->first();
            if ($s) {
                $s->reff = $reff;
                $s->location_id = $lokasi;
                $s->counter_id = $counter;
                $s->updated_by = Auth::user()->username;
                $s->updated_at = Carbon::now();
                $s->save();
                HelperController::activityLog("UPDATE MASTER PLACEMENT", 'master_placements', 'update', $request->ip(), $request->userAgent(), json_encode([
                    'user_id' => $id,
                    'reff' => $reff,
                    'location_id' => $lokasi,
                    'counter_id' => $counter,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => Carbon::now(),
                ]), $id);
            } else {
                MasterPlacement::create([
                    'user_id' => $id,
                    'reff' => $reff,
                    'location_id' => $lokasi,
                    'counter_id' => $counter,
                    'created_by' => Auth::user()->username,
                    'created_at' => Carbon::now(),
                ]);
                HelperController::activityLog("CREATE MASTER PLACEMENT", 'master_placements', 'create', $request->ip(), $request->userAgent(), json_encode([
                    'user_id' => $id,
                    'reff' => $reff,
                    'location_id' => $lokasi,
                    'counter_id' => $counter,
                    'created_by' => Auth::user()->username,
                    'created_at' => Carbon::now(),
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
        $u = User::where('id', $id)->first();
        $d = new stdClass;
        $d->id = $u->id;
        $d->username = $u->username;
        $d->name = $u->name;
        $p = MasterPlacement::where('user_id', $id)->first();
        if ($p) {
            $d->reff = $p->reff;
            $d->lokasi = $p->location_id;
            $d->counter = $p->counter_id;
        }
        return response()->json($d, 200);
    }

    public function hapus(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            MasterPlacement::where('user_id', $id)->update([
                'deleted_by' => Auth::user()->username,
                'deleted_at' => Carbon::now(),
            ]);
            HelperController::activityLog("DELETE MASTER PLACEMENT", 'master_placements', 'delete', $request->ip(), $request->userAgent(), null, $id);
            DB::commit();
            return response()->json('Delete Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json('Delete Failed', 422);
        }
    }
}
