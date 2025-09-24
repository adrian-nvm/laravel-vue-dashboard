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

Route::get('/chart/slideshow', [\App\Http\Controllers\Chart\ChartSlideshowController::class, 'showSlideshow'])->name('chart.slideshow');

Route::get('/login', function () {
    return view('welcome');
})->name('login');

Route::get('/{any?}', function () {
    return view('welcome');
})->where('any', '.*');
