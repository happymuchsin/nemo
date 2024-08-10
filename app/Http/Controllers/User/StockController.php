<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
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

        $area = MasterArea::get();
        $needle = MasterNeedle::get();

        $counter = MasterCounter::get();
        $box = MasterBox::get();

        return view('User.Stock.index', compact('title', 'page', 'area', 'needle', 'counter', 'box'));
    }

    public function data(Request $request)
    {
        // $filter_area = $request->filter_area;
        // $filter_counter = $request->filter_counter;
        // $filter_box = $request->filter_box;
        $data = Stock::join('master_areas as ma', 'ma.id', 'stocks.master_area_id')
            ->join('master_counters as mc', 'mc.id', 'stocks.master_counter_id')
            ->join('master_boxes as mb', 'mb.id', 'stocks.master_box_id')
            ->join('master_needles as mn', 'mn.id', 'stocks.master_needle_id')
            ->selectRaw('stocks.id as id, ma.name as area, mc.name as counter, mb.name as box, mn.brand as brand, mn.tipe as tipe, mn.size as size, mn.code as code, mn.machine as machine, stocks.`in` as `in`, stocks.`out` as `out`, stocks.is_clear as is_clear')
            // ->when($filter_area != 'all', function ($q) use ($filter_area) {
            //     $q->where('master_area_id', $filter_area);
            // })
            // ->when($filter_counter != 'all', function ($q) use ($filter_counter) {
            //     $q->where('master_counter_id', $filter_counter);
            // })
            // ->when($filter_box != 'all', function ($q) use ($filter_box) {
            //     $q->where('master_box_id', $filter_box);
            // })
            ->where('is_clear', 'not')
            ->get();
        return datatables()->of($data)
            // ->addColumn('area', function ($q) {
            //     return $q->area->name;
            // })
            // ->addColumn('counter', function ($q) {
            //     return $q->counter->name;
            // })
            // ->addColumn('box', function ($q) {
            //     return $q->box->name;
            // })
            // ->addColumn('brand', function ($q) {
            //     return $q->needle->brand;
            // })
            // ->addColumn('tipe', function ($q) {
            //     return $q->needle->tipe;
            // })
            // ->addColumn('size', function ($q) {
            //     return $q->needle->size;
            // })
            // ->addColumn('code', function ($q) {
            //     return $q->needle->code;
            // })
            // ->addColumn('machine', function ($q) {
            //     return $q->needle->machine;
            // })
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
            $s = Stock::where('master_area_id', $master_area_id)->where('master_counter_id', $master_counter_id)->where('is_clear', 'not')->get();
            foreach ($s as $s) {
                $x[] = $s->master_box_id;
            }
            $x = MasterBox::where('master_counter_id', $master_counter_id)->whereNotIn('id', $x)->get();
        } else {
            $x = [];
        }

        return response()->json($x, 200);
    }

    // public function needle(Request $request)
    // {
    //     $master_needle_id = $request->master_needle_id;
    //     $s = MasterNeedle::where('id', $master_needle_id)->first();
    //     if ($s) {
    //         $d = new stdClass;
    //         $d->brand = $s->brand;
    //         $d->tipe = $s->tipe;
    //         $d->size = $s->size;
    //         $d->qty = '<input type="number" min="1" required name="qty[]" id="qty' . $s->id . '" class="form-control"><input type="text" hidden name="master_needle_id[]" id="master_needle_id' . $s->id . '" class="form-control" value="' . $s->id . '">';
    //         $x = '';
    //         $x .= '<a href="#" class="text-center" title="Delete" onclick="hapusStore(this, \'' . $s->id . '\')"><i class="fa fa-trash-alt text-danger mr-3"></i></a>';
    //         $d->action = $x;
    //         return response()->json($d, 200);
    //     } else {
    //         return response()->json('Needle not found', 422);
    //     }
    // }

    // public function store(Request $request)
    // {
    //     $master_needle_id = $request->master_needle_id;
    //     $qty = $request->qty;
    //     $tanggal = $request->tanggal;
    //     $master_area_id = $request->master_area_id;
    //     $master_counter_id = $request->master_counter_id;
    //     $master_box_id = $request->master_box_id;
    //     $now = Carbon::now();

    //     try {
    //         DB::beginTransaction();

    //         for ($i = 0; $i < count($qty); $i++) {
    //             Stock::create([
    //                 'tanggal' => $tanggal,
    //                 'master_area_id' => $master_area_id,
    //                 'master_counter_id' => $master_counter_id,
    //                 'master_box_id' => $master_box_id,
    //                 'master_needle_id' => $master_needle_id[$i],
    //                 'in' => $qty[$i],
    //                 'created_by' => Auth::user()->username,
    //                 'created_at' => $now,
    //             ]);
    //         }

    //         DB::commit();
    //         return response()->json('Saved Successfully', 200);
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         return response()->json('Saved Failed', 422);
    //     }
    // }

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

            // HistoryAddStock::create([
            //     'stock_id' => $i->id,
            //     'stock_before' => 0,
            //     'qty' => $qty,
            //     'stock_after' => $qty,
            //     'created_by' => Auth::user()->username,
            //     'created_at' => $now,
            // ]);

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

                HistoryEditStock::create([
                    'stock_id' => $id,
                    'stock_before' => $s->in,
                    'stock_after' => $in,
                    'created_by' => Auth::user()->username,
                    'created_at' => $now,
                ]);
            } else if ($tipe == 'add') {
                Stock::where('id', $id)->update([
                    'in' => $s->in + $in,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => $now,
                ]);

                HistoryAddStock::create([
                    'stock_id' => $id,
                    'stock_before' => $s->in,
                    'qty' => $in,
                    'stock_after' => $s->in + $in,
                    'created_by' => Auth::user()->username,
                    'created_at' => $now,
                ]);
            }

            DB::commit();
            return response()->json('Saved Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json('Saved Failed', 422);
        }
    }

    public function hapus($id)
    {
        try {
            DB::beginTransaction();
            Stock::where('id', $id)->update([
                'deleted_by' => Auth::user()->username,
                'deleted_at' => Carbon::now(),
            ]);
            DB::commit();
            return response()->json('Delete Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json('Delete Failed', 422);
        }
    }

    public function clear($id)
    {
        try {
            DB::beginTransaction();
            Stock::where('id', $id)->update([
                'is_clear' => 'yes',
                'updated_by' => Auth::user()->username,
                'updated_at' => Carbon::now(),
            ]);
            DB::commit();
            return response()->json('Clear Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json('Clear Failed', 422);
        }
    }
}
