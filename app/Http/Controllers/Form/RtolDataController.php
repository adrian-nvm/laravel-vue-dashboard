<?php

namespace App\Http\Controllers\Form;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class RtolDataController extends Controller
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
            'rtolTrxFreq' => 'required|numeric',
            'rtolTrxAmt' => 'required|numeric',
            'rtolUniqueCifQty' => 'required|numeric',
        ]);

        $endDt = Carbon::parse($validatedData['startDt'])->endOfMonth()->toDateString();

        DB::table('monthly_trf_trx')->updateOrInsert(
            ['START_DT' => $validatedData['startDt']],
            [
                'END_DT' => $endDt,
                'TRF_OUT_TRX_FREQ' => $validatedData['rtolTrxFreq'],
                'TRF_OUT_TRX_AMT' => $validatedData['rtolTrxAmt'],
                'TRF_OUT_UNIQUE_CIF_QTY' => $validatedData['rtolUniqueCifQty'],
            ]
        );

        return response()->json(['message' => 'RTOL data saved successfully!']);
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
            'rtolMyhanaTrxFreq' => 'required|numeric',
            'rtolMyhanaTrxAmt' => 'required|numeric',
            'rtolMyhanaUniqueCifQty' => 'required|numeric',
        ]);

        $endDt = Carbon::parse($validatedData['startDt'])->endOfMonth()->toDateString();

        DB::table('monthly_trf_trx')->updateOrInsert(
            ['START_DT' => $validatedData['startDt']],
            [
                'END_DT' => $endDt,
                'TRF_OUT_MYHANA_TRX_FREQ' => $validatedData['rtolMyhanaTrxFreq'],
                'TRF_OUT_MYHANA_TRX_AMT' => $validatedData['rtolMyhanaTrxAmt'],
                'TRF_OUT_MYHANA_UNIQUE_CIF_QTY' => $validatedData['rtolMyhanaUniqueCifQty'],
            ]
        );

        return response()->json(['message' => 'RTOL MyHana data saved successfully!']);
    }

    /**
     * Retrieve paginated RTOL Line data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getRtolLineData(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = DB::table('monthly_trf_trx')
            ->select([
                'START_DT as startDt',
                'TRF_OUT_TRX_FREQ as rtolTrxFreq',
                'TRF_OUT_TRX_AMT as rtolTrxAmt',
                'TRF_OUT_UNIQUE_CIF_QTY as rtolUniqueCifQty',
            ])
            ->whereNotNull('TRF_OUT_TRX_FREQ');

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

            $allowedFields = ['startDt', 'rtolTrxFreq', 'rtolTrxAmt', 'rtolUniqueCifQty'];
            if (in_array($sortField, $allowedFields)) {
                $dbField = [
                    'startDt' => 'START_DT',
                    'rtolTrxFreq' => 'TRF_OUT_TRX_FREQ',
                    'rtolTrxAmt' => 'TRF_OUT_TRX_AMT',
                    'rtolUniqueCifQty' => 'TRF_OUT_UNIQUE_CIF_QTY',
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
     * Retrieve paginated RTOL MyHana data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getRtolHanaData(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = DB::table('monthly_trf_trx')
            ->select([
                'START_DT as startDt',
                'TRF_OUT_MYHANA_TRX_FREQ as rtolMyhanaTrxFreq',
                'TRF_OUT_MYHANA_TRX_AMT as rtolMyhanaTrxAmt',
                'TRF_OUT_MYHANA_UNIQUE_CIF_QTY as rtolMyhanaUniqueCifQty'
            ])
            ->whereNotNull('TRF_OUT_MYHANA_TRX_FREQ');

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

            $allowedFields = ['startDt', 'rtolMyhanaTrxFreq', 'rtolMyhanaTrxAmt', 'rtolMyhanaUniqueCifQty'];
            if (in_array($sortField, $allowedFields)) {
                $dbField = [
                    'startDt' => 'START_DT',
                    'rtolMyhanaTrxFreq' => 'TRF_OUT_MYHANA_TRX_FREQ',
                    'rtolMyhanaTrxAmt' => 'TRF_OUT_MYHANA_TRX_AMT',
                    'rtolMyhanaUniqueCifQty' => 'TRF_OUT_MYHANA_UNIQUE_CIF_QTY',
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
                'TRF_OUT_TRX_FREQ' => 'nullable|numeric',
                'TRF_OUT_TRX_AMT' => 'nullable|numeric',
                'TRF_OUT_UNIQUE_CIF_QTY' => 'nullable|numeric',
                'TRF_OUT_MYHANA_TRX_FREQ' => 'nullable|numeric',
                'TRF_OUT_MYHANA_TRX_AMT' => 'nullable|numeric',
                'TRF_OUT_MYHANA_UNIQUE_CIF_QTY' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors(), 'row' => $row], 422);
            }

            try {
                $startDt = Carbon::parse($row['START_DT'])->format('Y-m-d');
                $endDt = Carbon::parse($startDt)->endOfMonth()->toDateString();

                DB::table('monthly_trf_trx')->updateOrInsert(
                    ['START_DT' => $startDt],
                    [
                        'END_DT' => $endDt,
                        'TRF_OUT_TRX_FREQ' => $row['TRF_OUT_TRX_FREQ'] ?? null,
                        'TRF_OUT_TRX_AMT' => $row['TRF_OUT_TRX_AMT'] ?? null,
                        'TRF_OUT_UNIQUE_CIF_QTY' => $row['TRF_OUT_UNIQUE_CIF_QTY'] ?? null,
                        'TRF_OUT_MYHANA_TRX_FREQ' => $row['TRF_OUT_MYHANA_TRX_FREQ'] ?? null,
                        'TRF_OUT_MYHANA_TRX_AMT' => $row['TRF_OUT_MYHANA_TRX_AMT'] ?? null,
                        'TRF_OUT_MYHANA_UNIQUE_CIF_QTY' => $row['TRF_OUT_MYHANA_UNIQUE_CIF_QTY'] ?? null,
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
