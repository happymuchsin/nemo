<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\Approval;
use App\Models\MasterApproval;
use App\Models\Needle;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'user_approval';
        $title = 'USER APPROVAL';

        HelperController::activityLog('OPEN USER APPROVAL', 'approvals', 'read', $request->ip(), $request->userAgent());

        return view('User.Approval.index', compact('title', 'page'));
    }

    public function data(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $filter_status = $request->filter_status;

        $master_approval = MasterApproval::where('user_id', Auth::user()->id)->first();
        if ($master_approval) {
            $master_approval_id = $master_approval->id;
        } else {
            $master_approval_id = null;
        }

        $data = Approval::with(['user', 'needle', 'master_needle', 'master_line', 'master_style'])
            ->when(Auth::user()->username != 'developer', function ($q) use ($master_approval_id) {
                $q->where('master_approval_id', $master_approval_id);
            })
            ->whereYear('tanggal', $tahun)
            ->when($bulan != 'all', function ($q) use ($bulan) {
                $q->whereMonth('tanggal', $bulan);
            })
            ->where('status', $filter_status)
            ->orderBy('created_at', 'desc')
            ->get();
        return datatables()->of($data)
            ->editColumn('created_at', function ($q) {
                return Carbon::parse($q->created_at)->format('Y-m-d H:i:s');
            })
            // ->editColumn('needle_status', function ($q) {
            //     return strtoupper($q->needle_status);
            // })
            ->addColumn('needleBrand', function ($q) {
                return $q->master_needle ? $q->master_needle->brand : '';
            })
            ->addColumn('needleTipe', function ($q) {
                return $q->master_needle ? $q->master_needle->tipe : '';
            })
            ->addColumn('needleSize', function ($q) {
                return $q->master_needle ? $q->master_needle->size : '';
            })
            ->addColumn('line', function ($q) {
                return $q->master_line->name;
            })
            ->addColumn('style', function ($q) {
                return $q->master_style->name;
            })
            ->addColumn('requestor', function ($q) {
                return $q->user->username . ' - ' . $q->user->name;
            })
            ->editColumn('tipe', function ($q) {
                return strtoupper($q->needle_status);
                // if ($q->tipe == 'request-new') {
                //     return 'REQUEST NEW NEEDLE';
                // } else if ($q->tipe == 'missing-fragment') {
                //     return 'MISSING FRAGMENT';
                // }
            })
            // ->editColumn('remark', function ($q) {
            //     return strtoupper($q->remark);
            // })
            ->addColumn('gambar', function ($q) {
                if ($q->tipe == 'request-new') {
                    return '';
                }
                $c = Carbon::parse($q->tanggal);
                if (strlen($c->month) == 1) {
                    $month = '0' . $c->month;
                } else {
                    $month = $c->month;
                }
                $h = '';
                if ($q->filename) {
                    $gambar = asset("assets/uploads/needle/$c->year/$month/$q->id.$q->ext");
                } else {
                    $gambar = asset('assets/img/altgambar.jpeg');
                }
                $h .= '<a href="#" onclick="poto(\'' . $gambar . '\')"><img src="' . $gambar . '" width="75px" /></a>';
                return $h;
            })
            ->addColumn('action', function ($q) {
                $h = '';
                if ($q->status == 'WAITING') {
                    $h .= '<a href="#" class="text-center" title="Approve" onclick="approval(\'' . route('user.approval.approval', ['id' => $q->id, 'status' => 'approve']) . '\', \'' . 'APPROVE' . '\')"><i class="fa fa-check text-success mr-3"></i></a>';
                    $h .= '<a href="#" class="text-center" title="Reject" onclick="approval(\'' . route('user.approval.approval', ['id' => $q->id, 'status' => 'reject']) . '\', \'' . 'REJECT' . '\')"><i class="fa fa-x text-danger mr-3"></i></a>';
                } else {
                    $h .= '<a href="#" class="text-center" title="Approve"><i class="fa fa-check text-secondary mr-3"></i></a>';
                    $h .= '<a href="#" class="text-center" title="Reject"><i class="fa fa-x text-secondary mr-3"></i></a>';
                }
                return $h;
            })
            ->rawColumns(['action', 'gambar'])
            ->make(true);
    }

    public function approval(Request $request, $id, $status)
    {
        try {
            DB::beginTransaction();

            $now = Carbon::now();
            Approval::where('id', $id)->update([
                'status' => strtoupper($status),
                $status => $now,
                'updated_by' => Auth::user()->username,
                'updated_at' => $now,
            ]);

            HelperController::activityLog("UPDATE APPROVAL", 'approvals', 'update', $request->ip(), $request->userAgent(), json_encode([
                'id' => $id,
                'status' => strtoupper($status),
                $status => $now,
                'updated_by' => Auth::user()->username,
                'updated_at' => $now,
            ]), $id);

            HelperController::emitEvent('nemo', [
                'event' => 'nemoReload',
                'tipe' => 'reload',
            ]);

            DB::commit();
            return response()->json(ucwords($status) . ' Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(ucwords($status) . ' Failed', 422);
        }
    }
}
