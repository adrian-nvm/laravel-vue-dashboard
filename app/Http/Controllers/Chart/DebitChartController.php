<?php

namespace App\Http\Controllers\Chart;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebitChartController extends Controller
{
    public function getChartData(Request $request)
    {
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');

        $startDate = $startMonth ? Carbon::parse($startMonth)->startOfMonth()->toDateString() : null;
        $endDate = $endMonth ? Carbon::parse($endMonth)->endOfMonth()->toDateString() : null;

        $query = DB::table('monthly_debit_trx')
            ->select([
                'START_DT as startDt',
                'DEBIT_TRX_FREQ as debitTrxFreq',
                'DEBIT_TRX_AMT as debitTrxAmt',
                'DEBIT_UNIQUE_CIF_QTY as debitUniqueCifQty',
                'DEBIT_MYHANA_TRX_FREQ as debitMyhanaTrxFreq',
                'DEBIT_MYHANA_TRX_AMT as debitMyhanaTrxAmt',
                'DEBIT_MYHANA_UNIQUE_CIF_QTY as debitMyhanaUniqueCifQty'
            ]);

        if ($startDate && $endDate) {
            $query->whereBetween('START_DT', [$startDate, $endDate])->orderBy('START_DT', 'asc');
        } elseif ($startDate) {
            $query->where('START_DT', '>=', $startDate)->orderBy('START_DT', 'asc');
        } else {
            // Get last 12 months if no date filter
            $subQuery = DB::table('monthly_debit_trx')->orderBy('START_DT', 'desc')->limit(12);
            $query->fromSub($subQuery, 'monthly_debit_trx')->orderBy('START_DT', 'asc');
        }

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
