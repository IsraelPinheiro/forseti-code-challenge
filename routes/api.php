<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\TagController;
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

Route::middleware('json.response')->group(function () {
    //Routes to check server connectivity
    Route::get('/', function () {
        return redirect()->route('online');
    });
    Route::get('/online', [ApplicationController::class, 'check'])->name('online');

    //News related routes
    Route::prefix('news')->group(function () {
        Route::get('/', [NewsController::class, 'index']);
        Route::get('/csv', [NewsController::class, 'exportToCsv']);
        Route::get('/{uuid}', [NewsController::class, 'show']);
        Route::get('/{uuid}/csv', [NewsController::class, 'exportOneToCsv']);
        Route::delete('/{uuid}', [NewsController::class, 'destroy']);
    });

    //Tags related routes
    Route::prefix('tags')->group(function () {
        Route::get('/', [TagController::class, 'index']);
        Route::post('/', [TagController::class, 'store']);
        Route::get('/{uuid}/news', [TagController::class, 'news']);
        Route::delete('/{uuid}', [TagController::class, 'destroy']);
    });
});