<?php

namespace App\Http\Controllers\Chart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CombinedChartController extends Controller
{
    public function fetchData(Request $request)
    {
        $chartTypes = $request->input('charts', []);
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');
        $datasets = [];

        $startDate = $startMonth ? Carbon::parse($startMonth)->startOfMonth()->toDateString() : null;
        $endDate = $endMonth ? Carbon::parse($endMonth)->endOfMonth()->toDateString() : null;

        $qrisQuery = DB::table('monthly_qris_trx')->select('START_DT', 'QRIS_TRX_AMT', 'QRIS_TRX_FREQ', 'QRIS_MYHANA_TRX_AMT', 'QRIS_MYHANA_TRX_FREQ', 'QRIS_UNIQUE_CIF_QTY', 'QRIS_MYHANA_UNIQUE_CIF_QTY');
        $debitQuery = DB::table('monthly_debit_trx')->select('START_DT', 'DEBIT_TRX_AMT', 'DEBIT_TRX_FREQ', 'DEBIT_MYHANA_TRX_AMT', 'DEBIT_MYHANA_TRX_FREQ', 'DEBIT_UNIQUE_CIF_QTY', 'DEBIT_MYHANA_UNIQUE_CIF_QTY');
        $billerQuery = DB::table('monthly_bill_trx')->select('START_DT', 'BILL_TRX_AMT', 'BILL_TRX_FREQ', 'BILL_MYHANA_TRX_AMT', 'BILL_MYHANA_TRX_FREQ', 'BILL_UNIQUE_CIF_QTY', 'BILL_MYHANA_UNIQUE_CIF_QTY');
        $bifastQuery = DB::table('monthly_bifast_trx')->select('START_DT', 'BIFAST_TRX_AMT', 'BIFAST_TRX_FREQ', 'BIFAST_MYHANA_TRX_AMT', 'BIFAST_MYHANA_TRX_FREQ', 'BIFAST_UNIQUE_CIF_QTY', 'BIFAST_MYHANA_UNIQUE_CIF_QTY');
        $rtolQuery = DB::table('monthly_trf_trx')->select('START_DT', 'TRF_OUT_TRX_AMT', 'TRF_OUT_TRX_FREQ', 'TRF_OUT_MYHANA_TRX_AMT', 'TRF_OUT_MYHANA_TRX_FREQ', 'TRF_OUT_UNIQUE_CIF_QTY', 'TRF_OUT_MYHANA_UNIQUE_CIF_QTY');

        if ($startDate || $endDate) {
            if ($startDate) {
                $qrisQuery->where('START_DT', '>=', $startDate);
                $debitQuery->where('START_DT', '>=', $startDate);
                $billerQuery->where('START_DT', '>=', $startDate);
                $bifastQuery->where('START_DT', '>=', $startDate);
                $rtolQuery->where('START_DT', '>=', $startDate);
            }

            if ($endDate) {
                $qrisQuery->where('START_DT', '<=', $endDate);
                $debitQuery->where('START_DT', '<=', $endDate);
                $billerQuery->where('START_DT', '<=', $endDate);
                $bifastQuery->where('START_DT', '<=', $endDate);
                $rtolQuery->where('START_DT', '<=', $endDate);
            }

            $qrisData = $qrisQuery->orderBy('START_DT')->get();
            $debitData = $debitQuery->orderBy('START_DT')->get();
            $billerData = $billerQuery->orderBy('START_DT')->get();
            $bifastData = $bifastQuery->orderBy('START_DT')->get();
            $rtolData = $rtolQuery->orderBy('START_DT')->get();
        } else {
            // Get last 12 months if no date filter
            $twelveMonthsAgo = Carbon::now()->subMonths(12)->startOfMonth()->toDateString();
            $qrisQuery->where('START_DT', '>=', $twelveMonthsAgo);
            $debitQuery->where('START_DT', '>=', $twelveMonthsAgo);
            $billerQuery->where('START_DT', '>=', $twelveMonthsAgo);
            $bifastQuery->where('START_DT', '>=', $twelveMonthsAgo);
            $rtolQuery->where('START_DT', '>=', $twelveMonthsAgo);

            $qrisData = $qrisQuery->orderBy('START_DT')->get();
            $debitData = $debitQuery->orderBy('START_DT')->get();
            $billerData = $billerQuery->orderBy('START_DT')->get();
            $bifastData = $bifastQuery->orderBy('START_DT')->get();
            $rtolData = $rtolQuery->orderBy('START_DT')->get();
        }


        $labels = $qrisData->pluck('START_DT')
            ->merge($debitData->pluck('START_DT'))
            ->merge($billerData->pluck('START_DT'))
            ->merge($bifastData->pluck('START_DT'))
            ->merge($rtolData->pluck('START_DT'))
            ->unique()
            ->sortBy(function ($date) {
                return Carbon::parse($date)->timestamp;
            })
            ->values();

        foreach ($chartTypes as $chartType) {
            switch ($chartType) {
                case 'Qris_LineBank_Volume':
                    $datasets[] = [
                        'label' => 'QRIS LineBank Volume',
                        'type' => 'bar',
                        'data' => $this->prepareData($qrisData, $labels, 'QRIS_TRX_AMT'),
                        'borderColor' => 'rgba(75, 192, 192, 1)',
                        'backgroundColor' => 'rgba(75, 192, 192, 0.6)',
                        'yAxisID' => 'y1',
                    ];
                    break;
                case 'Debit_LineBank_Volume':
                    $datasets[] = [
                        'label' => 'Debit LineBank Volume',
                        'type' => 'bar',
                        'data' => $this->prepareData($debitData, $labels, 'DEBIT_TRX_AMT'),
                        'borderColor' => 'rgba(54, 162, 235, 1)',
                        'backgroundColor' => 'rgba(54, 162, 235, 0.6)',
                        'yAxisID' => 'y1',
                    ];
                    break;
                case 'Qris_LineBank_Frequency':
                    $datasets[] = [
                        'label' => 'QRIS LineBank Frequency',
                        'type' => 'line',
                        'data' => $this->prepareData($qrisData, $labels, 'QRIS_TRX_FREQ'),
                        'borderColor' => 'rgba(40, 167, 69, 1)',
                        'backgroundColor' => 'rgba(40, 167, 69, 0.2)',
                        'yAxisID' => 'y',
                        'trendlineLinear' => [
                            'style' => 'rgba(255, 0, 0, 1)',
                            'lineStyle' => 'dotted',
                            'width' => 3,
                            'projection' => false,
                        ]
                    ];
                    break;
                case 'Debit_LineBank_Frequency':
                    $datasets[] = [
                        'label' => 'Debit LineBank Frequency',
                        'type' => 'line',
                        'data' => $this->prepareData($debitData, $labels, 'DEBIT_TRX_FREQ'),
                        'borderColor' => 'rgba(255, 99, 132, 1)',
                        'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                        'yAxisID' => 'y',
                        'trendlineLinear' => [
                            'style' => 'rgba(255, 0, 0, 1)',
                            'lineStyle' => 'dotted',
                            'width' => 3,
                            'projection' => false,
                        ]
                    ];
                    break;
                case 'Biller_LineBank_Volume':
                    $datasets[] = [
                        'label' => 'Biller LineBank Volume',
                        'type' => 'bar',
                        'data' => $this->prepareData($billerData, $labels, 'BILL_TRX_AMT'),
                        'borderColor' => 'rgba(255, 159, 64, 1)',
                        'backgroundColor' => 'rgba(255, 159, 64, 0.6)',
                        'yAxisID' => 'y1',
                    ];
                    break;
                case 'Biller_LineBank_Frequency':
                    $datasets[] = [
                        'label' => 'Biller LineBank Frequency',
                        'type' => 'line',
                        'data' => $this->prepareData($billerData, $labels, 'BILL_TRX_FREQ'),
                        'borderColor' => 'rgba(255, 206, 86, 1)',
                        'backgroundColor' => 'rgba(255, 206, 86, 0.2)',
                        'yAxisID' => 'y',
                        'trendlineLinear' => [
                            'style' => 'rgba(255, 0, 0, 1)',
                            'lineStyle' => 'dotted',
                            'width' => 3,
                            'projection' => false,
                        ]
                    ];
                    break;
                case 'Qris_HanaBank_Volume':
                    $datasets[] = [
                        'label' => 'QRIS HanaBank Volume',
                        'type' => 'bar',
                        'data' => $this->prepareData($qrisData, $labels, 'QRIS_MYHANA_TRX_AMT'),
                        'borderColor' => 'rgba(153, 102, 255, 1)',
                        'backgroundColor' => 'rgba(153, 102, 255, 0.6)',
                        'yAxisID' => 'y1',
                    ];
                    break;
                case 'Debit_HanaBank_Volume':
                    $datasets[] = [
                        'label' => 'Debit HanaBank Volume',
                        'type' => 'bar',
                        'data' => $this->prepareData($debitData, $labels, 'DEBIT_MYHANA_TRX_AMT'),
                        'borderColor' => 'rgba(255, 159, 64, 1)',
                        'backgroundColor' => 'rgba(255, 159, 64, 0.6)',
                        'yAxisID' => 'y1',
                    ];
                    break;
                case 'Biller_HanaBank_Volume':
                    $datasets[] = [
                        'label' => 'Biller HanaBank Volume',
                        'type' => 'bar',
                        'data' => $this->prepareData($billerData, $labels, 'BILL_MYHANA_TRX_AMT'),
                        'borderColor' => 'rgba(201, 203, 207, 1)',
                        'backgroundColor' => 'rgba(201, 203, 207, 0.6)',
                        'yAxisID' => 'y1',
                    ];
                    break;
                case 'Qris_HanaBank_Frequency':
                    $datasets[] = [
                        'label' => 'QRIS HanaBank Frequency',
                        'type' => 'line',
                        'data' => $this->prepareData($qrisData, $labels, 'QRIS_MYHANA_TRX_FREQ'),
                        'borderColor' => 'rgba(255, 99, 132, 1)',
                        'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                        'yAxisID' => 'y',
                    ];
                    break;
                case 'Debit_HanaBank_Frequency':
                    $datasets[] = [
                        'label' => 'Debit HanaBank Frequency',
                        'type' => 'line',
                        'data' => $this->prepareData($debitData, $labels, 'DEBIT_MYHANA_TRX_FREQ'),
                        'borderColor' => 'rgba(54, 162, 235, 1)',
                        'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                        'yAxisID' => 'y',
                    ];
                    break;
                case 'Biller_HanaBank_Frequency':
                    $datasets[] = [
                        'label' => 'Biller HanaBank Frequency',
                        'type' => 'line',
                        'data' => $this->prepareData($billerData, $labels, 'BILL_MYHANA_TRX_FREQ'),
                        'borderColor' => 'rgba(75, 192, 192, 1)',
                        'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                        'yAxisID' => 'y',
                    ];
                    break;
                case 'Bifast_LineBank_Volume':
                    $datasets[] = [
                        'label' => 'BI-Fast LineBank Volume',
                        'type' => 'bar',
                        'data' => $this->prepareData($bifastData, $labels, 'BIFAST_TRX_AMT'),
                        'borderColor' => 'rgba(255, 99, 71, 1)',
                        'backgroundColor' => 'rgba(255, 99, 71, 0.6)',
                        'yAxisID' => 'y1',
                    ];
                    break;
                case 'Bifast_LineBank_Frequency':
                    $datasets[] = [
                        'label' => 'BI-Fast LineBank Frequency',
                        'type' => 'line',
                        'data' => $this->prepareData($bifastData, $labels, 'BIFAST_TRX_FREQ'),
                        'borderColor' => 'rgba(255, 165, 0, 1)',
                        'backgroundColor' => 'rgba(255, 165, 0, 0.2)',
                        'yAxisID' => 'y',
                        'trendlineLinear' => [
                            'style' => 'rgba(255, 0, 0, 1)',
                            'lineStyle' => 'dotted',
                            'width' => 3,
                            'projection' => false,
                        ]
                    ];
                    break;
                case 'Bifast_HanaBank_Volume':
                    $datasets[] = [
                        'label' => 'BI-Fast HanaBank Volume',
                        'type' => 'bar',
                        'data' => $this->prepareData($bifastData, $labels, 'BIFAST_MYHANA_TRX_AMT'),
                        'borderColor' => 'rgba(60, 179, 113, 1)',
                        'backgroundColor' => 'rgba(60, 179, 113, 0.6)',
                        'yAxisID' => 'y1',
                    ];
                    break;
                case 'Bifast_HanaBank_Frequency':
                    $datasets[] = [
                        'label' => 'BI-Fast HanaBank Frequency',
                        'type' => 'line',
                        'data' => $this->prepareData($bifastData, $labels, 'BIFAST_MYHANA_TRX_FREQ'),
                        'borderColor' => 'rgba(106, 90, 205, 1)',
                        'backgroundColor' => 'rgba(106, 90, 205, 0.2)',
                        'yAxisID' => 'y',
                    ];
                    break;
                case 'Rtol_LineBank_Volume':
                    $datasets[] = [
                        'label' => 'RTOL Out - LineBank Volume',
                        'type' => 'bar',
                        'data' => $this->prepareData($rtolData, $labels, 'TRF_OUT_TRX_AMT'),
                        'borderColor' => 'rgba(108, 117, 125, 1)',
                        'backgroundColor' => 'rgba(108, 117, 125, 0.6)',
                        'yAxisID' => 'y1',
                    ];
                    break;
                case 'Rtol_LineBank_Frequency':
                    $datasets[] = [
                        'label' => 'RTOL Out - LineBank Frequency',
                        'type' => 'line',
                        'data' => $this->prepareData($rtolData, $labels, 'TRF_OUT_TRX_FREQ'),
                        'borderColor' => 'rgba(255, 0, 255, 1)',
                        'backgroundColor' => 'rgba(255, 0, 255, 0.2)',
                        'yAxisID' => 'y',
                        'trendlineLinear' => [
                            'style' => 'rgba(255, 0, 0, 1)',
                            'lineStyle' => 'dotted',
                            'width' => 3,
                            'projection' => false,
                        ]
                    ];
                    break;
                case 'Rtol_HanaBank_Volume':
                    $datasets[] = [
                        'label' => 'RTOL Out - HanaBank Volume',
                        'type' => 'bar',
                        'data' => $this->prepareData($rtolData, $labels, 'TRF_OUT_MYHANA_TRX_AMT'),
                        'borderColor' => 'rgba(60, 179, 113, 1)',
                        'backgroundColor' => 'rgba(60, 179, 113, 0.6)',
                        'yAxisID' => 'y1',
                    ];
                    break;
                case 'Rtol_HanaBank_Frequency':
                    $datasets[] = [
                        'label' => 'RTOL Out - HanaBank Frequency',
                        'type' => 'line',
                        'data' => $this->prepareData($rtolData, $labels, 'TRF_OUT_MYHANA_TRX_FREQ'),
                        'borderColor' => 'rgba(106, 90, 205, 1)',
                        'backgroundColor' => 'rgba(106, 90, 205, 0.2)',
                        'yAxisID' => 'y',
                    ];
                    break;
                case 'Qris_Unique_CIF':
                    $datasets[] = [
                        'label' => 'QRIS Unique CIF',
                        'type' => 'line',
                        'data' => $this->prepareData($qrisData, $labels, 'QRIS_UNIQUE_CIF_QTY'),
                        'borderColor' => 'rgba(251, 191, 36, 1)',
                        'backgroundColor' => 'rgba(251, 191, 36, 0.2)',
                        'yAxisID' => 'y',
                    ];
                    break;
                case 'Debit_Unique_CIF':
                    $datasets[] = [
                        'label' => 'Debit Unique CIF',
                        'type' => 'line',
                        'data' => $this->prepareData($debitData, $labels, 'DEBIT_UNIQUE_CIF_QTY'),
                        'borderColor' => 'rgba(0, 0, 255, 1)',
                        'backgroundColor' => 'rgba(0, 0, 255, 0.2)',
                        'yAxisID' => 'y',
                    ];
                    break;
                case 'Biller_Unique_CIF':
                    $datasets[] = [
                        'label' => 'Biller Unique CIF',
                        'type' => 'line',
                        'data' => $this->prepareData($billerData, $labels, 'BILL_UNIQUE_CIF_QTY'),
                        'borderColor' => 'rgba(255, 140, 0, 1)',
                        'backgroundColor' => 'rgba(255, 140, 0, 0.2)',
                        'yAxisID' => 'y',
                    ];
                    break;
                case 'Bifast_Unique_CIF':
                    $datasets[] = [
                        'label' => 'BI-Fast Unique CIF',
                        'type' => 'line',
                        'data' => $this->prepareData($bifastData, $labels, 'BIFAST_UNIQUE_CIF_QTY'),
                        'borderColor' => 'rgba(220, 20, 60, 1)',
                        'backgroundColor' => 'rgba(220, 20, 60, 0.2)',
                        'yAxisID' => 'y',
                    ];
                    break;
                case 'Rtol_Unique_CIF':
                    $datasets[] = [
                        'label' => 'RTOL Out - Unique CIF',
                        'type' => 'line',
                        'data' => $this->prepareData($rtolData, $labels, 'TRF_OUT_UNIQUE_CIF_QTY'),
                        'borderColor' => 'rgba(148, 0, 211, 1)',
                        'backgroundColor' => 'rgba(148, 0, 211, 0.2)',
                        'yAxisID' => 'y',
                    ];
                    break;
                case 'Qris_HanaBank_Unique_CIF':
                    $datasets[] = [
                        'label' => 'QRIS HanaBank Unique CIF',
                        'type' => 'line',
                        'data' => $this->prepareData($qrisData, $labels, 'QRIS_MYHANA_UNIQUE_CIF_QTY'),
                        'borderColor' => 'rgba(255, 20, 147, 1)',
                        'backgroundColor' => 'rgba(255, 20, 147, 0.2)',
                        'yAxisID' => 'y',
                    ];
                    break;
                case 'Debit_HanaBank_Unique_CIF':
                    $datasets[] = [
                        'label' => 'Debit HanaBank Unique CIF',
                        'type' => 'line',
                        'data' => $this->prepareData($debitData, $labels, 'DEBIT_MYHANA_UNIQUE_CIF_QTY'),
                        'borderColor' => 'rgba(0, 191, 255, 1)',
                        'backgroundColor' => 'rgba(0, 191, 255, 0.2)',
                        'yAxisID' => 'y',
                    ];
                    break;
                case 'Biller_HanaBank_Unique_CIF':
                    $datasets[] = [
                        'label' => 'Biller HanaBank Unique CIF',
                        'type' => 'line',
                        'data' => $this->prepareData($billerData, $labels, 'BILL_MYHANA_UNIQUE_CIF_QTY'),
                        'borderColor' => 'rgba(255, 105, 180, 1)',
                        'backgroundColor' => 'rgba(255, 105, 180, 0.2)',
                        'yAxisID' => 'y',
                    ];
                    break;
                case 'Bifast_HanaBank_Unique_CIF':
                    $datasets[] = [
                        'label' => 'BI-Fast HanaBank Unique CIF',
                        'type' => 'line',
                        'data' => $this->prepareData($bifastData, $labels, 'BIFAST_MYHANA_UNIQUE_CIF_QTY'),
                        'borderColor' => 'rgba(123, 104, 238, 1)',
                        'backgroundColor' => 'rgba(123, 104, 238, 0.2)',
                        'yAxisID' => 'y',
                    ];
                    break;
                case 'Rtol_HanaBank_Unique_CIF':
                    $datasets[] = [
                        'label' => 'RTOL Out - HanaBank Unique CIF',
                        'type' => 'line',
                        'data' => $this->prepareData($rtolData, $labels, 'TRF_OUT_MYHANA_UNIQUE_CIF_QTY'),
                        'borderColor' => 'rgba(64, 224, 208, 1)',
                        'backgroundColor' => 'rgba(64, 224, 208, 0.2)',
                        'yAxisID' => 'y',
                    ];
                    break;
            }
        }

        return response()->json([
            'labels' => $labels->all(),
            'datasets' => $datasets,
        ]);
    }

    private function prepareData($data, $labels, $column)
    {
        $map = $data->keyBy('START_DT');
        return $labels->map(function ($label) use ($map, $column) {
            return $map->has($label) ? $map[$label]->$column : 0;
        });
    }
}
