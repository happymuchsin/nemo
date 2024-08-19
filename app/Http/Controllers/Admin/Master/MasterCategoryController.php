<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\MasterCategory;
use App\Models\MasterStyle;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MasterCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'admin_master_category';
        $title = 'ADMIN MASTER CATEGORY';

        HelperController::activityLog('OPEN ADMIN MASTER CATEGORY', 'master_categories', 'read', $request->ip(), $request->userAgent());

        $admin_master = 'menu-open';

        return view('Admin.Master.Category.index', compact('title', 'page', 'admin_master'));
    }

    public function data(Request $request)
    {
        $data = MasterCategory::get();
        return datatables()->of($data)
            ->addColumn('action', function ($q) {
                return view('includes.admin.action', [
                    'edit' => route('admin.master.category.edit', ['id' => $q->id]),
                    'hapus' => route('admin.master.category.hapus', ['id' => $q->id]),
                ]);
            })
            ->make(true);
    }

    public function crup(Request $request)
    {
        $id = $request->id;
        $name = strtoupper($request->name);

        try {
            DB::beginTransaction();

            if ($id == 0) {
                $s = MasterCategory::where('name', $name)->first();
                if ($s) {
                    return response()->json('Category already used', 422);
                } else {
                    MasterCategory::create([
                        'name' => $name,
                        'created_by' => Auth::user()->username,
                        'created_at' => Carbon::now(),
                    ]);
                    HelperController::activityLog("CREATE MASTER CATEGORY", 'master_categories', 'create', $request->ip(), $request->userAgent(), json_encode([
                        'name' => $name,
                        'created_by' => Auth::user()->username,
                        'created_at' => Carbon::now(),
                    ]));
                }
            } else {
                $c = 0;
                $s = MasterCategory::where('id', $id)->first();
                if ($s->name != $name) {
                    $u = MasterCategory::where('name', $name)->first();
                    if ($u) {
                        return response()->json('Category already used', 422);
                    } else {
                        $c = 1;
                    }
                } else {
                    $c = 1;
                }

                if ($c == 1) {
                    MasterCategory::where('id', $id)->update([
                        'name' => $name,
                        'updated_by' => Auth::user()->username,
                        'updated_at' => Carbon::now(),
                    ]);
                    HelperController::activityLog("UPDATE MASTER CATEGORY", 'master_categories', 'update', $request->ip(), $request->userAgent(), json_encode([
                        'id' => $id,
                        'name' => $name,
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
        $s = MasterCategory::find($id);
        return response()->json($s, 200);
    }

    public function hapus(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            MasterStyle::where('master_category_id', $id)->update([
                'deleted_by' => Auth::user()->username,
                'deleted_at' => Carbon::now(),
            ]);
            MasterCategory::where('id', $id)->update([
                'deleted_by' => Auth::user()->username,
                'deleted_at' => Carbon::now(),
            ]);
            HelperController::activityLog("DELETE MASTER CATEGORY", 'master_categories', 'delete', $request->ip(), $request->userAgent(), null, $id);
            DB::commit();
            return response()->json('Delete Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json('Delete Failed', 422);
        }
    }
}
