<?php

namespace App\Http\Controllers\Form;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class DebitDataController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeLineData(Request $request)
    {
        $validatedData = $request->validate([
            'startDt' => 'required|date',
            'debitTrxFreq' => 'required|numeric',
            'debitTrxAmt' => 'required|numeric',
            'debitUniqueCifQty' => 'required|numeric',
        ]);

        $endDt = Carbon::parse($validatedData['startDt'])->endOfMonth()->toDateString();

        DB::table('monthly_debit_trx')->updateOrInsert(
            ['START_DT' => $validatedData['startDt']],
            [
                'END_DT' => $endDt,
                'DEBIT_TRX_FREQ' => $validatedData['debitTrxFreq'],
                'DEBIT_TRX_AMT' => $validatedData['debitTrxAmt'],
                'DEBIT_UNIQUE_CIF_QTY' => $validatedData['debitUniqueCifQty'],
            ]
        );

        return response()->json(['message' => 'Debit Line data saved successfully!']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeHanaData(Request $request)
    {
        $validatedData = $request->validate([
            'startDt' => 'required|date',
            'debitMyhanaTrxFreq' => 'required|numeric',
            'debitMyhanaTrxAmt' => 'required|numeric',
            'debitMyhanaUniqueCifQty' => 'required|numeric',
        ]);

        $endDt = Carbon::parse($validatedData['startDt'])->endOfMonth()->toDateString();

        DB::table('monthly_debit_trx')->updateOrInsert(
            ['START_DT' => $validatedData['startDt']],
            [
                'END_DT' => $endDt,
                'DEBIT_MYHANA_TRX_FREQ' => $validatedData['debitMyhanaTrxFreq'],
                'DEBIT_MYHANA_TRX_AMT' => $validatedData['debitMyhanaTrxAmt'],
                'DEBIT_MYHANA_UNIQUE_CIF_QTY' => $validatedData['debitMyhanaUniqueCifQty'],
            ]
        );

        return response()->json(['message' => 'Debit MyHana data saved successfully!']);
    }

    /**
     * Retrieve paginated Debit Line data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getDebitLineData(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = DB::table('monthly_debit_trx')
            ->select([
                'START_DT as startDt',
                'DEBIT_TRX_FREQ as debitTrxFreq',
                'DEBIT_TRX_AMT as debitTrxAmt',
                'DEBIT_UNIQUE_CIF_QTY as debitUniqueCifQty',
            ])
            ->whereNotNull('DEBIT_TRX_FREQ');

        if ($request->has('start_dt') && $request->input('start_dt')) {
            $query->where('START_DT', 'like', $request->input('start_dt') . '%');
        }

        if ($request->filled('start_month')) {
            $query->where('START_DT', '>=', Carbon::parse($request->input('start_month'))->startOfMonth());
        }

        if ($request->filled('end_month')) {
            $query->where('START_DT', '<=', Carbon::parse($request->input('end_month'))->endOfMonth());
        }

        if ($request->has('sort_field') && $request->has('sort_type')) {
            $sortField = $request->input('sort_field');
            $sortType = $request->input('sort_type');

            $allowedFields = ['startDt', 'debitTrxFreq', 'debitTrxAmt', 'debitUniqueCifQty'];
            if (in_array($sortField, $allowedFields)) {
                $dbField = [
                    'startDt' => 'START_DT',
                    'debitTrxFreq' => 'DEBIT_TRX_FREQ',
                    'debitTrxAmt' => 'DEBIT_TRX_AMT',
                    'debitUniqueCifQty' => 'DEBIT_UNIQUE_CIF_QTY',
                ][$sortField];
                $query->orderBy($dbField, $sortType);
            }
        } else {
            $query->orderBy('START_DT', 'desc');
        }

        if ($perPage == -1) {
            $data = $query->get();
            return response()->json(['data' => $data]);
        }

        $data = $query->paginate($perPage);

        return response()->json($data);
    }

    /**
     * Retrieve paginated Debit MyHana data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getDebitHanaData(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = DB::table('monthly_debit_trx')
            ->select([
                'START_DT as startDt',
                'DEBIT_MYHANA_TRX_FREQ as debitMyhanaTrxFreq',
                'DEBIT_MYHANA_TRX_AMT as debitMyhanaTrxAmt',
                'DEBIT_MYHANA_UNIQUE_CIF_QTY as debitMyhanaUniqueCifQty'
            ])
            ->whereNotNull('DEBIT_MYHANA_TRX_FREQ');

        if ($request->has('start_dt') && $request->input('start_dt')) {
            $query->where('START_DT', 'like', $request->input('start_dt') . '%');
        }

        if ($request->filled('start_month')) {
            $query->where('START_DT', '>=', Carbon::parse($request->input('start_month'))->startOfMonth());
        }

        if ($request->filled('end_month')) {
            $query->where('START_DT', '<=', Carbon::parse($request->input('end_month'))->endOfMonth());
        }

        if ($request->has('sort_field') && $request->has('sort_type')) {
            $sortField = $request->input('sort_field');
            $sortType = $request->input('sort_type');

            $allowedFields = ['startDt', 'debitMyhanaTrxFreq', 'debitMyhanaTrxAmt', 'debitMyhanaUniqueCifQty'];
            if (in_array($sortField, $allowedFields)) {
                $dbField = [
                    'startDt' => 'START_DT',
                    'debitMyhanaTrxFreq' => 'DEBIT_MYHANA_TRX_FREQ',
                    'debitMyhanaTrxAmt' => 'DEBIT_MYHANA_TRX_AMT',
                    'debitMyhanaUniqueCifQty' => 'DEBIT_MYHANA_UNIQUE_CIF_QTY',
                ][$sortField];
                $query->orderBy($dbField, $sortType);
            }
        } else {
            $query->orderBy('START_DT', 'desc');
        }

        if ($perPage == -1) {
            $data = $query->get();
            return response()->json(['data' => $data]);
        }

        $data = $query->paginate($perPage);

        return response()->json($data);
    }

    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->input('data');

        foreach ($data as $row) {
            $validator = Validator::make($row, [
                'START_DT' => 'required',
                'DEBIT_TRX_FREQ' => 'nullable|numeric',
                'DEBIT_TRX_AMT' => 'nullable|numeric',
                'DEBIT_UNIQUE_CIF_QTY' => 'nullable|numeric',
                'DEBIT_MYHANA_TRX_FREQ' => 'nullable|numeric',
                'DEBIT_MYHANA_TRX_AMT' => 'nullable|numeric',
                'DEBIT_MYHANA_UNIQUE_CIF_QTY' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors(), 'row' => $row], 422);
            }

            try {
                $startDt = Carbon::parse($row['START_DT'])->format('Y-m-d');
                $endDt = Carbon::parse($startDt)->endOfMonth()->toDateString();

                DB::table('monthly_debit_trx')->updateOrInsert(
                    ['START_DT' => $startDt],
                    [
                        'END_DT' => $endDt,
                        'DEBIT_TRX_FREQ' => $row['DEBIT_TRX_FREQ'] ?? null,
                        'DEBIT_TRX_AMT' => $row['DEBIT_TRX_AMT'] ?? null,
                        'DEBIT_UNIQUE_CIF_QTY' => $row['DEBIT_UNIQUE_CIF_QTY'] ?? null,
                        'DEBIT_MYHANA_TRX_FREQ' => $row['DEBIT_MYHANA_TRX_FREQ'] ?? null,
                        'DEBIT_MYHANA_TRX_AMT' => $row['DEBIT_MYHANA_TRX_AMT'] ?? null,
                        'DEBIT_MYHANA_UNIQUE_CIF_QTY' => $row['DEBIT_MYHANA_UNIQUE_CIF_QTY'] ?? null,
                    ]
                );
            } catch (QueryException $e) {
                return response()->json(['message' => 'Database error on row: ' . json_encode($row), 'error' => $e->getMessage()], 500);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Invalid date format on row: ' . json_encode($row), 'error' => $e->getMessage()], 422);
            }
        }

        return response()->json(['message' => 'CSV data imported successfully!']);
    }
}
