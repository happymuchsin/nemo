<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\MasterHoliday;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;

class MasterHolidayController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'admin_master_holiday';
        $title = 'ADMIN MASTER HOLIDAY';

        HelperController::activityLog('OPEN ADMIN MASTER HOLIDAY', 'master_holidays', 'read', $request->ip(), $request->userAgent());

        $admin_master = 'menu-open';

        return view('Admin.Master.Holiday.index', compact('title', 'page', 'admin_master'));
    }

    public function calendar(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        try {
            $data = [];
            $master_holiday = MasterHoliday::whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->get();
            $collect_master_holiday = collect($master_holiday);
            $first = date('Y-m-d', strtotime($tahun . '-' . $bulan . '-1'));

            for ($i = 1; $i <= cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun); $i++) {
                $d = new stdClass;
                $tanggal = date('Y-m-d', strtotime($tahun . '-' . $bulan . '-' . $i));
                $x = $collect_master_holiday->where('tanggal', $tanggal);
                $d->judul = date('F Y', strtotime($tanggal));
                $d->title = $x->value('description') ?? '';
                $d->start = $x->value('tanggal') ?? '';
                $d->allDay = true;
                $d->className = $x->value('id') ? 'text-white bg-success' : '';
                $data[] = $d;
            }

            return response()->json([
                'first' => $first,
                'events' => $data,
            ], 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 422);
        }
    }

    public function crup(Request $request)
    {
        $now = Carbon::now();
        $tanggal = $request->tanggal;
        $description = strtoupper($request->description);

        try {
            DB::beginTransaction();

            $mh = MasterHoliday::where('tanggal', $tanggal)->first();
            if ($mh) {
                $mh->description = $description;
                $mh->updated_by = Auth::user()->username;
                $mh->updated_at = $now;
                $mh->save();
                HelperController::activityLog("UPDATE MASTER HOLIDAY", 'master_holidays', 'update', $request->ip(), $request->userAgent(), json_encode([
                    'id' => $mh->id,
                    'description' => $mh->description,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => $now,
                ]), $mh->id);
            } else {
                MasterHoliday::create([
                    'tanggal' => $tanggal,
                    'description' => $description,
                    'created_by' => Auth::user()->username,
                    'created_at' => $now,
                ]);
                HelperController::activityLog("CREATE MASTER HOLIDAY", 'master_holidays', 'create', $request->ip(), $request->userAgent(), json_encode([
                    'tanggal' => $tanggal,
                    'description' => $description,
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
}
