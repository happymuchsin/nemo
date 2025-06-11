<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\HistoryAddStock;
use App\Models\HistoryAddDeadStock;
use App\Models\HistoryEditDeadStock;
use App\Models\HistoryOutDeadStock;
use App\Models\HistoryTransfer;
use App\Models\MasterArea;
use App\Models\MasterNeedle;
use App\Models\Stock;
use App\Models\DeadStock;
use App\Models\HistoryOutStock;
use App\Models\MasterBox;
use App\Models\MasterCounter;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;

class DeadStockController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'user_dead_stock';
        $title = 'USER DEAD STOCK';

        HelperController::activityLog('OPEN USER DEAD STOCK', 'dead_stocks', 'read', $request->ip(), $request->userAgent());

        $area = MasterArea::get();
        $needle = MasterNeedle::get();

        $counter = MasterCounter::get();
        $box = MasterBox::where('tipe', 'NORMAL')->get();

        return view('User.DeadStock.index', compact('title', 'page', 'area', 'needle', 'counter', 'box'));
    }

    public function data(Request $request)
    {
        $tipe = $request->tipe;
        $filter_area = $request->filter_area;
        if ($tipe == 'data') {
            $data = DeadStock::join('master_areas as ma', 'ma.id', 'dead_stocks.master_area_id')
                ->join('master_needles as mn', 'mn.id', 'dead_stocks.master_needle_id')
                ->selectRaw('dead_stocks.id as id, ma.name as area, mn.brand as brand, mn.tipe as tipe, mn.size as size, mn.code as code, mn.machine as machine, mn.min_stock as min_stock, dead_stocks.`in` as `in`, dead_stocks.`out` as `out`')
                ->when($filter_area != 'all', function ($q) use ($filter_area) {
                    $q->where('master_area_id', $filter_area);
                })
                // untuk testing
                // ->where('master_area_id', 2)
                // ->where('master_needle_id', 10)
                ->get();
            return datatables()->of($data)
                ->addColumn('stock', function ($q) {
                    return $q->in - $q->out;
                })
                ->addColumn('action', function ($q) {
                    $h = '';
                    $onclick1 = 'onclick="btnTransfer(\'' . route('user.dead-stock.get', ['id' => $q->id]) . '\', \'' . 'add' . '\')"';
                    $color1 = 'text-success';
                    $onclick2 = 'onclick="btnTransfer(\'' . route('user.dead-stock.get', ['id' => $q->id]) . '\', \'' . 'minus' . '\')"';
                    $color2 = 'text-warning';
                    $h .= '<a href="#" class="text-center" title="Add" ' . $onclick1 . '><i class="fa fa-plus ' . $color1 . ' mr-3"></i></a>';
                    $h .= '<a href="#" class="text-center" title="Minus" ' . $onclick2 . '><i class="fa fa-minus ' . $color2 . ' mr-3"></i></a>';
                    return $h;
                })
                ->rawColumns(['action'])
                ->make(true);
        } else if ($tipe == 'counter') {
            $dead_stock_id = $request->dead_stock_id;
            $data = [];
            $dead_stock = DeadStock::where('id', $dead_stock_id)->first();
            $stock = Stock::where('master_area_id', $dead_stock->master_area_id)->where('master_needle_id', $dead_stock->master_needle_id)->where('is_clear', 'not')->get();
            foreach ($stock as $s) {
                $data[] = [
                    'counter' => $s->counter->name,
                    'box' => $s->box->name,
                    'stock' => '<input type="number" disabled id="stock' . $s->id . '" value="' . $s->in - $s->out . '">',
                    'transfer' => '<input type="number" id="transfer' . $s->id . '"><input type="hidden" class="input-transfer" id="' . $s->id . '">',
                    'after' => '<input type="number" disabled id="after' . $s->id . '" value="' . $s->in - $s->out . '">',
                ];
            }
            return datatables()->of($data)
                ->rawColumns(['stock', 'transfer', 'after'])
                ->make(true);
        }
    }

    public function get($id)
    {
        $s = DeadStock::with(['area', 'needle'])->where('id', $id)->first();
        return response()->json($s, 200);
    }

    public function history(Request $request)
    {
        $dead_stock_id = $request->dead_stock_id;
        $tipe = $request->tipe;
        $mode = $request->mode;

        $data = [];
        $history_transfer = HistoryTransfer::when($mode != '', function ($q) use ($mode, $dead_stock_id) {
            if ($mode == 'add') {
                $q->where('ke_id', $dead_stock_id);
            } else if ($mode == 'minus') {
                $q->where('dari_id', $dead_stock_id);
            }
        })
            ->get();
        foreach ($history_transfer as $h) {
            $stock = Stock::when($mode != '', function ($q) use ($mode, $h) {
                if ($mode == 'add') {
                    $q->where('id', $h->dari_id);
                } else if ($mode == 'minus') {
                    $q->where('id', $h->ke_id);
                }
            })
                ->first();
            $data[] = [
                'created_at' => $h->created_at->format('Y-m-d H:i:s'),
                'counter' => $stock->counter->name,
                'box' => $stock->box->name,
                'qty' => $h->qty,
            ];
        }
        return datatables()->of($data)
            ->make(true);
    }

    public function transfer(Request $request)
    {
        $mode = $request->mode;
        $dead_stock_id = $request->dead_stock_id;
        $data = $request->data;
        $now = Carbon::now();
        try {
            DB::beginTransaction();
            if ($mode == 'add') {
                $total = 0;
                foreach ($data as $d) {
                    $stock = Stock::where('id', $d['counter_id'])->first();
                    if ($d['after'] < 0) {
                        DB::rollBack();
                        return response()->json('Transfer is more than Stock', 422);
                    }
                    Stock::where('id', $d['counter_id'])->update([
                        'out' => $stock->out + $d['qty'],
                        'updated_by' => Auth::user()->username,
                        'updated_at' => $now,
                    ]);
                    HelperController::activityLog('UPDATE STOCK', 'stocks', 'update', $request->ip(), $request->userAgent(), json_encode([
                        'id' => $d['counter_id'],
                        'out' => $stock->out + $d['qty'],
                        'updated_by' => Auth::user()->username,
                        'updated_at' => $now,
                    ]), $d['counter_id']);
                    HistoryOutStock::create([
                        'stock_id' => $d['counter_id'],
                        'stock_before' => $stock->out,
                        'qty' => $d['qty'],
                        'stock_after' => $stock->out + $d['qty'],
                        'created_by' => Auth::user()->username,
                        'created_at' => $now,
                    ]);
                    HelperController::activityLog("CREATE HISTORY OUT STOCK", 'history_out_stocks', 'create', $request->ip(), $request->userAgent(), json_encode([
                        'stock_id' => $d['counter_id'],
                        'stock_before' => $stock->out,
                        'qty' => $d['qty'],
                        'stock_after' => $stock->out + $d['qty'],
                        'created_by' => Auth::user()->username,
                        'created_at' => $now,
                    ]));
                    HistoryTransfer::create([
                        'ke' => 'dead stock',
                        'ke_id' => $dead_stock_id,
                        'dari' => 'stock',
                        'dari_id' => $d['counter_id'],
                        'qty' => $d['qty'],
                        'created_by' => Auth::user()->username,
                        'created_at' => $now,
                    ]);
                    HelperController::activityLog("CREATE HISTORY TRANSFER", 'history_transfers', 'create', $request->ip(), $request->userAgent(), json_encode([
                        'ke' => 'dead stock',
                        'ke_id' => $dead_stock_id,
                        'dari' => 'stock',
                        'dari_id' => $d['counter_id'],
                        'qty' => $d['qty'],
                        'created_by' => Auth::user()->username,
                        'created_at' => $now,
                    ]));
                    $total += $d['qty'];
                }
                $dead_stock = DeadStock::where('id', $dead_stock_id)->first();
                DeadStock::where('id', $dead_stock_id)->update([
                    'in' => $dead_stock->in + $total,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => $now,
                ]);
                HelperController::activityLog('UPDATE DEAD STOCK', 'dead_stocks', 'update', $request->ip(), $request->userAgent(), json_encode([
                    'id' => $dead_stock_id,
                    'in' => $dead_stock->in + $total,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => $now,
                ]), $dead_stock_id);
                HistoryAddDeadStock::create([
                    'dead_stock_id' => $dead_stock_id,
                    'dead_stock_before' => $dead_stock->in,
                    'qty' => $total,
                    'dead_stock_after' => $dead_stock->in + $total,
                    'created_by' => Auth::user()->username,
                    'created_at' => $now,
                ]);
                HelperController::activityLog("CREATE HISTORY ADD DEAD STOCK", 'history_add_dead_stocks', 'create', $request->ip(), $request->userAgent(), json_encode([
                    'dead_stock_id' => $dead_stock_id,
                    'dead_stock_before' => $dead_stock->in,
                    'qty' => $total,
                    'dead_stock_after' => $dead_stock->in + $total,
                    'created_by' => Auth::user()->username,
                    'created_at' => $now,
                ]));
            } else if ($mode == 'minus') {
                $total = 0;
                foreach ($data as $d) {
                    $stock = Stock::where('id', $d['counter_id'])->first();
                    Stock::where('id', $d['counter_id'])->update([
                        'in' => $stock->in + $d['qty'],
                        'updated_by' => Auth::user()->username,
                        'updated_at' => $now,
                    ]);
                    HelperController::activityLog('UPDATE STOCK', 'stocks', 'update', $request->ip(), $request->userAgent(), json_encode([
                        'id' => $d['counter_id'],
                        'in' => $stock->in + $d['qty'],
                        'updated_by' => Auth::user()->username,
                        'updated_at' => $now,
                    ]), $d['counter_id']);
                    HistoryAddStock::create([
                        'stock_id' => $d['counter_id'],
                        'stock_before' => $stock->in,
                        'qty' => $d['qty'],
                        'stock_after' => $stock->in + $d['qty'],
                        'created_by' => Auth::user()->username,
                        'created_at' => $now,
                    ]);
                    HelperController::activityLog("CREATE HISTORY ADD STOCK", 'history_add_stocks', 'create', $request->ip(), $request->userAgent(), json_encode([
                        'stock_id' => $d['counter_id'],
                        'stock_before' => $stock->in,
                        'qty' => $d['qty'],
                        'stock_after' => $stock->in + $d['qty'],
                        'created_by' => Auth::user()->username,
                        'created_at' => $now,
                    ]));
                    HistoryTransfer::create([
                        'dari' => 'dead stock',
                        'dari_id' => $dead_stock_id,
                        'ke' => 'stock',
                        'ke_id' => $d['counter_id'],
                        'qty' => $d['qty'],
                        'created_by' => Auth::user()->username,
                        'created_at' => $now,
                    ]);
                    HelperController::activityLog("CREATE HISTORY TRANSFER", 'history_transfers', 'create', $request->ip(), $request->userAgent(), json_encode([
                        'dari' => 'dead stock',
                        'dari_id' => $dead_stock_id,
                        'ke' => 'stock',
                        'ke_id' => $d['counter_id'],
                        'qty' => $d['qty'],
                        'created_by' => Auth::user()->username,
                        'created_at' => $now,
                    ]));
                    $total += $d['qty'];
                }
                $dead_stock = DeadStock::where('id', $dead_stock_id)->first();
                $stock_dead_stock = $dead_stock->in - $dead_stock->out;
                if ($total > $stock_dead_stock) {
                    DB::rollBack();
                    return response()->json('Total Transfer is more than Remaining Stock', 422);
                }
                DeadStock::where('id', $dead_stock_id)->update([
                    'out' => $dead_stock->out + $total,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => $now,
                ]);
                HelperController::activityLog('UPDATE DEAD STOCK', 'dead_stocks', 'update', $request->ip(), $request->userAgent(), json_encode([
                    'id' => $dead_stock_id,
                    'out' => $dead_stock->out + $total,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => $now,
                ]), $dead_stock_id);
                HistoryOutDeadStock::create([
                    'dead_stock_id' => $dead_stock_id,
                    'dead_stock_before' => $dead_stock->out,
                    'qty' => $total,
                    'dead_stock_after' => $dead_stock->out + $total,
                    'created_by' => Auth::user()->username,
                    'created_at' => $now,
                ]);
                HelperController::activityLog("CREATE HISTORY OUT DEAD STOCK", 'history_add_dead_stocks', 'create', $request->ip(), $request->userAgent(), json_encode([
                    'dead_stock_id' => $dead_stock_id,
                    'dead_stock_before' => $dead_stock->out,
                    'qty' => $total,
                    'dead_stock_after' => $dead_stock->out + $total,
                    'created_by' => Auth::user()->username,
                    'created_at' => $now,
                ]));
            }
            DB::commit();
            return response()->json('Transfer Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json('Transfer Failed', 422);
        }
    }
}
