<?php

namespace App\Http\Controllers\Chart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Chart\QrisChartController;
use App\Http\Controllers\Chart\BillerChartController;
use App\Http\Controllers\Chart\DebitChartController;

class ChartSlideshowController extends Controller
{
    public function showSlideshow(Request $request)
    {
        // Create instances of chart controllers
        $qrisChartController = new QrisChartController();
        $billerChartController = new BillerChartController();
        $debitChartController = new DebitChartController();
        $bifastChartController = new BifastChartController();
        $rtolChartController = new RtolChartController();

        // Call the getChartData method for each controller
        $qrisResponse = $qrisChartController->getChartData($request);
        $billerResponse = $billerChartController->getChartData($request);
        $debitResponse = $debitChartController->getChartData($request);
        $bifastResponse = $bifastChartController->getChartData($request);
        $rtolResponse = $rtolChartController->getChartData($request);

        // Get the data from the responses
        $qrisChartData = $qrisResponse->getData(true);
        $billerChartData = $billerResponse->getData(true);
        $debitChartData = $debitResponse->getData(true);
        $bifastChartData = $bifastResponse->getData(true);
        $rtolChartData = $rtolResponse->getData(true);

        // Combine all chart data into a single array
        $combinedChartData = [
            'qris' => $qrisChartData,
            'biller' => $billerChartData,
            'debit' => $debitChartData,
            'bifast' => $bifastChartData,
            'rtol' => $rtolChartData,
        ];

        Log::info('SlideShow Chart Data:', $combinedChartData);

        // Pass the combined data to the view
        return view('slideshowchart', ['chartData' => $combinedChartData]);
    }
}
