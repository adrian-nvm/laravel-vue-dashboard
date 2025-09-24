@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1>Biller Line Chart</h1>

        <form method="GET" action="{{ route('biller.billerlinechart') }}" class="form-inline mb-4">
            <div class="form-group mr-3">
                <label for="start_month" class="mr-2">Start Month</label>
                <input type="month" class="form-control" id="start_month" name="start_month" value="{{ request('start_month') }}">
            </div>
            <div class="form-group mr-3">
                <label for="end_month" class="mr-2">End Month</label>
                <input type="month" class="form-control" id="end_month" name="end_month" value="{{ request('end_month') }}">
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <biller-line-chart :data='@json($chartData)'></biller-line-chart>
    </div>
@endsection
