<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class NotifController extends Controller
{
    public function notif()
    {
        $notif = Auth::user()->unreadNotifications;
        $jml = count($notif);

        return response()->json(['jml' => $jml, 'notif' => $notif], 200);
    }

    public function notif_clicked(Request $request)
    {
        $tipe = $request->tipe;
        $url = url()->previous();
        $host = url('/');
        $current = explode($host . '/', $url);
        $user = Auth::user()->unreadNotifications;
        if ($tipe == 'approval') {
            Auth::user()->unreadNotifications->markAsRead();
            // if ($current[1] != 'user/approval') {
            return redirect()->route('user.approval');
            // }
        }
    }
}
