<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'admin_profile';
        $title = 'ADMIN PROFILE';

        HelperController::activityLog('OPEN ADMIN PROFILE', '', 'read', $request->ip(), $request->userAgent());

        return view('Admin.Profile.index', compact('title', 'page'));
    }

    public function change(Request $request)
    {
        $password = $request->password;

        User::where('id', Auth::user()->id)->update([
            'password' => bcrypt($password),
            'updated_by' => Auth::user()->username,
            'updated_at' => Carbon::now(),
        ]);

        HelperController::activityLog("CHANGE PASSWORD", 'users', 'update', $request->ip(), $request->userAgent(), json_encode([
            'id' => Auth::user()->id,
            'password' => $password,
            'updated_by' => Auth::user()->username,
            'updated_at' => Carbon::now(),
        ]), Auth::user()->id);

        return response()->json('Change Password Successfully', 200);
    }
}
