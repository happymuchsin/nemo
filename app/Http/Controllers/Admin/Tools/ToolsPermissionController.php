<?php

namespace App\Http\Controllers\Admin\Tools;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HomeController;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use stdClass;

class ToolsPermissionController extends Controller
{
    public function __construct()
    {

        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'admin_tools_permission';
        $title = 'ADMIN TOOLS PERMISSION';

        $admin_tools = 'menu-open';

        return view('Admin.Tools.Permission.index', compact('title', 'page', 'admin_tools'));
    }

    public function data()
    {
        $data = Permission::orderBy('updated_at', 'DESC');
        return datatables()->of($data)
            ->addColumn('action', function ($data) {
                return view('includes.admin.action', [
                    'edit' => route('admin.tools.permission.edit', $data->id),
                    'hapus' => route('admin.tools.permission.hapus', $data->id),
                ]);
            })
            ->make(true);
    }

    public function crup(Request $request)
    {
        $id = $request->id;
        $name = strtolower(trim($request->name));
        $description = ucwords(trim($request->description));
        try {
            DB::beginTransaction();
            if ($id == 0) {
                if (Permission::where('name', $name)->exists()) {
                    return response()->json('Permission Name Already Exists!', 422);
                }
                Permission::create([
                    'name' => $name,
                    'display_name' => $name,
                    'description' => $description,
                    'created_by' => Auth::user()->nik,
                ]);
                DB::commit();
                return response()->json('Saving Success', 200);
            } else {
                if (Permission::where('name', $name)->where('id', '!=', $id)->exists()) {
                    return response()->json('Permission already exists, please find another permission name.', 422);
                }
                Permission::where('id', $id)->update([
                    'name' => $name,
                    'display_name' => $name,
                    'description' => $description,
                    'updated_by' => Auth::user()->nik,
                ]);
                DB::commit();
                return response()->json('Update Success', 200);
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json('Saving Failed', 422);
        }
    }

    public function edit($id)
    {
        $data = Permission::find($id);

        return response()->json($data, 200);
    }

    public function hapus($id)
    {
        $permission = Permission::findOrFail($id);
        $roles = $permission->roles;
        if ($roles->isNotEmpty()) {
            return response()->json('Permission still used!', 422);
        }
        $permission = Permission::findorFail($id)->delete();

        return response()->json(200);
    }
}
