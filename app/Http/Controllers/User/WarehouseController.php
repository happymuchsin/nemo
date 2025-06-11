<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\HistoryAddStock;
use App\Models\HistoryAddWarehouse;
use App\Models\HistoryEditWarehouse;
use App\Models\HistoryOutWarehouse;
use App\Models\HistoryTransfer;
use App\Models\MasterArea;
use App\Models\MasterNeedle;
use App\Models\Stock;
use App\Models\Warehouse;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;

class WarehouseController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'user_warehouse';
        $title = 'USER WAREHOUSE';

        HelperController::activityLog('OPEN USER WAREHOUSE', 'warehouses', 'read', $request->ip(), $request->userAgent());

        $area = MasterArea::get();
        $needle = MasterNeedle::get();

        return view('User.Warehouse.index', compact('title', 'page', 'area', 'needle'));
    }

    public function data(Request $request)
    {
        $tipe = $request->tipe;
        if ($tipe == 'data') {
            $data = Warehouse::join('master_areas as ma', 'ma.id', 'warehouses.master_area_id')
                ->join('master_needles as mn', 'mn.id', 'warehouses.master_needle_id')
                ->selectRaw('warehouses.id as id, ma.name as area, mn.brand as brand, mn.tipe as tipe, mn.size as size, mn.code as code, mn.machine as machine, mn.min_stock as min_stock, warehouses.`in` as `in`, warehouses.`out` as `out`')
                ->get();
            return datatables()->of($data)
                ->addColumn('stock', function ($q) {
                    return $q->in - $q->out;
                })
                ->addColumn('action', function ($q) {
                    $h = '';
                    if ($q->out > 0) {
                        $onclick1 = 'onclick="addWarehouse(\'' . route('user.warehouse.get', ['id' => $q->id]) . '\')"';
                        $color1 = 'text-success';
                        $onclick2 = '';
                        $color2 = 'text-secondary';
                        $onclick3 = '';
                        $color3 = 'text-secondary';
                    } else {
                        $onclick1 = '';
                        $color1 = 'text-secondary';
                        $onclick2 = 'onclick="edit(\'' . route('user.warehouse.get', ['id' => $q->id]) . '\')"';
                        $color2 = 'text-info';
                        $onclick3 = 'onclick="hapus(\'' . route('user.warehouse.hapus', ['id' => $q->id]) . '\')"';
                        $color3 = 'text-danger';
                    }
                    // if ($q->in - $q->out > 0) {
                    $onclick4 = 'onclick="btnTransfer(\'' . route('user.warehouse.get', ['id' => $q->id]) . '\')"';
                    $color4 = 'text-warning';
                    // } else {
                    //     $onclick4 = '';
                    //     $color4 = 'text-secondary';
                    // }
                    $h .= '<a href="#" class="text-center" title="Add" ' . $onclick1 . '><i class="fa fa-plus ' . $color1 . ' mr-3"></i></a>';
                    $h .= '<a href="#" class="text-center" title="Transfer" ' . $onclick4 . '><i class="fa fa-right-left ' . $color4 . ' mr-3"></i></a>';
                    $h .= '<a href="#" class="text-center" title="Edit" ' . $onclick2 . '><i class="fa fa-edit ' . $color2 . ' mr-3"></i></a>';
                    $h .= '<a href="#" class="text-center" title="Delete" ' . $onclick3 . '><i class="fa fa-trash-alt ' . $color3 . ' mr-3"></i></a>';
                    return $h;
                })
                ->rawColumns(['action'])
                ->make(true);
        } else if ($tipe == 'counter') {
            $warehouse_id = $request->warehouse_id;
            $data = [];
            $warehouse = Warehouse::where('id', $warehouse_id)->first();
            $stock = Stock::where('master_area_id', $warehouse->master_area_id)->where('master_needle_id', $warehouse->master_needle_id)->where('is_clear', 'not')->get();
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

    public function spinner(Request $request)
    {
        $tipe = $request->tipe;
        $master_area_id = $request->master_area_id;
        if ($tipe == 'needle') {
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
        $master_needle_id = $request->master_needle_id;
        $qty = $request->qty;
        $now = Carbon::now();

        try {
            DB::beginTransaction();

            $i = Warehouse::create([
                'master_area_id' => $master_area_id,
                'master_needle_id' => $master_needle_id,
                'in' => $qty,
                'created_by' => Auth::user()->username,
                'created_at' => $now,
            ]);

            HelperController::activityLog("CREATE WAREHOUSE", 'warehouses', 'create', $request->ip(), $request->userAgent(), json_encode([
                'master_area_id' => $master_area_id,
                'master_needle_id' => $master_needle_id,
                'in' => $qty,
                'created_by' => Auth::user()->username,
                'created_at' => $now,
            ]));

            HistoryAddWarehouse::create([
                'warehouse_id' => $i->id,
                'warehouse_before' => 0,
                'qty' => $qty,
                'warehouse_after' => $qty,
                'created_by' => Auth::user()->username,
                'created_at' => $now,
            ]);
            HelperController::activityLog("CREATE HISTORY ADD WAREHOUSE", 'history_add_warehouses', 'create', $request->ip(), $request->userAgent(), json_encode([
                'warehouse_id' => $i->id,
                'warehouse_before' => 0,
                'qty' => $qty,
                'warehouse_after' => $qty,
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

    public function get($id)
    {
        $s = Warehouse::with(['area', 'needle'])->where('id', $id)->first();
        return response()->json($s, 200);
    }

    public function history(Request $request)
    {
        $warehouse_id = $request->warehouse_id;
        $tipe = $request->tipe;
        if ($tipe == 'add') {
            $data = HistoryAddWarehouse::with(['user'])->where('warehouse_id', $warehouse_id)
                ->get();
            return datatables()->of($data)
                ->editColumn('created_at', function ($q) {
                    return Carbon::parse($q->created_at)->format('Y-m-d H:i:s');
                })
                ->addColumn('user', function ($q) {
                    return $q->user->username . ' - ' . $q->user->name;
                })
                ->make(true);
        } else if ($tipe == 'transfer') {
            $data = [];
            $history_transfer = HistoryTransfer::where('dari_id', $warehouse_id)->get();
            foreach ($history_transfer as $h) {
                $stock = Stock::where('id', $h->ke_id)->first();
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
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $in = $request->in;
        $tipe = $request->tipe;
        $now = Carbon::now();

        try {
            DB::beginTransaction();

            $s = Warehouse::where('id', $id)->first();

            if ($tipe == 'edit') {
                Warehouse::where('id', $id)->update([
                    'in' => $in,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => $now,
                ]);
                HelperController::activityLog("EDIT WAREHOUSE", 'warehouses', 'update', $request->ip(), $request->userAgent(), json_encode([
                    'id' => $id,
                    'in' => $in,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => $now,
                ]), $id);

                HistoryEditWarehouse::create([
                    'warehouse_id' => $id,
                    'warehouse_before' => $s->in,
                    'warehouse_after' => $in,
                    'created_by' => Auth::user()->username,
                    'created_at' => $now,
                ]);
                HelperController::activityLog("CREATE HISTORY EDIT WAREHOUSE", 'history_edit_warehouses', 'create', $request->ip(), $request->userAgent(), json_encode([
                    'warehouse_id' => $id,
                    'warehouse_before' => $s->in,
                    'warehouse_after' => $in,
                    'created_by' => Auth::user()->username,
                    'created_at' => $now,
                ]));
            } else if ($tipe == 'add') {
                Warehouse::where('id', $id)->update([
                    'in' => $s->in + $in,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => $now,
                ]);
                HelperController::activityLog("ADD WAREHOUSE", 'warehouses', 'update', $request->ip(), $request->userAgent(), json_encode([
                    'in' => $s->in + $in,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => $now,
                ]), $id);

                HistoryAddWarehouse::create([
                    'warehouse_id' => $id,
                    'warehouse_before' => $s->in,
                    'qty' => $in,
                    'warehouse_after' => $s->in + $in,
                    'created_by' => Auth::user()->username,
                    'created_at' => $now,
                ]);
                HelperController::activityLog("CREATE HISTORY ADD WAREHOUSE", 'history_add_warehouses', 'create', $request->ip(), $request->userAgent(), json_encode([
                    'warehouse_id' => $id,
                    'warehouse_before' => $s->in,
                    'qty' => $in,
                    'warehouse_after' => $s->in + $in,
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
            Warehouse::where('id', $id)->update([
                'deleted_by' => Auth::user()->username,
                'deleted_at' => Carbon::now(),
            ]);
            HelperController::activityLog("DELETE WAREHOUSE", 'warehouses', 'delete', $request->ip(), $request->userAgent(), null, $id);
            DB::commit();
            return response()->json('Delete Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json('Delete Failed', 422);
        }
    }

    public function transfer(Request $request)
    {
        $dari = $request->dari;
        $dari_id = $request->dari_id;
        $ke = $request->ke;
        $data = $request->data;
        $now = Carbon::now();
        try {
            DB::beginTransaction();
            $total = 0;
            foreach ($data as $d) {
                $stock = Stock::where('id', $d['ke_id'])->first();
                Stock::where('id', $d['ke_id'])->update([
                    'in' => $stock->in + $d['qty'],
                    'updated_by' => Auth::user()->username,
                    'updated_at' => $now,
                ]);
                HelperController::activityLog('UPDATE STOCK', 'stocks', 'update', $request->ip(), $request->userAgent(), json_encode([
                    'id' => $d['ke_id'],
                    'in' => $stock->in + $d['qty'],
                    'updated_by' => Auth::user()->username,
                    'updated_at' => $now,
                ]), $d['ke_id']);
                HistoryAddStock::create([
                    'stock_id' => $d['ke_id'],
                    'stock_before' => $stock->in,
                    'qty' => $d['qty'],
                    'stock_after' => $stock->in + $d['qty'],
                    'created_by' => Auth::user()->username,
                    'created_at' => $now,
                ]);
                HelperController::activityLog("CREATE HISTORY ADD STOCK", 'history_add_stocks', 'create', $request->ip(), $request->userAgent(), json_encode([
                    'stock_id' => $d['ke_id'],
                    'stock_before' => $stock->in,
                    'qty' => $d['qty'],
                    'stock_after' => $stock->in + $d['qty'],
                    'created_by' => Auth::user()->username,
                    'created_at' => $now,
                ]));
                HistoryTransfer::create([
                    'dari' => $dari,
                    'dari_id' => $dari_id,
                    'ke' => $ke,
                    'ke_id' => $d['ke_id'],
                    'qty' => $d['qty'],
                    'created_by' => Auth::user()->username,
                    'created_at' => $now,
                ]);
                HelperController::activityLog("CREATE HISTORY TRANSFER", 'history_transfers', 'create', $request->ip(), $request->userAgent(), json_encode([
                    'dari' => $dari,
                    'dari_id' => $dari_id,
                    'ke' => $ke,
                    'ke_id' => $d['ke_id'],
                    'qty' => $d['qty'],
                    'created_by' => Auth::user()->username,
                    'created_at' => $now,
                ]));
                $total += $d['qty'];
            }
            $warehouse = Warehouse::where('id', $dari_id)->first();
            $stock_warehouse = $warehouse->in - $warehouse->out;
            if ($total > $stock_warehouse) {
                DB::rollBack();
                return response()->json('Total Transfer is more than Remaining Stock', 422);
            }
            Warehouse::where('id', $dari_id)->update([
                'out' => $warehouse->out + $total,
                'updated_by' => Auth::user()->username,
                'updated_at' => $now,
            ]);
            HelperController::activityLog('UPDATE WAREHOUSE', 'warehouses', 'update', $request->ip(), $request->userAgent(), json_encode([
                'id' => $dari_id,
                'out' => $warehouse->out + $total,
                'updated_by' => Auth::user()->username,
                'updated_at' => $now,
            ]), $dari_id);
            HistoryOutWarehouse::create([
                'warehouse_id' => $dari_id,
                'warehouse_before' => $warehouse->out,
                'qty' => $total,
                'warehouse_after' => $warehouse->out + $total,
                'created_by' => Auth::user()->username,
                'created_at' => $now,
            ]);
            HelperController::activityLog("CREATE HISTORY OUT WAREHOUSE", 'history_add_warehouses', 'create', $request->ip(), $request->userAgent(), json_encode([
                'warehouse_id' => $dari_id,
                'warehouse_before' => $warehouse->out,
                'qty' => $total,
                'warehouse_after' => $warehouse->out + $total,
                'created_by' => Auth::user()->username,
                'created_at' => $now,
            ]));
            DB::commit();
            return response()->json('Transfer Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json('Transfer Failed', 422);
        }
    }
}
