<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;
use Symfony\Component\VarDumper\Cloner\Data;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', [PageController::class, 'index']);
Route::prefix('/daily-report-global/report')->name('daily-report-global.')->group(function(){
    Route::get('ames',[PageController::class, 'DailyReportAmes'])->name('ames');
    Route::get('ames-data',[PageController::class, 'getDailyReportAmes'])->name('ames.data');
    Route::get('annas',[PageController::class, 'DailyReportAnnas'])->name('annas');
    Route::get('annas-data',[PageController::class, 'getDailyReportAnnas'])->name('annas.data');
    Route::get('carols',[PageController::class, 'DailyReportCarols'])->name('carols');
    Route::get('carols-data',[PageController::class, 'getDailyReportCarols'])->name('carols.data');
    Route::get('erics',[PageController::class, 'DailyReportErics'])->name('erics');
    Route::get('erics-data',[PageController::class, 'getDailyReportErics'])->name('erics.data');
    Route::get('ichas',[PageController::class, 'DailyReportIchas'])->name('ichas');
    Route::get('ichas-data',[PageController::class, 'getDailyReportIchas'])->name('ichas.data');
    Route::get('lilies',[PageController::class, 'DailyReportLilies'])->name('lilies');
    Route::get('lilies-data',[PageController::class, 'getDailyReportLilies'])->name('lilies.data');
    Route::get('maydewis',[PageController::class, 'DailyReportMayDewis'])->name('maydewis');
    Route::get('maydewis-data',[PageController::class, 'getDailyReportMayDewis'])->name('maydewis.data');
    Route::get('ranis',[PageController::class, 'DailyReportRanis'])->name('ranis');
    Route::get('ranis-data',[PageController::class, 'getDailyReportRanis'])->name('ranis.data');
});
Route::prefix('/daily-report-indo/report')->name('daily-report-indo.')->group(function(){
    Route::get('icha-nurs',[PageController::class, 'DailyReportIndoIchaNurs'])->name('icha-nurs');
    Route::get('icha-nurs-data',[PageController::class, 'getDailyReportIndoIchaNurs'])->name('icha-nurs.data');
    Route::get('irels',[PageController::class, 'DailyReportIndoIrels'])->name('irels');
    Route::get('irels-data',[PageController::class, 'getDailyReportIndoIrels'])->name('irels.data');
});
Route::prefix('/team-monitoring')->name('team-monitoring.')->group(function(){
    Route::get('global',[PageController::class, 'GlobalTeamMonitoring'])->name('global');
    Route::get('global-data',[PageController::class, 'getGlobalTeamMonitoring'])->name('global.data');
    Route::get('indo',[PageController::class, 'IndoTeamMonitoring'])->name('indo');
    Route::get('indo-data',[PageController::class, 'getIndoTeamMonitoring'])->name('indo.data');
});
Route::prefix('/all-report')->name('all-report.')->group(function(){
    Route::get('get-date',[PageController::class, 'GetDateWeekly'])->name('date-weekly');
    Route::get('weekly',[PageController::class, 'WeeklyReport'])->name('weekly');
    Route::get('weekly-data',[PageController::class, 'getWeeklyReport'])->name('weekly.data');
    Route::get('monthly',[PageController::class, 'MonthlyReport'])->name('monthly');
    Route::get('monthly-data',[PageController::class, 'getMonthlyReport'])->name('monthly.data');
});