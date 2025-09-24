@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <router-view></router-view>
    </div>
@endsection

@push('scripts')
<script>
    window.chartData = @json($chartData ?? []);
</script>
@endpush
