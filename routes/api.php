<?php

use App\Http\Controllers\ChannelController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome to Soapbox backend challenge API'
    ], 200);
});

Route::group(['prefix' => 'workspaces'], function () {
    Route::post('create', [WorkspaceController::class, 'create']);
    Route::post('login', [WorkspaceController::class, 'login']);
    Route::post('logout', [WorkspaceController::class, 'logout']);
    Route::get('accept-invite', [WorkspaceController::class, 'acceptInvite']);
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/', [WorkspaceController::class, 'index']);
        Route::group(['prefix' => 'users'], function () {
            Route::post('create', [WorkspaceController::class, 'createUsers']);
            Route::post('invite', [WorkspaceController::class, 'inviteUsers']);
        });
        Route::group(['prefix' => 'channels'], function () {
            Route::get('/', [ChannelController::class, 'index']);
            Route::post('create', [ChannelController::class, 'create']);
            Route::group(['prefix' => 'users'], function () {
                Route::post('create', [ChannelController::class, 'createUsers']);
                Route::get('get-user-channels', [ChannelController::class, 'getUserChannels']);
            });
            Route::group(['prefix' => 'messages'], function () {
                Route::post('create', [MessageController::class, 'create']);
                Route::get('', [MessageController::class, 'index']);
            });
        });
    });
});


