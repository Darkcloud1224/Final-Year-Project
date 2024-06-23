@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Total Assets -->
    <div class="row mb-4">
        <div class="col-md-12 text-center">
            <h3 class="display-4">Total Assets: {{ $totalAssets }}</h3>
        </div>
    </div>

    <!-- Rectified and Not Rectified Assets -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Assets Rectified</h5>
                    <p class="card-text display-4">{{ $rectifiedAssets }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white bg-danger mb-3">
                <div class="card-body">
                    <h5 class="card-title">Assets Not Rectified</h5>
                    <p class="card-text display-4">{{ $notRectifiedAssets }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row mb-4">
        <div id="healthStatusChart" class="col-md-6" style="height: 400px;"></div>
        <div id="defectsBarGraph" class="col-md-6" style="height: 400px;"></div>
    </div>
    <div class="row mb-4">
        <div id="defectsByBrandChart" class="col-md-12" style="height: 600px;"></div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script>
    Highcharts.chart('healthStatusChart', {
        chart: {
            type: 'pie'
        },
        title: {
            text: 'Health Status Distribution'
        },
        series: [{
            name: 'Health Status',
            data: {!! json_encode(array_map(function ($key, $value) {
                return ['name' => $key, 'y' => $value];
            }, array_keys($criticalityData), $criticalityData)) !!}
        }]
    });

    Highcharts.chart('defectsBarGraph', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Defects Distribution'
        },
        xAxis: {
            categories: {!! json_encode(array_keys($defectsData)) !!}
        },
        yAxis: {
            title: {
                text: 'Number of Assets'
            }
        },
        series: [{
            name: 'Assets',
            data: {!! json_encode(array_values($defectsData)) !!}
        }]
    });

    Highcharts.chart('defectsByBrandChart', {
        chart: {
            type: 'bar'
        },
        title: {
            text: 'Defects by Brand'
        },
        xAxis: {
            categories: {!! json_encode($categories) !!}
        },
        yAxis: {
            title: {
                text: 'Number of Defects'
            }
        },
        series: {!! json_encode($series) !!}
    });
</script>

<style>
    .card-title {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .card-text {
        font-size: 2.5rem;
        font-weight: bold;
    }

    .display-4 {
        font-size: 2.5rem;
    }

    h3.display-4 {
        font-weight: bold;
        margin-bottom: 1rem;
    }

    .container {
        margin-top: 2rem;
    }
</style>
@endsection
