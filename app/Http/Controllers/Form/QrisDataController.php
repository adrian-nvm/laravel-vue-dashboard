<?php

namespace App\Http\Controllers\Form;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class QrisDataController extends Controller
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
            'qrisTrxFreq' => 'required|numeric',
            'qrisTrxAmt' => 'required|numeric',
            'qrisUniqueCifQty' => 'required|numeric',
        ]);

        $endDt = Carbon::parse($validatedData['startDt'])->endOfMonth()->toDateString();

        DB::table('monthly_qris_trx')->updateOrInsert(
            ['START_DT' => $validatedData['startDt']],
            [
                'END_DT' => $endDt,
                'QRIS_TRX_FREQ' => $validatedData['qrisTrxFreq'],
                'QRIS_TRX_AMT' => $validatedData['qrisTrxAmt'],
                'QRIS_UNIQUE_CIF_QTY' => $validatedData['qrisUniqueCifQty'],
            ]
        );

        return response()->json(['message' => 'QRIS Line data saved successfully!']);
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
            'qrisMyhanaTrxFreq' => 'required|numeric',
            'qrisMyhanaTrxAmt' => 'required|numeric',
            'qrisMyhanaUniqueCifQty' => 'required|numeric',
        ]);

        $endDt = Carbon::parse($validatedData['startDt'])->endOfMonth()->toDateString();

        DB::table('monthly_qris_trx')->updateOrInsert(
            ['START_DT' => $validatedData['startDt']],
            [
                'END_DT' => $endDt,
                'QRIS_MYHANA_TRX_FREQ' => $validatedData['qrisMyhanaTrxFreq'],
                'QRIS_MYHANA_TRX_AMT' => $validatedData['qrisMyhanaTrxAmt'],
                'QRIS_MYHANA_UNIQUE_CIF_QTY' => $validatedData['qrisMyhanaUniqueCifQty'],
            ]
        );

        return response()->json(['message' => 'QRIS MyHana data saved successfully!']);
    }

    /**
     * Retrieve paginated QRIS Line data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getQrisLineData(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = DB::table('monthly_qris_trx')
            ->select([
                'START_DT as startDt',
                'QRIS_TRX_FREQ as qrisTrxFreq',
                'QRIS_TRX_AMT as qrisTrxAmt',
                'QRIS_UNIQUE_CIF_QTY as qrisUniqueCifQty',
            ])
            ->whereNotNull('QRIS_TRX_FREQ');

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

            // Whitelist columns to prevent SQL injection
            $allowedFields = ['startDt', 'qrisTrxFreq', 'qrisTrxAmt', 'qrisUniqueCifQty'];
            if (in_array($sortField, $allowedFields)) {
                // Map frontend field to DB column
                $dbField = [
                    'startDt' => 'START_DT',
                    'qrisTrxFreq' => 'QRIS_TRX_FREQ',
                    'qrisTrxAmt' => 'QRIS_TRX_AMT',
                    'qrisUniqueCifQty' => 'QRIS_UNIQUE_CIF_QTY',
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
     * Retrieve paginated QRIS MyHana data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getQrisHanaData(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = DB::table('monthly_qris_trx')
            ->select([
                'START_DT as startDt',
                'QRIS_MYHANA_TRX_FREQ as qrisMyhanaTrxFreq',
                'QRIS_MYHANA_TRX_AMT as qrisMyhanaTrxAmt',
                'QRIS_MYHANA_UNIQUE_CIF_QTY as qrisMyhanaUniqueCifQty'
            ])
            ->whereNotNull('QRIS_MYHANA_TRX_FREQ');

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

            // Whitelist columns to prevent SQL injection
            $allowedFields = ['startDt', 'qrisMyhanaTrxFreq', 'qrisMyhanaTrxAmt', 'qrisMyhanaUniqueCifQty'];
            if (in_array($sortField, $allowedFields)) {
                // Map frontend field to DB column
                $dbField = [
                    'startDt' => 'START_DT',
                    'qrisMyhanaTrxFreq' => 'QRIS_MYHANA_TRX_FREQ',
                    'qrisMyhanaTrxAmt' => 'QRIS_MYHANA_TRX_AMT',
                    'qrisMyhanaUniqueCifQty' => 'QRIS_MYHANA_UNIQUE_CIF_QTY',
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
                'START_DT' => 'required', // Date validation can be tricky with multiple formats
                'QRIS_TRX_FREQ' => 'nullable|numeric',
                'QRIS_TRX_AMT' => 'nullable|numeric',
                'QRIS_UNIQUE_CIF_QTY' => 'nullable|numeric',
                'QRIS_MYHANA_TRX_FREQ' => 'nullable|numeric',
                'QRIS_MYHANA_TRX_AMT' => 'nullable|numeric',
                'QRIS_MYHANA_UNIQUE_CIF_QTY' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors(), 'row' => $row], 422);
            }

            try {
                // Attempt to parse common date formats
                $startDt = Carbon::parse($row['START_DT'])->format('Y-m-d');
                $endDt = Carbon::parse($startDt)->endOfMonth()->toDateString();

                DB::table('monthly_qris_trx')->updateOrInsert(
                    ['START_DT' => $startDt],
                    [
                        'END_DT' => $endDt,
                        'QRIS_TRX_FREQ' => $row['QRIS_TRX_FREQ'] ?? null,
                        'QRIS_TRX_AMT' => $row['QRIS_TRX_AMT'] ?? null,
                        'QRIS_UNIQUE_CIF_QTY' => $row['QRIS_UNIQUE_CIF_QTY'] ?? null,
                        'QRIS_MYHANA_TRX_FREQ' => $row['QRIS_MYHANA_TRX_FREQ'] ?? null,
                        'QRIS_MYHANA_TRX_AMT' => $row['QRIS_MYHANA_TRX_AMT'] ?? null,
                        'QRIS_MYHANA_UNIQUE_CIF_QTY' => $row['QRIS_MYHANA_UNIQUE_CIF_QTY'] ?? null,
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
