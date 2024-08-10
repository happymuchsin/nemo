<?php

namespace App\Http\Controllers\Admin\Tools;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ToolsProfileController extends Controller
{
    public function __construct()
    {

        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'admin_tools_profile';
        $title = 'ADMIN TOOLS PROFILE';

        $admin_tools = 'menu-open';

        return view('Admin.Tools.Profile.index', compact('title', 'page', 'admin_tools'));
    }

    public function change(Request $request)
    {
        $password = $request->password;

        User::where('id', Auth::user()->id)->update([
            'password' => bcrypt($password),
            'updated_by' => Auth::user()->username,
            'updated_at' => Carbon::now(),
        ]);

        return response()->json('Change Password Successfully', 200);
    }
}
