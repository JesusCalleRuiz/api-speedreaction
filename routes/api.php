<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\TimeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the TimeServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::post('/register', [AuthController::class, 'register']);
//Route::post('/login', [AuthController::class, 'login']);


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/times', [TimeController::class, 'store']);
    Route::get('/times/{id}', [TimeController::class, 'show']);
    Route::get('/times', [TimeController::class, 'index']);
    Route::get('/user/times', [TimeController::class, 'showUserTimes']);
});

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

