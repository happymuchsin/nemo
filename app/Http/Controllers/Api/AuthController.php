<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Http\Resources\ApiResource;
use App\Models\MasterCounter;
use App\Models\MasterLine;
use App\Models\MasterPlacement;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use AuthenticatesUsers;

    protected $username;

    public function __construct()
    {
        $this->username = $this->findUsername();
    }

    public function findUsername()
    {
        $login = request()->input('username');
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
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return new ApiResource(422, $validator->errors(), '');
            }

            $credentials = $this->credentials($request);

            $user = User::with(['division', 'position'])->where('username', $credentials['username'])->first();

            if ($user) {
                if (Auth::validate($credentials)) {
                    $u = Auth::getLastAttempted();

                    $reff = '';
                    $area = '';
                    $area_id = '';
                    $lokasi = '';
                    $lokasi_id = '';
                    $placement = MasterPlacement::where('user_id', $user->id)->first();
                    if ($placement) {
                        if ($placement->reff == 'counter') {
                            $r = MasterCounter::with(['area'])->where('id', $placement->location_id)->first();
                            $reff = $placement->reff;
                            $area = $r->area->name;
                            $area_id = $r->area->id;
                            $lokasi = $r->name;
                            $lokasi_id = $r->id;
                        } else if ($placement->reff == 'line') {
                            $r = MasterLine::with(['area'])->where('id', $placement->location_id)->first();
                            $reff = $placement->reff;
                            $area = $r->area->name;
                            $area_id = $r->area->id;
                            $lokasi = $r->name;
                            $lokasi_id = $r->id;
                        }

                        if ($placement->reff == 'counter') {
                            Auth::login($u, $request->has('remember'));
                            HelperController::activityLog('ANDROID LOGIN', 'users', 'login', $request->ip(), $request->userAgent(), json_encode($request->all), null, $u->username);

                            return new ApiResource(200, 'success', [
                                'token' => $user->createToken('authToken')->plainTextToken,
                                'tokenType' => 'Bearer',
                                'user' => $user,
                                'role' => $user->getRoleNames(),
                                'reff' => $reff,
                                'area' => $area,
                                'area_id' => $area_id,
                                'lokasi' => $lokasi,
                                'lokasi_id' => $lokasi_id,
                            ]);
                        } else {
                            return new ApiResource(422, 'Placement counter only', '');
                        }
                    } else {
                        return new ApiResource(422, 'Placement not found', '');
                    }
                }
            } else {
                return new ApiResource(422, 'User not found', '');
            }
        } catch (Exception $e) {
            // return new ApiResource(422, 'Login Failed', '');
            return new ApiResource(422, $e->getMessage(), '');
        }
    }

    public function logout(Request $request)
    {
        //current token
        // $request->user()->currentAccessToken()->delete();

        // all token
        foreach ($request->user()->tokens as $token) {
            $token->delete();
        }

        // Auth::user()->tokens->delete();

        HelperController::activityLog("ANDROID LOGOUT", 'users', 'logout', $request->ip(), $request->userAgent(), null, null, $request->user()->username);

        return new ApiResource(200, 'Logout Success', []);
    }
}
