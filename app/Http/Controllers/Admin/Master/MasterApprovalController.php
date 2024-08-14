<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\MasterApproval;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MasterApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'admin_master_approval';
        $title = 'ADMIN MASTER APPROVAL';

        HelperController::activityLog('OPEN ADMIN MASTER APPROVAL', 'master_approvals', 'read', $request->ip(), $request->userAgent());

        $admin_master = 'menu-open';

        $user = User::permission('user-approval')->where('name', '!=', 'developer')->get();

        return view('Admin.Master.Approval.index', compact('title', 'page', 'admin_master', 'user'));
    }

    public function data(Request $request)
    {
        $data = MasterApproval::with(['user'])
            ->whereIn('user_id', function ($q) {
                $q->select('id')
                    ->from('users')
                    ->where('name', '!=', 'developer');
            })
            ->get();
        return datatables()->of($data)
            ->addColumn('user_id', function ($q) {
                return $q->user->username . ' - ' . $q->user->name;
            })
            ->addColumn('action', function ($q) {
                return view('includes.admin.action', [
                    'hapus' => route('admin.master.area.hapus', ['id' => $q->id]),
                ]);
            })
            ->make(true);
    }

    public function crup(Request $request)
    {
        $id = $request->id;
        $user_id = $request->user_id;

        try {
            DB::beginTransaction();

            if ($id == 0) {
                $s = MasterApproval::where('user_id', $user_id)->first();
                if ($s) {
                    return response()->json('Approval already used', 422);
                } else {
                    MasterApproval::create([
                        'user_id' => $user_id,
                        'created_by' => Auth::user()->username,
                        'created_at' => Carbon::now(),
                    ]);

                    HelperController::activityLog("CREATE MASTER APPROVAL", 'master_approvals', 'create', $request->ip(), $request->userAgent(), json_encode([
                        'user_id' => $user_id,
                        'created_by' => Auth::user()->username,
                        'created_at' => Carbon::now(),
                    ]));
                }
            } else {
                $c = 0;
                $s = MasterApproval::where('id', $id)->first();
                if ($s->user_id != $user_id) {
                    $u = MasterApproval::where('user_id', $user_id)->first();
                    if ($u) {
                        return response()->json('Approval already used', 422);
                    } else {
                        $c = 1;
                    }
                } else {
                    $c = 1;
                }

                if ($c == 1) {
                    MasterApproval::where('id', $id)->update([
                        'user_id' => $user_id,
                        'updated_by' => Auth::user()->username,
                        'updated_at' => Carbon::now(),
                    ]);

                    HelperController::activityLog("UPDATE MASTER APPROVAL", 'master_approvals', 'update', $request->ip(), $request->userAgent(), json_encode([
                        'id' => $id,
                        'user_id' => $user_id,
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
        $s = MasterApproval::find($id);
        return response()->json($s, 200);
    }

    public function hapus(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            MasterApproval::where('id', $id)->update([
                'deleted_by' => Auth::user()->username,
                'deleted_at' => Carbon::now(),
            ]);

            HelperController::activityLog("DELETE MASTER APPROVAL", 'master_approvals', 'delete', $request->ip(), $request->userAgent(), null, $id);
            DB::commit();
            return response()->json('Delete Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json('Delete Failed', 422);
        }
    }
}
