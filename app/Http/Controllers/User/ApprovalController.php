<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\Approval;
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

        return view('User.Approval.index', compact('title', 'page'));
    }

    public function data(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $data = Approval::with(['user', 'needle' => function ($q) {
            $q->with(['line', 'needle']);
        }])
            ->where('approval_id', Auth::user()->id)
            ->whereYear('tanggal', $tahun)
            ->when($bulan != 'all', function ($q) use ($bulan) {
                $q->whereMonth('tanggal', $bulan);
            })
            ->where('status', 'WAITING')
            ->orderBy('created_at', 'desc')
            ->get();
        return datatables()->of($data)
            ->editColumn('created_at', function ($q) {
                return Carbon::parse($q->created_at)->format('Y-m-d H:i:s');
            })
            ->editColumn('needle_status', function ($q) {
                return strtoupper($q->needle_status);
            })
            ->addColumn('brand', function ($q) {
                return $q->needle->needle->brand;
            })
            ->addColumn('tipe', function ($q) {
                return $q->needle->needle->tipe;
            })
            ->addColumn('size', function ($q) {
                return $q->needle->needle->size;
            })
            ->addColumn('line', function ($q) {
                return $q->needle->line->name;
            })
            ->addColumn('requestor', function ($q) {
                return $q->user->username . ' - ' . $q->user->name;
            })
            ->addColumn('gambar', function ($q) {
                $c = Carbon::parse($q->tanggal);
                if (strlen($c->month) == 1) {
                    $month = '0' . $c->month;
                } else {
                    $month = $c->month;
                }
                $h = '';
                if ($q->filename) {
                    $gambar = asset("assets/uploads/needle/$c->year/$month/$q->needle_id/$q->id.$q->ext");
                } else {
                    $gambar = asset('assets/img/altgambar.jpeg');
                }
                $h .= '<a href="#" onclick="poto(\'' . $gambar . '\')"><img src="' . $gambar . '" width="75px" /></a>';
                return $h;
            })
            ->addColumn('action', function ($q) {
                $h = '';
                $h .= '<a href="#" class="text-center" title="Approve" onclick="approval(\'' . route('user.approval.approval', ['id' => $q->id, 'status' => 'approve']) . '\', \'' . 'APPROVE' . '\')"><i class="fa fa-check text-success mr-3"></i></a>';
                $h .= '<a href="#" class="text-center" title="Reject" onclick="approval(\'' . route('user.approval.approval', ['id' => $q->id, 'status' => 'reject']) . '\', \'' . 'REJECT' . '\')"><i class="fa fa-x text-danger mr-3"></i></a>';
                return $h;
            })
            ->rawColumns(['action', 'gambar'])
            ->make(true);
    }

    public function approval($id, $status)
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
