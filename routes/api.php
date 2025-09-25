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
use App\Http\Controllers\Form\BifastDataController;
use App\Http\Controllers\Chart\BifastChartController;
use App\Http\Controllers\Form\RtolDataController;
use App\Http\Controllers\Chart\RtolChartController;

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
    // QRIS Data Routes
    Route::post('qris/store-qris-line-data', [QrisDataController::class, 'storeLineData']);
    Route::post('qris/store-qris-hana-data', [QrisDataController::class, 'storeHanaData']);
    Route::get('qris/qris-line-data', [QrisDataController::class, 'getQrisLineData']);
    Route::get('qris/qris-hana-data', [QrisDataController::class, 'getQrisHanaData']);
    Route::post('qris/upload', [QrisDataController::class, 'upload']);

    // Biller Data Routes
    Route::post('biller/store-biller-line-data', [BillerDataController::class, 'storeLineData']);
    Route::post('biller/store-biller-hana-data', [BillerDataController::class, 'storeHanaData']);
    Route::get('biller/biller-line-data', [BillerDataController::class, 'getBillerLineData']);
    Route::get('biller/biller-hana-data', [BillerDataController::class, 'getBillerHanaData']);
    Route::post('biller/upload', [BillerDataController::class, 'upload']);

    // Debit Data Routes
    Route::post('debit/store-debit-line-data', [DebitDataController::class, 'storeLineData']);
    Route::post('debit/store-debit-hana-data', [DebitDataController::class, 'storeHanaData']);
    Route::get('debit/debit-line-data', [DebitDataController::class, 'getDebitLineData']);
    Route::get('debit/debit-hana-data', [DebitDataController::class, 'getDebitHanaData']);
    Route::post('debit/upload', [DebitDataController::class, 'upload']);

    // Bifast Data Routes
    Route::post('bifast/store-bifast-line-data', [BifastDataController::class, 'storeLineData']);
    Route::post('bifast/store-bifast-hana-data', [BifastDataController::class, 'storeHanaData']);
    Route::get('bifast/bifast-line-data', [BifastDataController::class, 'getBifastLineData']);
    Route::get('bifast/bifast-hana-data', [BifastDataController::class, 'getBifastHanaData']);
    Route::post('bifast/upload', [BifastDataController::class, 'upload']);

    // RTOL Data Routes
    Route::post('rtol/store-rtol-line-data', [RtolDataController::class, 'storeLineData']);
    Route::post('rtol/store-rtol-hana-data', [RtolDataController::class, 'storeHanaData']);
    Route::get('rtol/rtol-line-data', [RtolDataController::class, 'getRtolLineData']);
    Route::get('rtol/rtol-hana-data', [RtolDataController::class, 'getRtolHanaData']);
    Route::post('rtol/upload', [RtolDataController::class, 'upload']);

    // Combined Chart Route
    Route::post('combined-chart', [App\Http\Controllers\Chart\CombinedChartController::class, 'fetchData']);

    // Chart Slideshow Route
    Route::get('charts/slideshow', [ChartSlideshowController::class, 'getChartData']);

    // Chart Data Routes
    Route::get('chart/qris-line', [QrisChartController::class, 'showLineChart']);
    Route::get('chart/qris-hana', [QrisChartController::class, 'showHanaChart']);
    Route::get('chart/biller-line', [BillerChartController::class, 'showLineChart']);
    Route::get('chart/biller-hana', [BillerChartController::class, 'showHanaChart']);
    Route::get('chart/debit-line', [DebitChartController::class, 'showLineChart']);
    Route::get('chart/debit-hana', [DebitChartController::class, 'showHanaChart']);
    Route::get('chart/bifast-line', [BifastChartController::class, 'showLineChart']);
    Route::get('chart/bifast-hana', [BifastChartController::class, 'showHanaChart']);
    Route::get('chart/rtol-line', [RtolChartController::class, 'showLineChart']);
    Route::get('chart/rtol-hana', [RtolChartController::class, 'showHanaChart']);

});
