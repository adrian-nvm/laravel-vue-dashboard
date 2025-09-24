<?php

use App\Http\Controllers\Chart\BillerChartController;
use App\Http\Controllers\Chart\ChartSlideshowController;
use App\Http\Controllers\Chart\DebitChartController;
use App\Http\Controllers\Chart\QrisChartController;
use Illuminate\Support\Facades\Route;

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

Route::get('/chart/qris-line', [QrisChartController::class, 'showLineChart'])->name('qris.qrislinechart');
Route::get('/chart/qris-hana', [QrisChartController::class, 'showHanaChart'])->name('qris.qrishanachart');

Route::get('/chart/biller-line', [BillerChartController::class, 'showLineChart'])->name('biller.billerlinechart');
Route::get('/chart/biller-hana', [BillerChartController::class, 'showHanaChart'])->name('biller.billerhanachart');

Route::get('/chart/debit-line', [DebitChartController::class, 'showLineChart'])->name('debit.debitlinechart');
Route::get('/chart/debit-hana', [DebitChartController::class, 'showHanaChart'])->name('debit.debithanachart');

Route::get('/chart/slideshow', [ChartSlideshowController::class, 'showSlideshow'])->name('chart.slideshow');


Route::get('/{any?}', function () {
    return view('welcome');
})->where('any', '.*');
