<?php

namespace App\Http\Controllers\User;

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

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'user_stock';
        $title = 'USER STOCK';

        HelperController::activityLog('OPEN USER STOCK', 'stocks', 'read', $request->ip(), $request->userAgent());

        $area = MasterArea::get();
        $needle = MasterNeedle::get();

        $counter = MasterCounter::get();
        $box = MasterBox::where('tipe', 'NORMAL')->get();

        return view('User.Stock.index', compact('title', 'page', 'area', 'needle', 'counter', 'box'));
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
                // if ($q->is_clear == 'yes') {
                //     $h .= '<a href="#" class="text-center" title="Add Stock"><i class="fa fa-plus text-secondary mr-3"></i></a>';
                //     $h .= '<a href="#" class="text-center" title="Edit"><i class="fa fa-edit text-secondary mr-3"></i></a>';
                //     $h .= '<a href="#" class="text-center" title="Delete"><i class="fa fa-trash-alt text-secondary mr-3"></i></a>';
                // } else {
                if ($q->out > 0) {
                    $h .= '<a href="#" class="text-center" title="Add Stock" onclick="addStock(\'' . route('user.stock.add', ['id' => $q->id]) . '\')"><i class="fa fa-plus text-success mr-3"></i></a>';
                    $h .= '<a href="#" class="text-center" title="Edit"><i class="fa fa-edit text-secondary mr-3"></i></a>';
                    $h .= '<a href="#" class="text-center" title="Delete"><i class="fa fa-trash-alt text-secondary mr-3"></i></a>';
                } else {
                    $h .= '<a href="#" class="text-center" title="Add Stock"><i class="fa fa-plus text-secondary mr-3"></i></a>';
                    $h .= '<a href="#" class="text-center" title="Edit" onclick="edit(\'' . route('user.stock.edit', ['id' => $q->id]) . '\')"><i class="fa fa-edit text-info mr-3"></i></a>';
                    $h .= '<a href="#" class="text-center" title="Delete" onclick="hapus(\'' . route('user.stock.hapus', ['id' => $q->id]) . '\')"><i class="fa fa-trash-alt text-danger mr-3"></i></a>';
                }
                // }
                if ($q->in - $q->out != 0) {
                    $h .= '<a href="#" class="text-center" title="Clear"><i class="fa fa-shelves-empty text-secondary mr-3"></i></a>';
                } else {
                    $h .= '<a href="#" class="text-center" title="Clear" onclick="bersihkan(\'' . route('user.stock.clear', ['id' => $q->id]) . '\')"><i class="fa fa-shelves-empty text-danger mr-3"></i></a>';
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
                ->whereNull('status')
                ->where('is_clear', 'not')
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
        $qty = $request->qty;
        $now = Carbon::now();

        try {
            DB::beginTransaction();

            $i = Stock::create([
                'master_area_id' => $master_area_id,
                'master_counter_id' => $master_counter_id,
                'master_box_id' => $master_box_id,
                'master_needle_id' => $master_needle_id,
                'in' => $qty,
                'created_by' => Auth::user()->username,
                'created_at' => $now,
            ]);

            HelperController::activityLog("CREATE STOCK", 'stocks', 'create', $request->ip(), $request->userAgent(), json_encode([
                'master_area_id' => $master_area_id,
                'master_counter_id' => $master_counter_id,
                'master_box_id' => $master_box_id,
                'master_needle_id' => $master_needle_id,
                'in' => $qty,
                'created_by' => Auth::user()->username,
                'created_at' => $now,
            ]));

            HistoryAddStock::create([
                'stock_id' => $i->id,
                'stock_before' => 0,
                'qty' => $qty,
                'stock_after' => $qty,
                'created_by' => Auth::user()->username,
                'created_at' => $now,
            ]);
            HelperController::activityLog("CREATE HISTORY ADD STOCK", 'history_add_stocks', 'create', $request->ip(), $request->userAgent(), json_encode([
                'stock_id' => $i->id,
                'stock_before' => 0,
                'qty' => $qty,
                'stock_after' => $qty,
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

    public function edit($id)
    {
        $s = Stock::with(['area', 'counter', 'box', 'needle'])->where('id', $id)->first();
        return response()->json($s, 200);
    }

    public function add($id)
    {
        $s = Stock::with(['area', 'counter', 'box', 'needle'])->where('id', $id)->first();
        return response()->json($s, 200);
    }

    public function history(Request $request)
    {
        $stock_id = $request->stock_id;
        $data = HistoryAddStock::with(['user'])->where('stock_id', $stock_id)
            ->get();
        return datatables()->of($data)
            ->editColumn('created_at', function ($q) {
                return Carbon::parse($q->created_at)->format('Y-m-d H:i:s');
            })
            ->addColumn('user', function ($q) {
                return $q->user->username . ' - ' . $q->user->name;
            })
            ->make(true);
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $in = $request->in;
        $tipe = $request->tipe;
        $now = Carbon::now();

        try {
            DB::beginTransaction();

            $s = Stock::where('id', $id)->first();

            if ($tipe == 'edit') {
                Stock::where('id', $id)->update([
                    'in' => $in,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => $now,
                ]);
                HelperController::activityLog("EDIT STOCK", 'stocks', 'update', $request->ip(), $request->userAgent(), json_encode([
                    'id' => $id,
                    'in' => $in,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => $now,
                ]), $id);

                HistoryEditStock::create([
                    'stock_id' => $id,
                    'stock_before' => $s->in,
                    'stock_after' => $in,
                    'created_by' => Auth::user()->username,
                    'created_at' => $now,
                ]);
                HelperController::activityLog("CREATE HISTORY EDIT STOCK", 'history_edit_stocks', 'create', $request->ip(), $request->userAgent(), json_encode([
                    'stock_id' => $id,
                    'stock_before' => $s->in,
                    'stock_after' => $in,
                    'created_by' => Auth::user()->username,
                    'created_at' => $now,
                ]));
            } else if ($tipe == 'add') {
                Stock::where('id', $id)->update([
                    'in' => $s->in + $in,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => $now,
                ]);
                HelperController::activityLog("ADD STOCK", 'stocks', 'update', $request->ip(), $request->userAgent(), json_encode([
                    'in' => $s->in + $in,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => $now,
                ]), $id);

                HistoryAddStock::create([
                    'stock_id' => $id,
                    'stock_before' => $s->in,
                    'qty' => $in,
                    'stock_after' => $s->in + $in,
                    'created_by' => Auth::user()->username,
                    'created_at' => $now,
                ]);
                HelperController::activityLog("CREATE HISTORY ADD STOCK", 'history_add_stocks', 'create', $request->ip(), $request->userAgent(), json_encode([
                    'stock_id' => $id,
                    'stock_before' => $s->in,
                    'qty' => $in,
                    'stock_after' => $s->in + $in,
                    'created_by' => Auth::user()->username,
                    'created_at' => $now,

                ]));
            }

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

    public function clear(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            Stock::where('id', $id)->update([
                'is_clear' => 'yes',
                'updated_by' => Auth::user()->username,
                'updated_at' => Carbon::now(),
            ]);
            HelperController::activityLog("CLEAR STOCK", 'stocks', 'delete', $request->ip(), $request->userAgent(), null, $id);
            DB::commit();
            return response()->json('Clear Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json('Clear Failed', 422);
        }
    }
}
