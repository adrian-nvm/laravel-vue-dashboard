<?php

namespace App\Http\Controllers\Form;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class BifastDataController extends Controller
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
            'bifastTrxFreq' => 'required|numeric',
            'bifastTrxAmt' => 'required|numeric',
            'bifastUniqueCifQty' => 'required|numeric',
        ]);

        $endDt = Carbon::parse($validatedData['startDt'])->endOfMonth()->toDateString();

        DB::table('monthly_bifast_trx')->updateOrInsert(
            ['START_DT' => $validatedData['startDt']],
            [
                'END_DT' => $endDt,
                'BIFAST_TRX_FREQ' => $validatedData['bifastTrxFreq'],
                'BIFAST_TRX_AMT' => $validatedData['bifastTrxAmt'],
                'BIFAST_UNIQUE_CIF_QTY' => $validatedData['bifastUniqueCifQty'],
            ]
        );

        return response()->json(['message' => 'Bifast Line data saved successfully!']);
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
            'bifastMyhanaTrxFreq' => 'required|numeric',
            'bifastMyhanaTrxAmt' => 'required|numeric',
            'bifastMyhanaUniqueCifQty' => 'required|numeric',
        ]);

        $endDt = Carbon::parse($validatedData['startDt'])->endOfMonth()->toDateString();

        DB::table('monthly_bifast_trx')->updateOrInsert(
            ['START_DT' => $validatedData['startDt']],
            [
                'END_DT' => $endDt,
                'BIFAST_MYHANA_TRX_FREQ' => $validatedData['bifastMyhanaTrxFreq'],
                'BIFAST_MYHANA_TRX_AMT' => $validatedData['bifastMyhanaTrxAmt'],
                'BIFAST_MYHANA_UNIQUE_CIF_QTY' => $validatedData['bifastMyhanaUniqueCifQty'],
            ]
        );

        return response()->json(['message' => 'Bifast MyHana data saved successfully!']);
    }

    /**
     * Retrieve paginated Bifast Line data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getBifastLineData(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = DB::table('monthly_bifast_trx')
            ->select([
                'START_DT as startDt',
                'BIFAST_TRX_FREQ as bifastTrxFreq',
                'BIFAST_TRX_AMT as bifastTrxAmt',
                'BIFAST_UNIQUE_CIF_QTY as bifastUniqueCifQty',
            ])
            ->whereNotNull('BIFAST_TRX_FREQ');

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

            $allowedFields = ['startDt', 'bifastTrxFreq', 'bifastTrxAmt', 'bifastUniqueCifQty'];
            if (in_array($sortField, $allowedFields)) {
                $dbField = [
                    'startDt' => 'START_DT',
                    'bifastTrxFreq' => 'BIFAST_TRX_FREQ',
                    'bifastTrxAmt' => 'BIFAST_TRX_AMT',
                    'bifastUniqueCifQty' => 'BIFAST_UNIQUE_CIF_QTY',
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
     * Retrieve paginated Bifast MyHana data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getBifastHanaData(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = DB::table('monthly_bifast_trx')
            ->select([
                'START_DT as startDt',
                'BIFAST_MYHANA_TRX_FREQ as bifastMyhanaTrxFreq',
                'BIFAST_MYHANA_TRX_AMT as bifastMyhanaTrxAmt',
                'BIFAST_MYHANA_UNIQUE_CIF_QTY as bifastMyhanaUniqueCifQty'
            ])
            ->whereNotNull('BIFAST_MYHANA_TRX_FREQ');

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

            $allowedFields = ['startDt', 'bifastMyhanaTrxFreq', 'bifastMyhanaTrxAmt', 'bifastMyhanaUniqueCifQty'];
            if (in_array($sortField, $allowedFields)) {
                $dbField = [
                    'startDt' => 'START_DT',
                    'bifastMyhanaTrxFreq' => 'BIFAST_MYHANA_TRX_FREQ',
                    'bifastMyhanaTrxAmt' => 'BIFAST_MYHANA_TRX_AMT',
                    'bifastMyhanaUniqueCifQty' => 'BIFAST_MYHANA_UNIQUE_CIF_QTY',
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
                'BIFAST_TRX_FREQ' => 'nullable|numeric',
                'BIFAST_TRX_AMT' => 'nullable|numeric',
                'BIFAST_UNIQUE_CIF_QTY' => 'nullable|numeric',
                'BIFAST_MYHANA_TRX_FREQ' => 'nullable|numeric',
                'BIFAST_MYHANA_TRX_AMT' => 'nullable|numeric',
                'BIFAST_MYHANA_UNIQUE_CIF_QTY' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors(), 'row' => $row], 422);
            }

            try {
                $startDt = Carbon::parse($row['START_DT'])->format('Y-m-d');
                $endDt = Carbon::parse($startDt)->endOfMonth()->toDateString();

                DB::table('monthly_bifast_trx')->updateOrInsert(
                    ['START_DT' => $startDt],
                    [
                        'END_DT' => $endDt,
                        'BIFAST_TRX_FREQ' => $row['BIFAST_TRX_FREQ'] ?? null,
                        'BIFAST_TRX_AMT' => $row['BIFAST_TRX_AMT'] ?? null,
                        'BIFAST_UNIQUE_CIF_QTY' => $row['BIFAST_UNIQUE_CIF_QTY'] ?? null,
                        'BIFAST_MYHANA_TRX_FREQ' => $row['BIFAST_MYHANA_TRX_FREQ'] ?? null,
                        'BIFAST_MYHANA_TRX_AMT' => $row['BIFAST_MYHANA_TRX_AMT'] ?? null,
                        'BIFAST_MYHANA_UNIQUE_CIF_QTY' => $row['BIFAST_MYHANA_UNIQUE_CIF_QTY'] ?? null,
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
