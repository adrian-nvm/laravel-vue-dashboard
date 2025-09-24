@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1>Slide Show Charts</h1>
        <div id="app">
            <chart-slideshow :data='@json($chartData ?? [])'></chart-slideshow>
        </div>
    </div>
@endsection
