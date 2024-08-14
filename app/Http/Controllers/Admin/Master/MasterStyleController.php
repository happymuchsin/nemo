<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\MasterBuyer;
use App\Models\MasterCategory;
use App\Models\MasterFabric;
use App\Models\MasterSample;
use App\Models\MasterStyle;
use App\Models\MasterSubCategory;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;

class MasterStyleController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'admin_master_style';
        $title = 'ADMIN MASTER STYLE';

        HelperController::activityLog('OPEN ADMIN MASTER STYLE', 'master_styles', 'read', $request->ip(), $request->userAgent());

        $admin_master = 'menu-open';

        $buyer = MasterBuyer::get();
        $category = MasterCategory::get();
        $subcategory = MasterSubCategory::get();
        $sample = MasterSample::get();
        $fabric = MasterFabric::get();

        return view('Admin.Master.Style.index', compact('title', 'page', 'admin_master', 'buyer', 'category', 'subcategory', 'sample', 'fabric'));
    }

    public function data(Request $request)
    {
        $range_date = explode(' - ', $request->filter_range_date);
        $start = $range_date[0] ? $range_date[0] : Carbon::today()->subMonth();
        $end = $range_date[1] ? $range_date[1] : Carbon::today();
        $filter_master_buyer_id = $request->filter_master_buyer_id;
        $filter_master_category_id = $request->filter_master_category_id;
        $filter_master_sub_category_id = $request->filter_master_sub_category_id;
        $filter_master_sample_id = $request->filter_master_sample_id;
        $filter_master_fabric_id = $request->filter_master_fabric_id;
        $data = MasterStyle::with(['buyer', 'category', 'sub_category', 'sample', 'fabric'])
            ->where('start', '>=', $start)
            ->when($filter_master_buyer_id != 'all', function ($q) use ($filter_master_buyer_id) {
                $q->where('master_buyer_id', $filter_master_buyer_id);
            })
            ->when($filter_master_category_id != 'all', function ($q) use ($filter_master_category_id) {
                $q->where('master_category_id', $filter_master_category_id);
            })
            ->when($filter_master_sub_category_id != 'all', function ($q) use ($filter_master_sub_category_id) {
                $q->where('master_sub_category_id', $filter_master_sub_category_id);
            })
            ->when($filter_master_sample_id != 'all', function ($q) use ($filter_master_sample_id) {
                $q->where('master_sample_id', $filter_master_sample_id);
            })
            ->when($filter_master_fabric_id != 'all', function ($q) use ($filter_master_fabric_id) {
                $q->where('master_fabric_id', $filter_master_fabric_id);
            })
            ->get();
        return datatables()->of($data)
            ->addColumn('buyer', function ($q) {
                return $q->buyer->name;
            })
            ->addColumn('category', function ($q) {
                return $q->category->name;
            })
            ->addColumn('sub_category', function ($q) {
                return $q->sub_category->name;
            })
            ->addColumn('sample', function ($q) {
                return $q->sample->name;
            })
            ->addColumn('fabric', function ($q) {
                return $q->fabric->name;
            })
            ->addColumn('action', function ($q) {
                return view('includes.admin.action', [
                    'edit' => route('admin.master.style.edit', ['id' => $q->id]),
                    'hapus' => route('admin.master.style.hapus', ['id' => $q->id]),
                ]);
            })
            ->make(true);
    }

    public function crup(Request $request)
    {
        $id = $request->id;
        $master_buyer_id = $request->master_buyer_id;
        $master_category_id = $request->master_category_id;
        $master_sub_category_id = $request->master_sub_category_id;
        $master_sample_id = $request->master_sample_id;
        $master_fabric_id = $request->master_fabric_id;
        $srf = $request->srf;
        $season = strtoupper($request->season);
        $name = strtoupper($request->name);
        $range_date = explode(' - ', $request->range_date);
        if (!$range_date[0] && !$range_date[1]) {
            return response()->json('Please select Start - End', 422);
        }
        $start = $range_date[0];
        $end = $range_date[1];

        try {
            DB::beginTransaction();

            if ($id == 0) {
                MasterStyle::create([
                    'master_buyer_id' => $master_buyer_id,
                    'master_category_id' => $master_category_id,
                    'master_sub_category_id' => $master_sub_category_id,
                    'master_sample_id' => $master_sample_id,
                    'master_fabric_id' => $master_fabric_id,
                    'srf' => $srf,
                    'season' => $season,
                    'name' => $name,
                    'start' => $start,
                    'end' => $end,
                    'created_by' => Auth::user()->username,
                    'created_at' => Carbon::now(),
                ]);
                HelperController::activityLog("CREATE MASTER STYLE", 'master_styles', 'create', $request->ip(), $request->userAgent(), json_encode([
                    'master_buyer_id' => $master_buyer_id,
                    'master_category_id' => $master_category_id,
                    'master_sub_category_id' => $master_sub_category_id,
                    'master_sample_id' => $master_sample_id,
                    'master_fabric_id' => $master_fabric_id,
                    'srf' => $srf,
                    'season' => $season,
                    'name' => $name,
                    'start' => $start,
                    'end' => $end,
                    'created_by' => Auth::user()->username,
                    'created_at' => Carbon::now(),
                ]));
            } else {
                MasterStyle::where('id', $id)->update([
                    'master_buyer_id' => $master_buyer_id,
                    'master_sub_category_id' => $master_sub_category_id,
                    'master_sample_id' => $master_sample_id,
                    'master_fabric_id' => $master_fabric_id,
                    'srf' => $srf,
                    'season' => $season,
                    'name' => $name,
                    'start' => $start,
                    'end' => $end,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => Carbon::now(),
                ]);
                HelperController::activityLog("UPDATE MASTER STYLE", 'master_styles', 'update', $request->ip(), $request->userAgent(), json_encode([
                    'id' => $id,
                    'master_buyer_id' => $master_buyer_id,
                    'master_sub_category_id' => $master_sub_category_id,
                    'master_sample_id' => $master_sample_id,
                    'master_fabric_id' => $master_fabric_id,
                    'srf' => $srf,
                    'season' => $season,
                    'name' => $name,
                    'start' => $start,
                    'end' => $end,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => Carbon::now(),
                ]), $id);
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
        $s = MasterStyle::where('id', $id)->first();
        $d = new stdClass;
        $d->id = $s->id;
        $d->master_buyer_id = $s->master_buyer_id;
        $d->master_category_id = $s->master_category_id;
        $d->master_sub_category_id = $s->master_sub_category_id;
        $d->master_sample_id = $s->master_sample_id;
        $d->master_fabric_id = $s->master_fabric_id;
        $d->srf = $s->srf;
        $d->season = $s->season;
        $d->name = $s->name;
        $d->range_date = $s->start . ' - ' . $s->end;
        return response()->json($d, 200);
    }

    public function hapus(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            MasterStyle::where('id', $id)->update([
                'deleted_by' => Auth::user()->username,
                'deleted_at' => Carbon::now(),
            ]);
            HelperController::activityLog("DELETE MASTER STYLE", 'master_styles', 'delete', $request->ip(), $request->userAgent(), null, $id);
            DB::commit();
            return response()->json('Delete Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json('Delete Failed', 422);
        }
    }
}
