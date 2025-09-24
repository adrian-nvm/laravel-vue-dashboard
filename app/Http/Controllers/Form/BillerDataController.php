<?php

namespace App\Http\Controllers\Form;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class BillerDataController extends Controller
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
            'billerTrxFreq' => 'required|numeric',
            'billerTrxAmt' => 'required|numeric',
            'billerUniqueCifQty' => 'required|numeric',
        ]);

        $endDt = Carbon::parse($validatedData['startDt'])->endOfMonth()->toDateString();

        DB::table('monthly_bill_trx')->updateOrInsert(
            ['START_DT' => $validatedData['startDt']],
            [
                'END_DT' => $endDt,
                'BILL_TRX_FREQ' => $validatedData['billerTrxFreq'],
                'BILL_TRX_AMT' => $validatedData['billerTrxAmt'],
                'BILL_UNIQUE_CIF_QTY' => $validatedData['billerUniqueCifQty'],
            ]
        );

        return response()->json(['message' => 'Biller Line data saved successfully!']);
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
            'billerMyhanaTrxFreq' => 'required|numeric',
            'billerMyhanaTrxAmt' => 'required|numeric',
            'billerMyhanaUniqueCifQty' => 'required|numeric',
        ]);

        $endDt = Carbon::parse($validatedData['startDt'])->endOfMonth()->toDateString();

        DB::table('monthly_bill_trx')->updateOrInsert(
            ['START_DT' => $validatedData['startDt']],
            [
                'END_DT' => $endDt,
                'BILL_MYHANA_TRX_FREQ' => $validatedData['billerMyhanaTrxFreq'],
                'BILL_MYHANA_TRX_AMT' => $validatedData['billerMyhanaTrxAmt'],
                'BILL_MYHANA_UNIQUE_CIF_QTY' => $validatedData['billerMyhanaUniqueCifQty'],
            ]
        );

        return response()->json(['message' => 'Biller MyHana data saved successfully!']);
    }

    /**
     * Retrieve paginated Biller Line data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getBillerLineData(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = DB::table('monthly_bill_trx')
            ->select([
                'START_DT as startDt',
                'BILL_TRX_FREQ as billerTrxFreq',
                'BILL_TRX_AMT as billerTrxAmt',
                'BILL_UNIQUE_CIF_QTY as billerUniqueCifQty',
            ])
            ->whereNotNull('BILL_TRX_FREQ');

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

            $allowedFields = ['startDt', 'billerTrxFreq', 'billerTrxAmt', 'billerUniqueCifQty'];
            if (in_array($sortField, $allowedFields)) {
                $dbField = [
                    'startDt' => 'START_DT',
                    'billerTrxFreq' => 'BILL_TRX_FREQ',
                    'billerTrxAmt' => 'BILL_TRX_AMT',
                    'billerUniqueCifQty' => 'BILL_UNIQUE_CIF_QTY',
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
     * Retrieve paginated Biller MyHana data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getBillerHanaData(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = DB::table('monthly_bill_trx')
            ->select([
                'START_DT as startDt',
                'BILL_MYHANA_TRX_FREQ as billerMyhanaTrxFreq',
                'BILL_MYHANA_TRX_AMT as billerMyhanaTrxAmt',
                'BILL_MYHANA_UNIQUE_CIF_QTY as billerMyhanaUniqueCifQty'
            ])
            ->whereNotNull('BILL_MYHANA_TRX_FREQ');

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

            $allowedFields = ['startDt', 'billerMyhanaTrxFreq', 'billerMyhanaTrxAmt', 'billerMyhanaUniqueCifQty'];
            if (in_array($sortField, $allowedFields)) {
                $dbField = [
                    'startDt' => 'START_DT',
                    'billerMyhanaTrxFreq' => 'BILL_MYHANA_TRX_FREQ',
                    'billerMyhanaTrxAmt' => 'BILL_MYHANA_TRX_AMT',
                    'billerMyhanaUniqueCifQty' => 'BILL_MYHANA_UNIQUE_CIF_QTY',
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
                'BILL_TRX_FREQ' => 'nullable|numeric',
                'BILL_TRX_AMT' => 'nullable|numeric',
                'BILL_UNIQUE_CIF_QTY' => 'nullable|numeric',
                'BILL_MYHANA_TRX_FREQ' => 'nullable|numeric',
                'BILL_MYHANA_TRX_AMT' => 'nullable|numeric',
                'BILL_MYHANA_UNIQUE_CIF_QTY' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors(), 'row' => $row], 422);
            }

            try {
                $startDt = Carbon::parse($row['START_DT'])->format('Y-m-d');
                $endDt = Carbon::parse($startDt)->endOfMonth()->toDateString();

                DB::table('monthly_bill_trx')->updateOrInsert(
                    ['START_DT' => $startDt],
                    [
                        'END_DT' => $endDt,
                        'BILL_TRX_FREQ' => $row['BILL_TRX_FREQ'] ?? null,
                        'BILL_TRX_AMT' => $row['BILL_TRX_AMT'] ?? null,
                        'BILL_UNIQUE_CIF_QTY' => $row['BILL_UNIQUE_CIF_QTY'] ?? null,
                        'BILL_MYHANA_TRX_FREQ' => $row['BILL_MYHANA_TRX_FREQ'] ?? null,
                        'BILL_MYHANA_TRX_AMT' => $row['BILL_MYHANA_TRX_AMT'] ?? null,
                        'BILL_MYHANA_UNIQUE_CIF_QTY' => $row['BILL_MYHANA_UNIQUE_CIF_QTY'] ?? null,
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
