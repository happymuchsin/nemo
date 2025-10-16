<?php

namespace App\Http\Controllers\Admin\Tools;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\ApprovalMissingFragment;
use App\Models\Needle;
use App\Models\TimeLimit;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ToolsNeedleControlController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'admin_tools_needle_control';
        $title = 'ADMIN TOOLS NEEDLE CONTROL';

        HelperController::activityLog('OPEN ADMIN TOOLS NEEDLE CONTROL', '', 'read', $request->ip(), $request->userAgent());

        $admin_tools = 'menu-open';
        return view('Admin.Tools.NeedleControl.index', compact('title', 'page', 'admin_tools'));
    }

    public function data(Request $request)
    {
        $tipe = $request->tipe;
        if ($tipe == 'limit') {
            $data = TimeLimit::where('tipe', 'needle')->get();
            return datatables()->of($data)
                ->addColumn('action', function ($q) {
                    return view('includes.admin.action', [
                        'edit' => route('admin.tools.needle-control.edit', ['tipe' => 'limit', 'id' => $q->id]),
                    ]);
                })
                ->rawColumns(['action'])
                ->make(true);
        } else if ($tipe == 'needle') {
            $filter_date = $request->filter_date;
            $data = Needle::with(['style.buyer'])->join('master_statuses as ms', 'ms.id', 'needles.master_status_id')
                ->join('users as u', 'u.id', 'needles.user_id')
                ->join('master_lines as ml', 'ml.id', 'needles.master_line_id')
                ->leftJoin('master_needles as mn', 'mn.id', 'needles.master_needle_id')
                ->selectRaw('needles.created_at as created_at, ml.name as line, u.username as username, u.name as name, mn.brand as brand, mn.tipe as tipe, mn.size as size, ms.name as remark, needles.filename as filename, needles.ext as ext, needles.id as id, needles.user_id as user_id, master_line_id, master_style_id')
                ->whereDate('needles.created_at', $filter_date)
                ->whereIn('needles.master_status_id', [1, 2, 3, 4])
                ->whereNull('ms.deleted_at')
                ->whereNull('u.deleted_at')
                ->whereNull('ml.deleted_at')
                ->whereNull('mn.deleted_at')
                ->orderBy('needles.created_at')
                ->get();
            return datatables()->of($data)
                ->addColumn('time', function ($q) {
                    return Carbon::parse($q->created_at)->format('H:i:s');
                })
                ->addColumn('rfid', function ($q) {
                    return Carbon::parse($q->scan_rfid)->format('Y-m-d H:i:s');
                })
                ->addColumn('box', function ($q) {
                    return Carbon::parse($q->scan_box)->format('Y-m-d H:i:s');
                })
                ->addColumn('user', function ($q) {
                    return $q->username . ' - ' . $q->name;
                })
                ->addColumn('buyer', function ($q) {
                    return $q->style ? $q->style->buyer->name : '';
                })
                ->addColumn('style', function ($q) {
                    return $q->style ? $q->style->name : '';
                })
                ->addColumn('gambar', function ($q) {
                    $c = Carbon::parse($q->created_at);
                    if (strlen($c->month) == 1) {
                        $month = '0' . $c->month;
                    } else {
                        $month = $c->month;
                    }
                    $h = '';
                    if ($q->filename) {
                        $gambar = asset("assets/uploads/needle/$c->year/$month/$q->id.$q->ext");
                    } else {
                        $a = ApprovalMissingFragment::where('needle_id', $q->id)->first();
                        if ($a) {
                            $gambar = asset("assets/uploads/needle/$c->year/$month/$a->id.$a->ext");
                        } else {
                            $aa = ApprovalMissingFragment::where('user_id', $q->user_id)->where('master_line_id', $q->master_line_id)->where('master_style_id', $q->master_style_id)->where('updated_at', $q->created_at)->first();
                            if ($aa) {
                                $gambar = asset("assets/uploads/needle/$c->year/$month/$aa->id.$aa->ext");
                            } else {
                                $gambar = asset('assets/img/altgambar.jpeg');
                            }
                        }
                    }
                    $h .= '<a href="#" onclick="poto(\'' . $gambar . '\')"><img src="' . $gambar . '" width="75px" /></a>';
                    return $h;
                })
                ->addColumn('action', function ($q) {
                    return view('includes.admin.action', [
                        'hapus' => route('admin.tools.needle-control.hapus', ['tipe' => 'needle', 'id' => $q->id]),
                    ]);
                })
                ->rawColumns(['gambar'])
                ->make(true);
        }
    }

    public function update(Request $request)
    {
        $tipe = $request->tipe;
        $id = $request->id;
        $waktu = $request->waktu;
        $now = Carbon::now();

        try {
            DB::beginTransaction();

            if ($tipe == 'limit') {
                TimeLimit::where('id', $id)->update([
                    'waktu' => $waktu,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => $now,
                ]);
                HelperController::activityLog("UPDATE TIME LIMIT", 'time_limits', 'update', $request->ip(), $request->userAgent(), json_encode([
                    'id' => $id,
                    'waktu' => $waktu,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => $now,
                ]), $id);
            }

            DB::commit();
            return response()->json('Save Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json('Save Failed', 422);
        }
    }

    public function edit($tipe, $id)
    {
        if ($tipe == 'limit') {
            $s = TimeLimit::find($id);
        }
        return response()->json($s, 200);
    }

    public function hapus(Request $request, $tipe, $id)
    {
        $now = Carbon::now();
        try {
            DB::beginTransaction();
            if ($tipe == 'needle') {
                $needle = Needle::find($id);
                $c = Carbon::parse($needle->created_at);
                if (strlen($c->month) == 1) {
                    $month = '0' . $c->month;
                } else {
                    $month = $c->month;
                }
                if ($needle->filename) {
                    if (file_exists($file = asset("assets/uploads/needle/$c->year/$month/$needle->id.$needle->ext")))
                        unlink($file);
                }
                Needle::where('id', $id)->update([
                    'remark' => 'DELETED NEEDLE CONTROL by ' . Auth::user()->username,
                    'deleted_by' => Auth::user()->username,
                    'deleted_at' => $now,
                ]);
                HelperController::activityLog("DELETE NEEDLE", 'needles', 'delete', $request->ip(), $request->userAgent(), null, $id);
            }
            DB::commit();
            return response()->json('Delete Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json('Delete Failed', 422);
        }
    }
}
