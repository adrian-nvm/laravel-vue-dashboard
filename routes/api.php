<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;
use App\Http\Controllers\Form\QrisDataController;
use App\Http\Controllers\Form\BillerDataController;
use App\Http\Controllers\Form\DebitDataController;
use App\Http\Controllers\Chart\ChartSlideshowController;
use App\Http\Controllers\Chart\QrisChartController;
use App\Http\Controllers\Chart\BillerChartController;
use App\Http\Controllers\Chart\DebitChartController;

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

Route::post('login', [Api\AuthController::class, 'login']);
Route::post('register', [Api\RegisterController::class, 'register']);
Route::post('forgot', [Api\ForgotController::class, 'forgot']);
Route::post('reset', [Api\ForgotController::class, 'reset']);
Route::get('email/resend/{user}', [Api\VerifyController::class, 'resend'])->name('verification.resend');
Route::get('email/verify/{id}', [Api\VerifyController::class, 'verify'])->name('verification.verify');; // Make sure to keep this as your route name

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('user', [Api\AuthController::class, 'user']);
    Route::post('qris/line-data', [QrisDataController::class, 'storeLineData']);
    Route::post('qris/hana-data', [QrisDataController::class, 'storeHanaData']);
});

Route::get('qris/line-data', [QrisDataController::class, 'getQrisLineData'])->middleware('auth:api');
Route::get('qris/hana-data', [QrisDataController::class, 'getQrisHanaData'])->middleware('auth:api');
Route::post('qris/upload', [QrisDataController::class, 'upload'])->middleware('auth:api');

// Biller Data Routes
Route::get('biller/line-data', [BillerDataController::class, 'getBillerLineData'])->middleware('auth:api');
Route::get('biller/hana-data', [BillerDataController::class, 'getBillerHanaData'])->middleware('auth:api');
Route::post('biller/upload', [BillerDataController::class, 'upload'])->middleware('auth:api');

// Debit Data Routes
Route::get('debit/line-data', [DebitDataController::class, 'getDebitLineData'])->middleware('auth:api');
Route::get('debit/hana-data', [DebitDataController::class, 'getDebitHanaData'])->middleware('auth:api');
Route::post('debit/upload', [DebitDataController::class, 'upload'])->middleware('auth:api');

// Chart Slideshow Route
Route::get('charts/slideshow', [ChartSlideshowController::class, 'getChartData'])->middleware('auth:api');

// Combined Chart Route
Route::post('combined-chart', [App\Http\Controllers\Chart\CombinedChartController::class, 'fetchData'])->middleware('auth:api');

// Chart Data Routes
Route::get('chart/qris-line', [QrisChartController::class, 'showLineChart'])->middleware('auth:api');
Route::get('chart/qris-hana', [QrisChartController::class, 'showHanaChart'])->middleware('auth:api');
Route::get('chart/biller-line', [BillerChartController::class, 'showLineChart'])->middleware('auth:api');
Route::get('chart/biller-hana', [BillerChartController::class, 'showHanaChart'])->middleware('auth:api');
Route::get('chart/debit-line', [DebitChartController::class, 'showLineChart'])->middleware('auth:api');
Route::get('chart/debit-hana', [DebitChartController::class, 'showHanaChart'])->middleware('auth:api');
