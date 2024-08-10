<?php

namespace App\Http\Controllers\Admin\Tools;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HomeController;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ToolsRoleController extends Controller
{
    public function __construct()
    {

        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'admin_tools_role';
        $title = 'ADMIN TOOLS ROLE';

        $admin_tools = 'menu-open';

        return view('Admin.Tools.Role.index', compact('title', 'page', 'admin_tools'));
    }

    public function data()
    {
        $data = Role::orderBy('created_at', 'DESC');
        return datatables()->of($data)
            ->addColumn('action', function ($data) {
                return view('includes.admin.action', [
                    'detail' => route('admin.tools.role.detail', ['id' => $data->id, 'name' => $data->name]),
                    'edit' => route('admin.tools.role.edit', $data->id),
                    'hapus' => route('admin.tools.role.hapus', $data->id),
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
                if (Role::where('name', $name)->exists()) {
                    return response()->json('Role Name Already Exists!', 422);
                }
                Role::create([
                    'name' => $name,
                    'description' => $description,
                    'created_by' => Auth::user()->nik,
                ]);
                DB::commit();
                return response()->json('Saving Success', 200);
            } else {
                if (Role::where('name', $name)->where('id', '!=', $id)->exists()) {
                    return response()->json('Role already exists, please find another role name', 422);
                }
                Role::where('id', $id)->update([
                    'name' => $name,
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
        $data = Role::find($id);

        return response()->json($data, 200);
    }

    public function hapus($id)
    {
        $role = Role::find($id);
        if ($role->name == 'developer') {
            return response()->json('CANNOT DELETE ROLE DEVELOPER', 422);
        } else {
            if ($role->users->count() > 0) {
                return response()->json('ROLE STILL USED BY USER', 422);
            }
            $role = Role::findorFail($id)->delete();
            return response()->json(200);
        }
    }

    public function detail($id, $name)
    {
        return response(['name' => $name, 'role_id' => $id], 200);
    }

    public function spinner(Request $request)
    {
        $role_id = $request->role_id;
        $term = trim(strtoupper($request->q));
        $data = Permission::whereNotIn('id', function ($q) use ($role_id) {
            $q->select('p.id')
                ->from('roles as r')
                ->join('role_has_permissions as rhp', 'r.id', 'rhp.role_id')
                ->join('permissions as p', 'p.id', 'rhp.permission_id')
                ->when($role_id != 0, function ($q) use ($role_id) {
                    $q->where('r.id', $role_id);
                });
        })
            ->where('name', 'like', "%$term%")
            ->orderBy('name')
            ->get();

        return $data;
    }

    public function data_permission(Request $request)
    {
        $role_id = $request->role_id;
        $data = Role::selectRaw('p.id as id, p.name as name')
            ->join('role_has_permissions as rhp', 'roles.id', 'rhp.role_id')
            ->join('permissions as p', 'p.id', 'rhp.permission_id')
            ->when($role_id != 0, function ($q) use ($role_id) {
                $q->where('roles.id', $role_id);
            })->orderBy('p.name');

        return datatables()->of($data)
            ->addColumn('action', function ($data) use ($role_id) {
                return view('includes.admin.action', [
                    'hapusDetail' => route('admin.tools.role.hapus-permission', ['role_id' => $role_id, 'id' => $data->id]),
                ]);
            })
            ->make(true);
    }

    public function crup_permission(Request $request)
    {
        $role = Role::find($request->role_id);
        $role->givePermissionTo([(int) $request->permission_id]);

        return response()->json('Success', 200);
    }

    public function hapus_permission($role_id, $id)
    {
        $role = Role::find($role_id);
        if ($role->name == 'developer') {
            return response()->json('Cannot delete permission from developer', 422);
        } else {
            $permission_name = Permission::where('id', $id)->first();
            $permissions = $role->permissions()->where('permission_id', '!=', $id)->get();
            $array = [];
            foreach ($permissions as $key => $permission) {
                $array[] = $permission->id;
            }

            $role->revokePermissionTo($permission_name->name);

            return response()->json('Success', 200);
        }
    }
}
