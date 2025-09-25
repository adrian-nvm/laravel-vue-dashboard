<?php

namespace App\Http\Controllers\Chart;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BifastChartController extends Controller
{
    public function getChartData(Request $request)
    {
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');

        $startDate = $startMonth ? Carbon::parse($startMonth)->startOfMonth()->toDateString() : null;
        $endDate = $endMonth ? Carbon::parse($endMonth)->endOfMonth()->toDateString() : null;

        $query = DB::table('monthly_bifast_trx')
            ->select([
                'START_DT as startDt',
                'BIFAST_TRX_FREQ as bifastTrxFreq',
                'BIFAST_TRX_AMT as bifastTrxAmt',
                'BIFAST_UNIQUE_CIF_QTY as bifastUniqueCifQty',
                'BIFAST_MYHANA_TRX_FREQ as bifastMyhanaTrxFreq',
                'BIFAST_MYHANA_TRX_AMT as bifastMyhanaTrxAmt',
                'BIFAST_MYHANA_UNIQUE_CIF_QTY as bifastMyhanaUniqueCifQty'
            ]);

        if ($startDate || $endDate) {
            if ($startDate) {
                $query->where('START_DT', '>=', $startDate);
            }
            if ($endDate) {
                $query->where('START_DT', '<=', $endDate);
            }
        } else {
            // Get last 12 months if no date filter
            $twelveMonthsAgo = Carbon::now()->subMonths(12)->startOfMonth()->toDateString();
            $query->where('START_DT', '>=', $twelveMonthsAgo);
        }

        $query->orderBy('START_DT', 'asc');

        $data = $query->get();

        return response()->json($data)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }
    public function showLineChart(Request $request)
    {
        return $this->getChartData($request);
    }
    public function showHanaChart(Request $request)
    {
        return $this->getChartData($request);
    }
}
