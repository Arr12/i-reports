<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PutDatabaseController;
use App\Http\Controllers\SheetController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('/daily-report')->name('api.dailyReport.')->group(function(){
    Route::get('/get-spreadsheet', [SheetController::class, 'GetDailyReport'])->name('get');
});
Route::prefix('/team-monitoring')->name('api.setTeamMonitoring')->group(function(){
    Route::get('/global', [SheetController::class, 'TeamMonitoringGlobal'])->name('setGlobal');
    Route::get('/indo', [SheetController::class, 'TeamMonitoringIndo'])->name('setIndo');
});
Route::prefix('/all-team-report')->name('api.setAllTeam.')->group(function(){
    Route::get('/monthly', [SheetController::class, 'setAllTeamReport'])->name('monthly');
    Route::get('/weekly', [SheetController::class, 'setAllTeamReportWeekly'])->name('weekly');
});