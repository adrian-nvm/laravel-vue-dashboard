<?php

namespace App\Http\Controllers\Chart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CombinedChartController extends Controller
{
    public function fetchData(Request $request)
    {
        $chartTypes = $request->input('charts', []);
        $datasets = [];

        $qrisData = DB::table('monthly_qris_trx')->select('START_DT', 'QRIS_TRX_AMT', 'QRIS_TRX_FREQ')->orderBy('START_DT')->get();
        $debitData = DB::table('monthly_debit_trx')->select('START_DT', 'DEBIT_TRX_AMT', 'DEBIT_TRX_FREQ')->orderBy('START_DT')->get();
        $billerData = DB::table('monthly_bill_trx')->select('START_DT', 'BILL_TRX_AMT', 'BILL_TRX_FREQ')->orderBy('START_DT')->get();

        $labels = $qrisData->pluck('START_DT')->merge($debitData->pluck('START_DT'))->merge($billerData->pluck('START_DT'))->unique()->sort();

        foreach ($chartTypes as $chartType) {
            switch ($chartType) {
                case 'qrisLine_volume':
                    $datasets[] = [
                        'label' => 'QRIS Volume',
                        'data' => $this->prepareData($qrisData, $labels, 'QRIS_TRX_AMT'),
                        'borderColor' => 'rgba(255, 99, 132, 1)',
                        'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    ];
                    break;
                case 'debitLine_volume':
                    $datasets[] = [
                        'label' => 'Debit Volume',
                        'data' => $this->prepareData($debitData, $labels, 'DEBIT_TRX_AMT'),
                        'borderColor' => 'rgba(54, 162, 235, 1)',
                        'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    ];
                    break;
                case 'qrisLine_frequency':
                    $datasets[] = [
                        'label' => 'QRIS Frequency',
                        'data' => $this->prepareData($qrisData, $labels, 'QRIS_TRX_FREQ'),
                        'borderColor' => 'rgba(255, 206, 86, 1)',
                        'backgroundColor' => 'rgba(255, 206, 86, 0.2)',
                    ];
                    break;
                case 'debitLine_frequency':
                    $datasets[] = [
                        'label' => 'Debit Frequency',
                        'data' => $this->prepareData($debitData, $labels, 'DEBIT_TRX_FREQ'),
                        'borderColor' => 'rgba(75, 192, 192, 1)',
                        'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    ];
                    break;
                case 'billerLine_volume':
                    $datasets[] = [
                        'label' => 'Biller Volume',
                        'data' => $this->prepareData($billerData, $labels, 'BILL_TRX_AMT'),
                        'borderColor' => 'rgba(153, 102, 255, 1)',
                        'backgroundColor' => 'rgba(153, 102, 255, 0.2)',
                    ];
                    break;
                case 'billerLine_frequency':
                    $datasets[] = [
                        'label' => 'Biller Frequency',
                        'data' => $this->prepareData($billerData, $labels, 'BILL_TRX_FREQ'),
                        'borderColor' => 'rgba(255, 159, 64, 1)',
                        'backgroundColor' => 'rgba(255, 159, 64, 0.2)',
                    ];
                    break;
            }
        }

        return response()->json([
            'labels' => $labels->values()->all(),
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
