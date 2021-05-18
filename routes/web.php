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
Route::get('/person',[PageController::class, 'person']);
Route::get('/data-person',[PageController::class, 'dataPerson'])->name('data.person');
Route::get('/get-daily-reports-global', [SheetController::class, 'DailyReport']);