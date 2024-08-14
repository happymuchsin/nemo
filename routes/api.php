<?php

use App\Http\Controllers\Api\ApprovalController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CardController;
use App\Http\Controllers\Api\ChangeNeedleController;
use App\Http\Controllers\Api\NeedleController;
use App\Http\Controllers\Api\SpinnerController;
use App\Http\Controllers\Api\VersionController;
use App\Http\Resources\ApiResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/testcon', function () {
    return new ApiResource(200, 'connect', '');
});

Route::post('/version', [VersionController::class, 'version']);
Route::get('/update', [VersionController::class, 'update']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('/user')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
    });

    Route::prefix('/card')->group(function () {
        Route::post('person', [CardController::class, 'person']);
        Route::post('box', [CardController::class, 'box']);
    });

    Route::post('spinner', [SpinnerController::class, 'spinner']);

    Route::prefix('/needle')->group(function () {
        Route::post('save', [NeedleController::class, 'save']);
        Route::post('approval', [NeedleController::class, 'approval']);
        Route::post('stock', [NeedleController::class, 'stock']);
    });

    Route::prefix('/approval')->group(function () {
        Route::post('data', [ApprovalController::class, 'data']);
    });
});
