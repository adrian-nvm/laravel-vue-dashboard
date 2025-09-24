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

        // Call the getChartData method for each controller
        $qrisResponse = $qrisChartController->getChartData($request);
        $billerResponse = $billerChartController->getChartData($request);
        $debitResponse = $debitChartController->getChartData($request);

        // Get the data from the responses
        $qrisChartData = json_decode($qrisResponse->getContent(), true);
        $billerChartData = json_decode($billerResponse->getContent(), true);
        $debitChartData = json_decode($debitResponse->getContent(), true);

        // Combine all chart data into a single array
        $combinedChartData = [
            'qris' => $qrisChartData,
            'biller' => $billerChartData,
            'debit' => $debitChartData,
        ];

        Log::info('SlideShow Chart Data:', $combinedChartData);

        // Pass the combined data to the view
        return view('slideshowchart', ['chartData' => $combinedChartData]);
    }
}
