<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\MasterNeedle;
use App\Models\MasterStatus;
use App\Models\Needle;
use App\Models\NeedleDetail;
use App\Models\Stock;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use stdClass;

class DashboardController extends Controller
{
    public function __construct()
    {

        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'user_dashboard';
        $title = 'USER DASHBOARD';

        HelperController::activityLog('OPEN USER DASHBOARD', null, 'read', $request->ip(), $request->userAgent());

        $user_dashboard = 'active';

        return view('User.Dashboard.index', compact('title', 'page', 'user_dashboard'));
    }

    public function data(Request $request)
    {
        // Single Needle = DB X 1
        // Double Needle = DP X 5
        // Obras = DC X 27
        // Kansai = UO X 113 & DV X 57

        $bulan = date('n');
        $tipe = $request->tipe;

        $idSingleNeedle = [];
        $idDoubleNeedle = [];
        $idObras = [];
        $idKansai = [];
        $singleNeedle = MasterNeedle::where('tipe', 'like', '%DB X 1%')->get();
        $doubleNeedle = MasterNeedle::where('tipe', 'like', '%DP X 5%')->get();
        $obras = MasterNeedle::where('tipe', 'like', '%DC X 27%')->get();
        $kansai = MasterNeedle::where('tipe', 'like', '%UO X 113%')->orWhere('tipe', 'like', '%DV X 57%')->get();
        foreach ($singleNeedle as $sn) {
            $idSingleNeedle[] = $sn->id;
        }
        foreach ($doubleNeedle as $dn) {
            $idDoubleNeedle[] = $dn->id;
        }
        foreach ($obras as $o) {
            $idObras[] = $o->id;
        }
        foreach ($kansai as $k) {
            $idKansai[] = $k->id;
        }

        if ($tipe == 'outstanding') {
            $h = '';
            $notif = Auth::user()->unreadNotifications;
            foreach ($notif as $n) {
                $h .= '<li>
                    <a href="' . $n->data['link'] . '" style="color:black;font-weight:bold;">
                        ' . $n->data['title'] . '
                    </a>
                </li>';
                break;
            }
            $stock = Stock::join('master_areas as ma', 'ma.id', 'stocks.master_area_id')
                ->join('master_counters as mc', 'mc.id', 'stocks.master_counter_id')
                ->join('master_boxes as mb', 'mb.id', 'stocks.master_box_id')
                ->join('master_needles as mn', 'mn.id', 'stocks.master_needle_id')
                ->selectRaw('stocks.id as id, ma.name as area, mc.name as counter, mb.name as box, mn.brand as brand, mn.tipe as tipe, mn.size as size, mn.code as code, mn.machine as machine, mn.min_stock as min_stock, stocks.`in` as `in`, stocks.`out` as `out`, stocks.is_clear as is_clear')
                ->where('is_clear', 'not')
                ->get();
            $m = 0;
            foreach ($stock as $s) {
                $last = $s->in - $s->out;
                if ($last < $s->min_stock) {
                    $m = 1;
                    break;
                }
            }
            if ($m == 1) {
                $h .= '<li style="text-align:left;text-indent:-20px;padding-left:20px;">
                    <a href="' . route('user.stock') . '" style="color:black;font-weight:bold;">
                        There is stock that is below the minimum limit
                    </a>
                </li>';
            }
            return response()->json($h, 200);
        } else if ($tipe == 'box') {
            $inSingleNeedle = Stock::whereIn('master_needle_id', $idSingleNeedle)->whereMonth('created_at', $bulan)->sum('in');
            $outSingleNeedle = Stock::whereIn('master_needle_id', $idSingleNeedle)->whereMonth('created_at', $bulan)->sum('out');
            $inDoubleNeedle = Stock::whereIn('master_needle_id', $idDoubleNeedle)->whereMonth('created_at', $bulan)->sum('in');
            $outDoubleNeedle = Stock::whereIn('master_needle_id', $idDoubleNeedle)->whereMonth('created_at', $bulan)->sum('out');
            $inObras = Stock::whereIn('master_needle_id', $idObras)->whereMonth('created_at', $bulan)->sum('in');
            $outObras = Stock::whereIn('master_needle_id', $idObras)->whereMonth('created_at', $bulan)->sum('out');
            $inKansai = Stock::whereIn('master_needle_id', $idKansai)->whereMonth('created_at', $bulan)->sum('in');
            $outKansai = Stock::whereIn('master_needle_id', $idKansai)->whereMonth('created_at', $bulan)->sum('out');

            $ava_single_needle = $inSingleNeedle - $outSingleNeedle;
            $ava_double_needle = $inDoubleNeedle - $outDoubleNeedle;
            $ava_obras = $inObras - $outObras;
            $ava_kansai = $inKansai - $outKansai;
            $rep_single_needle = $outSingleNeedle;
            $rep_double_needle = $outDoubleNeedle;
            $rep_obras = $outObras;
            $rep_kansai = $outKansai;

            return response()->json([
                'ava_single_needle' => $ava_single_needle,
                'ava_obras' => $ava_obras,
                'ava_double_needle' => $ava_double_needle,
                'ava_kansai' => $ava_kansai,
                'rep_single_needle' => $rep_single_needle,
                'rep_obras' => $rep_obras,
                'rep_double_needle' => $rep_double_needle,
                'rep_kansai' => $rep_kansai,
            ], 200);
        } else if ($tipe == 'chart') {
            $data = [];
            for ($i = 1; $i <= 12; $i++) {
                $inSingleNeedle = Stock::whereIn('master_needle_id', $idSingleNeedle)->whereMonth('created_at', $i)->sum('in');
                $outSingleNeedle = Stock::whereIn('master_needle_id', $idSingleNeedle)->whereMonth('created_at', $i)->sum('out');
                $inDoubleNeedle = Stock::whereIn('master_needle_id', $idDoubleNeedle)->whereMonth('created_at', $i)->sum('in');
                $outDoubleNeedle = Stock::whereIn('master_needle_id', $idDoubleNeedle)->whereMonth('created_at', $i)->sum('out');
                $inObras = Stock::whereIn('master_needle_id', $idObras)->whereMonth('created_at', $i)->sum('in');
                $outObras = Stock::whereIn('master_needle_id', $idObras)->whereMonth('created_at', $i)->sum('out');
                $inKansai = Stock::whereIn('master_needle_id', $idKansai)->whereMonth('created_at', $i)->sum('in');
                $outKansai = Stock::whereIn('master_needle_id', $idKansai)->whereMonth('created_at', $i)->sum('out');

                $d = new stdClass;
                $d->date = date('M', strtotime(date('Y-' . $i . '-01')));
                $d->ava_single_needle = $inSingleNeedle - $outSingleNeedle;
                $d->ava_double_needle = $inDoubleNeedle - $outDoubleNeedle;
                $d->ava_obras = $inObras - $outObras;
                $d->ava_kansai = $inKansai - $outKansai;
                $d->rep_single_needle = $outSingleNeedle;
                $d->rep_double_needle = $outDoubleNeedle;
                $d->rep_obras = $outObras;
                $d->rep_kansai = $outKansai;
                $data[$i] = $d;
            }

            return response()->json($data, 200);
        }
    }
}
