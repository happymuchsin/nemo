<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use Exception;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    protected $username;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
        $this->username = $this->findUsername();
    }

    public function findUsername()
    {
        $login = request()->input('login');
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$fieldType => $login]);

        return $fieldType;
    }

    public function username()
    {
        return $this->username;
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'login' => 'required',
            'password' => 'required',
        ]);

        $credentials = $this->credentials($request);

        try {
            if (Auth::attempt([$this->username() => $request->input($this->username()), 'password' => $credentials['password']])) {
                $user = Auth::getLastAttempted();
                Auth::login($user);

                HelperController::activityLog('LOGIN', 'users', 'login', $request->ip(), $request->userAgent(), null, null, $user->username);

                return redirect()->route('user.dashboard');
            }

            if (isset($credentials['username'])) {
                Session::flash('error', 'Username or Password Wrong !!!');
                return redirect()->route('login');
            } else if (isset($credentials['email'])) {
                Session::flash('error', 'Email or Password Wrong !!!');
                return redirect()->route('login');
            }
        } catch (Exception $e) {
            Session::flash('error', 'Login Failed');
            return redirect()->route('login');
        }
    }
}
