<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\DailyClosing;
use App\Models\MasterNeedle;
use App\Models\Stock;
use App\Models\Warehouse;
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
        $tahun = date('Y');
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
            $x = 0;
            $master_needle = MasterNeedle::orderBy('tipe')->orderBy('size')->get();
            foreach ($master_needle as $m) {
                $in = Warehouse::where('master_needle_id', $m->id)->sum('in');
                $out = Warehouse::where('master_needle_id', $m->id)->sum('out');

                if ($in - $out <= $m->min_stock) {
                    $x = 1;
                    break;
                }
            }
            if ($x == 1) {
                $h .= '<li style="text-align:left;text-indent:-20px;padding-left:20px;">
                    <a href="' . route('user.warehouse') . '" style="color:black;font-weight:bold;">
                        There is stock that is below the minimum limit
                    </a>
                </li>';
            }
            return response()->json($h, 200);
        } else if ($tipe == 'box') {
            $daily_closing = DailyClosing::whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)->get();
            $collect_daily_closing = collect($daily_closing);

            $ava_single_needle = $collect_daily_closing->whereIn('master_needle_id', $idSingleNeedle)->max('opening') ?? 0;
            $ava_double_needle = $collect_daily_closing->whereIn('master_needle_id', $idDoubleNeedle)->max('opening') ?? 0;
            $ava_obras = $collect_daily_closing->whereIn('master_needle_id', $idObras)->max('opening') ?? 0;
            $ava_kansai = $collect_daily_closing->whereIn('master_needle_id', $idKansai)->max('opening') ?? 0;
            $rep_single_needle = $collect_daily_closing->whereIn('master_needle_id', $idSingleNeedle)->max('out') ?? 0;
            $rep_double_needle = $collect_daily_closing->whereIn('master_needle_id', $idDoubleNeedle)->max('out') ?? 0;
            $rep_obras = $collect_daily_closing->whereIn('master_needle_id', $idObras)->max('out') ?? 0;
            $rep_kansai = $collect_daily_closing->whereIn('master_needle_id', $idKansai)->max('out') ?? 0;

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
                $daily_closing = DailyClosing::whereYear('tanggal', $tahun)->whereMonth('tanggal', $i)->get();
                $collect_daily_closing = collect($daily_closing);

                $d = new stdClass;
                $d->date = date('M', strtotime(date('Y-' . $i . '-01')));
                $d->ava_single_needle = $collect_daily_closing->whereIn('master_needle_id', $idSingleNeedle)->max('opening') ?? 0;
                $d->ava_double_needle = $collect_daily_closing->whereIn('master_needle_id', $idDoubleNeedle)->max('opening') ?? 0;
                $d->ava_obras = $collect_daily_closing->whereIn('master_needle_id', $idObras)->max('opening') ?? 0;
                $d->ava_kansai = $collect_daily_closing->whereIn('master_needle_id', $idKansai)->max('opening') ?? 0;
                $d->rep_single_needle = $collect_daily_closing->whereIn('master_needle_id', $idSingleNeedle)->max('out') ?? 0;
                $d->rep_double_needle = $collect_daily_closing->whereIn('master_needle_id', $idDoubleNeedle)->max('out') ?? 0;
                $d->rep_obras = $collect_daily_closing->whereIn('master_needle_id', $idObras)->max('out') ?? 0;
                $d->rep_kansai = $collect_daily_closing->whereIn('master_needle_id', $idKansai)->max('out') ?? 0;
                $data[$i] = $d;
            }

            return response()->json($data, 200);
        }
    }
}
