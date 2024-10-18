<?php

namespace App\Http\Controllers\Admin\Tools;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\Approval;
use App\Models\MasterApproval;
use App\Models\MasterDivision;
use App\Models\MasterPosition;
use App\Models\Needle;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class ToolsUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'admin_tools_user';
        $title = 'ADMIN TOOLS USER';

        HelperController::activityLog('OPEN ADMIN USER', 'users', 'read', $request->ip(), $request->userAgent());

        // $admin_tools = 'menu-open';
        $admin_master = 'menu-open';

        $divisi = MasterDivision::where('name', '!=', 'DEVELOPER')->get();
        $position = MasterPosition::where('name', '!=', 'DEVELOPER')->get();

        return view('Admin.Tools.User.index', compact('title', 'page', 'admin_master', 'divisi', 'position'));
    }

    public function data(Request $request)
    {
        $data = User::with(['division', 'position'])
            ->whereIn('master_division_id', function ($q) {
                $q->select('id')
                    ->from('master_divisions')
                    ->where('name', '!=', 'DEVELOPER');
            })
            ->whereIn('master_position_id', function ($q) {
                $q->select('id')
                    ->from('master_positions')
                    ->where('name', '!=', 'DEVELOPER');
            })
            ->get();
        return datatables()->of($data)
            ->addColumn('division', function ($q) {
                return $q->division->name;
            })
            ->addColumn('position', function ($q) {
                return $q->position->name;
            })
            ->addColumn('role', function ($q) {
                $r = Role::selectRaw('roles.name as name, roles.description as ket')
                    ->join('model_has_roles as mhr', 'roles.id', 'mhr.role_id')
                    ->where('mhr.model_type', User::class)
                    ->where('mhr.model_id', $q->id)
                    ->get();
                $k = "";
                foreach ($r as $r) {
                    $k .= $r->ket;
                    $k .= ' ';
                }

                return $k;
            })
            ->addColumn('action', function ($q) {
                return view('includes.admin.action', [
                    'detail' => route('admin.tools.user.detail', ['id' => $q->id, 'username' => $q->username]),
                    'edit' => route('admin.tools.user.edit', ['id' => $q->id]),
                    'check' => route('admin.tools.user.check', ['id' => $q->id]),
                ]);
            })
            ->make(true);
    }

    public function crup(Request $request)
    {
        $id = $request->id;
        $username = $request->username;
        $name = strtoupper($request->name);
        $rfid = $request->rfid;
        $master_division_id = $request->master_division_id;
        $master_position_id = $request->master_position_id;
        $skill = $request->skill;
        $join_date = $request->join_date;
        $password = $request->password;

        try {
            DB::beginTransaction();
            if ($id == 0) {
                $s = User::where('username', $username)->first();
                if ($s) {
                    return response()->json('NIK / Username already used', 422);
                } else {
                    User::create([
                        'username' => $username,
                        'name' => $name,
                        'rfid' => $rfid,
                        'master_division_id' => $master_division_id,
                        'master_position_id' => $master_position_id,
                        'skill' => $skill,
                        'join_date' => $join_date,
                        'password' => bcrypt($password),
                        'created_by' => Auth::user()->username,
                        'created_at' => Carbon::now(),
                    ]);
                    HelperController::activityLog("CREATE USER", 'users', 'create', $request->ip(), $request->userAgent(), json_encode([
                        'username' => $username,
                        'name' => $name,
                        'rfid' => $rfid,
                        'master_division_id' => $master_division_id,
                        'master_position_id' => $master_position_id,
                        'skill' => $skill,
                        'join_date' => $join_date,
                        'password' => bcrypt($password),
                        'created_by' => Auth::user()->username,
                        'created_at' => Carbon::now(),
                    ]));
                }
            } else {
                $c = 0;
                $s = User::where('id', $id)->first();
                if ($s->username != $username) {
                    $u = User::where('username', $username)->first();
                    if ($u) {
                        return response()->json('NIK / Username already used', 422);
                    } else {
                        $c = 1;
                    }
                } else if ($s->rfid != $rfid) {
                    $u = User::where('rfid', $rfid)->first();
                    if ($u) {
                        return response()->json('RFID already used', 422);
                    } else {
                        $c = 1;
                    }
                } else {
                    $c = 1;
                }

                if ($c == 1) {
                    User::where('id', $id)->update([
                        'username' => $username,
                        'name' => $name,
                        'rfid' => $rfid,
                        'master_division_id' => $master_division_id,
                        'master_position_id' => $master_position_id,
                        'skill' => $skill,
                        'join_date' => $join_date,
                        'password' => $password ? bcrypt($password) : DB::raw('password'),
                        'updated_by' => Auth::user()->username,
                        'updated_at' => Carbon::now(),
                    ]);
                    HelperController::activityLog("UPDATE USER", 'users', 'update', $request->ip(), $request->userAgent(), json_encode([
                        'id' => $id,
                        'username' => $username,
                        'name' => $name,
                        'rfid' => $rfid,
                        'master_division_id' => $master_division_id,
                        'master_position_id' => $master_position_id,
                        'skill' => $skill,
                        'join_date' => $join_date,
                        'password' => $password ? bcrypt($password) : DB::raw('password'),
                        'updated_by' => Auth::user()->username,
                        'updated_at' => Carbon::now(),
                    ]), $id);
                }
            }

            DB::commit();
            return response()->json('Saved Success', 200);
        } catch (Exception $e) {
            return response()->json('Saved Failed', 422);
        }
    }

    public function edit($id)
    {
        $s = User::find($id);
        return response()->json($s, 200);
    }

    public function check(Request $request, $id)
    {
        $a = Approval::where('user_id', $id)->where('status', 'WAITING')->first();
        if ($a) {
            return response()->json(['tipe' => 'yes', 'message' => 'User has pending Approval, Are you sure want to Delete?', 'id' => $id], 200);
        } else {
            return response()->json(['tipe' => 'not', 'message' => '', 'id' => $id], 200);
        }
    }

    public function hapus(Request $request)
    {
        $id = $request->id;
        $u = User::where('id', $id)->first();
        if ($u->username == 'developer') {
            return response()->json('DEVELOPER CANNOT DELETED', 422);
        }

        $username = Auth::user()->username;
        $now = Carbon::now();

        $a = Approval::where('user_id', $id)->get();
        foreach ($a as $a) {
            $c = Carbon::parse($a->tanggal);
            if (strlen($c->month) == 1) {
                $month = '0' . $c->month;
            } else {
                $month = $c->month;
            }
            if ($a->filename) {
                if (file_exists($file = public_path("assets/uploads/needle/$c->year/$month/$a->id.$a->ext")))
                    unlink($file);
            }
        }
        Approval::where('user_id', $id)->update([
            'deleted_at' => $now,
            'deleted_by' => $username,
        ]);
        MasterApproval::where('user_id', $id)->update([
            'deleted_at' => $now,
            'deleted_by' => $username,
        ]);
        HelperController::activityLog("DELETE APPROVAL", 'approvals', 'delete', $request->ip(), $request->userAgent(), json_encode([
            'user_id' => $id,
            'deleted_at' => $now,
            'deleted_by' => $username,
        ]), $id);
        $n = Needle::where('user_id', $id)->get();
        foreach ($n as $n) {
            $c = Carbon::parse($n->created_at);
            if (strlen($c->month) == 1) {
                $month = '0' . $c->month;
            } else {
                $month = $c->month;
            }
            if ($n->filename) {
                if (file_exists($file = public_path("assets/uploads/needle/$c->year/$month/$n->id.$n->ext")))
                    unlink($file);
            }
        }
        Needle::where('user_id', $id)->update([
            'deleted_at' => $now,
            'deleted_by' => $username,
        ]);
        HelperController::activityLog("DELETE NEEDLE", 'needles', 'delete', $request->ip(), $request->userAgent(), json_encode([
            'user_id' => $id,
            'deleted_at' => $now,
            'deleted_by' => $username,
        ]), $id);
        User::where('id', $id)->update([
            'deleted_at' => $now,
            'deleted_by' => $username,
        ]);
        HelperController::activityLog("DELETE USER", 'users', 'delete', $request->ip(), $request->userAgent(), json_encode([
            'id' => $id,
            'deleted_at' => $now,
            'deleted_by' => $username,
        ]), $id);

        return response()->json('Delete Success', 200);
    }

    public function detail($id, $username)
    {
        return response(['username' => $username, 'user_id' => $id], 200);
    }

    public function data_role(Request $request)
    {
        $user_id = $request->user_id;
        $data = Role::selectRaw('roles.id as id, roles.name as name, roles.description as ket')
            ->join('model_has_roles as mhr', 'roles.id', 'mhr.role_id')
            ->where('mhr.model_type', User::class)
            ->when($user_id != 0, function ($q) use ($user_id) {
                $q->where('mhr.model_id', $user_id);
            });

        return datatables()->of($data)
            ->editColumn('name', function ($q) {
                return $q->ket;
            })
            ->addColumn('action', function ($data) use ($user_id) {
                return view('includes.admin.action', [
                    'hapusDetail' => route('admin.tools.user.hapus-role', ['user_id' => $user_id, 'id' => $data->id]),
                ]);
            })
            ->make(true);
    }

    public function spinner(Request $request)
    {
        $user_id = $request->user_id;
        $term = trim(strtoupper($request->q));
        $data = Role::whereNotIn('id', function ($q) use ($user_id) {
            $q->select('r.id')
                ->from('roles as r')
                ->join('model_has_roles as mhr', 'r.id', 'mhr.role_id')
                ->where('mhr.model_type', User::class)
                ->when($user_id != 0, function ($q) use ($user_id) {
                    $q->where('mhr.model_id', $user_id);
                });
        })
            ->where('name', 'like', "%$term%")
            ->where('name', '!=', 'developer')
            ->orderBy('name')
            ->get();

        return $data;
    }

    public function crup_role(Request $request)
    {
        $user = User::find($request->user_id);
        if ($user->username == 'developer') {
            return response()->json('Cannot edit role from developer master', 422);
        } else {
            $role_name = Role::find($request->role_id);
            $user->assignRole([$role_name->name]);

            return response()->json('Success', 200);
        }
    }

    public function hapus_role($user_id, $id)
    {
        $user = User::find($user_id);
        if ($user->username == 'developer') {
            return response()->json('Cannot remove role developer from developer master', 422);
        } else {
            $role_name = Role::find($id);
            $roles = $user->roles()->where('id', '!=', $id)->get();
            $array = [];
            foreach ($roles as $key => $role) {
                $array[] = $role->id;
            }

            $user->removeRole($role_name->name);

            return response()->json('Success', 200);
        }
    }
}
