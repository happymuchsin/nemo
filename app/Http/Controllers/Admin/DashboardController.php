<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Http\Controllers\HomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {

        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'admin_dashboard';
        $title = 'ADMIN DASHBOARD';

        HelperController::activityLog('OPEN ADMIN', '', 'read', $request->ip(), $request->userAgent());

        $admin_dashboard = 'active';

        return view('Admin.Dashboard.index', compact('title', 'page', 'admin_dashboard'));
    }
}
