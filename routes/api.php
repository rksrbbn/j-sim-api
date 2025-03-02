<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LogActivitiesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/check-token', [AuthController::class, 'checkToken']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::group(['prefix' => 'user'], function () {
        Route::get('/list', [UserController::class, 'getUser']);
        Route::get('/profile', [UserController::class, 'getUserProfile']);
        Route::post('/update', [UserController::class, 'updateUser']);
        Route::get('/detail', [UserController::class, 'getUserDetail']);
        Route::get('/profile-picture', [UserController::class, 'getUserPicture']);
        Route::get('/logs', [LogActivitiesController::class, 'getUserLogs']);
    });
    Route::group(['prefix' => 'action'], function () {
        Route::post('/work', [UserController::class, 'actionWork']);
        // Route::get('/list', [UserController::class, 'getUser']);
        // Route::get('/profile', [UserController::class, 'getUserProfile']);
        // Route::get('/detail', [UserController::class, 'getUserDetail']);
    });
    Route::group(['prefix' => 'logs'], function () {
        Route::post('/save', [LogActivitiesController::class, 'saveLog']);
        // Route::get('/list', [UserController::class, 'getUser']);
        // Route::get('/profile', [UserController::class, 'getUserProfile']);
        // Route::get('/detail', [UserController::class, 'getUserDetail']);
    });
});

