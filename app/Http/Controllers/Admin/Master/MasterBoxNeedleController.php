<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\HistoryAddStock;
use App\Models\HistoryEditStock;
use App\Models\MasterArea;
use App\Models\MasterBox;
use App\Models\MasterCounter;
use App\Models\MasterNeedle;
use App\Models\Stock;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;

class MasterBoxNeedleController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'admin_master_box_needle';
        $title = 'ADMIN MASTER BOX NEEDLE';

        HelperController::activityLog('OPEN ADMIN MASTER BOX NEEDLE', 'stocks', 'read', $request->ip(), $request->userAgent());

        $area = MasterArea::get();
        $needle = MasterNeedle::get();

        $counter = MasterCounter::get();
        $box = MasterBox::where('tipe', 'NORMAL')->get();

        $admin_master = 'menu-open';
        return view('Admin.Master.BoxNeedle.index', compact('title', 'page', 'area', 'needle', 'counter', 'box', 'admin_master'));
    }

    public function data(Request $request)
    {
        $data = Stock::join('master_areas as ma', 'ma.id', 'stocks.master_area_id')
            ->join('master_counters as mc', 'mc.id', 'stocks.master_counter_id')
            ->join('master_boxes as mb', 'mb.id', 'stocks.master_box_id')
            ->join('master_needles as mn', 'mn.id', 'stocks.master_needle_id')
            ->selectRaw('stocks.id as id, ma.name as area, mc.name as counter, mb.name as box, mn.brand as brand, mn.tipe as tipe, mn.size as size, mn.code as code, mn.machine as machine, mn.min_stock as min_stock, stocks.`in` as `in`, stocks.`out` as `out`, stocks.is_clear as is_clear')
            ->where('is_clear', 'not')
            ->whereNull('stocks.status')
            ->get();
        return datatables()->of($data)
            ->addColumn('stock', function ($q) {
                return $q->in - $q->out;
            })
            ->addColumn('action', function ($q) {
                $h = '';
                if ($q->out > 0 || $q->in > 0) {
                    $h .= '<a href="#" class="text-center" title="Delete"><i class="fa fa-trash-alt text-secondary mr-3"></i></a>';
                } else {
                    $h .= '<a href="#" class="text-center" title="Delete" onclick="hapus(\'' . route('user.stock.hapus', ['id' => $q->id]) . '\')"><i class="fa fa-trash-alt text-danger mr-3"></i></a>';
                }
                return $h;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function spinner(Request $request)
    {
        $tipe = $request->tipe;
        $master_area_id = $request->master_area_id;
        if ($tipe == 'counter') {
            $x = MasterCounter::where('master_area_id', $master_area_id)->get();
        } else if ($tipe == 'box') {
            $master_counter_id = $request->master_counter_id;
            $x = [];
            $s = Stock::where('master_area_id', $master_area_id)
                ->where('master_counter_id', $master_counter_id)
                ->where('is_clear', 'not')
                ->whereNull('status')
                ->get();
            foreach ($s as $s) {
                $x[] = $s->master_box_id;
            }
            $x = MasterBox::where('master_counter_id', $master_counter_id)->whereNotIn('id', $x)->where('tipe', 'NORMAL')->get();
        } else if ($tipe == 'needle') {
            $needle_category = $request->needle_category;
            $x = [];
            $mn = MasterNeedle::when($needle_category == 'single', function ($q) {
                $q->where('tipe', 'like', '%DB X 1%');
            })
                ->when($needle_category == 'double', function ($q) {
                    $q->where('tipe', 'like', '%DP X 5%');
                })
                ->when($needle_category == 'obras', function ($q) {
                    $q->where('tipe', 'like', '%DC X 27%')->get();
                })
                ->when($needle_category == 'kansai', function ($q) {
                    $q->where('tipe', 'like', '%UO X 113%')->orWhere('tipe', 'like', '%DV X 57%')->get();
                })
                ->get();
            foreach ($mn as $m) {
                $d = new stdClass;
                $d->id = $m->id;
                $d->name = "$m->brand - $m->tipe - $m->size - $m->code - $m->machine";
                $x[] = $d;
            }
        } else {
            $x = [];
        }

        return response()->json($x, 200);
    }

    public function store(Request $request)
    {
        $master_area_id = $request->master_area_id;
        $master_counter_id = $request->master_counter_id;
        $master_box_id = $request->master_box_id;
        $master_needle_id = $request->master_needle_id;
        $now = Carbon::now();

        try {
            DB::beginTransaction();
            Stock::create([
                'master_area_id' => $master_area_id,
                'master_counter_id' => $master_counter_id,
                'master_box_id' => $master_box_id,
                'master_needle_id' => $master_needle_id,
                'in' => 0,
                'out' => 0,
                'created_by' => Auth::user()->username,
                'created_at' => $now,
            ]);

            HelperController::activityLog("CREATE STOCK", 'stocks', 'create', $request->ip(), $request->userAgent(), json_encode([
                'master_area_id' => $master_area_id,
                'master_counter_id' => $master_counter_id,
                'master_box_id' => $master_box_id,
                'master_needle_id' => $master_needle_id,
                'in' => 0,
                'out' => 0,
                'created_by' => Auth::user()->username,
                'created_at' => $now,
            ]));

            DB::commit();
            return response()->json('Saved Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json('Saved Failed', 422);
        }
    }

    public function hapus(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            Stock::where('id', $id)->update([
                'deleted_by' => Auth::user()->username,
                'deleted_at' => Carbon::now(),
            ]);
            HelperController::activityLog("DELETE STOCK", 'stocks', 'delete', $request->ip(), $request->userAgent(), null, $id);
            DB::commit();
            return response()->json('Delete Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json('Delete Failed', 422);
        }
    }
}
