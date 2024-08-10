<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
                    Auth::login($u, $request->has('remember'));

                    $reff = '';
                    $area = '';
                    $area_id = '';
                    $lokasi = '';
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
                    }

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

        return new ApiResource(200, 'Logout Success', []);
    }
}
