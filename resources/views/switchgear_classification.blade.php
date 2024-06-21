@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Switchgear Classification</h1>

    <div class="row">
        <div class="col-md-6">
            <div id="rectifiedChartContainer"></div>
        </div>
        <div class="col-md-6">
            <div id="defectsChartContainer"></div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <h3>Assets Not Rectified Yet</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Functional Location</th>
                        <th>Switchgear Brand</th>
                        <th>Report Date</th>
                        <th>Target Date</th>
                        <th>Defect</th>
                        <th>Sub Defect</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($notRectifiedAssets as $asset)
                    <tr>
                        <td>{{ $asset->Functional_Location }}</td>
                        <td>{{ $asset->Switchgear_Brand }}</td>
                        <td>{{ $asset->Date }}</td>
                        <td>{{ $asset->Target_Date }}</td>
                        <td>{{ $asset->Defect }}</td>
                        <td>{{ $asset->Defect1 }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
    // Data for the charts
    var rectifiedCount = {{ $rectifiedCount }};
    var notRectifiedCount = {{ $notRectifiedCount }};
    var defectTypes = @json($defectTypes);

    // Rectified vs Not Rectified Pie Chart
    Highcharts.chart('rectifiedChartContainer', {
        chart: {
            type: 'pie'
        },
        title: {
            text: 'Rectified vs Not Rectified Switchgear'
        },
        series: [{
            name: 'Switchgear',
            colorByPoint: true,
            data: [{
                name: 'Rectified',
                y: rectifiedCount,
                color: 'rgba(75, 192, 192, 0.8)'
            }, {
                name: 'Not Rectified',
                y: notRectifiedCount,
                color: 'rgba(255, 99, 132, 0.8)'
            }]
        }]
    });

    // Defects Bar Chart
    Highcharts.chart('defectsChartContainer', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Defects in Not Rectified Assets'
        },
        xAxis: {
            categories: Object.keys(defectTypes),
            title: {
                text: 'Defect Types'
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Number of Assets'
            }
        },
        series: [{
            name: 'Assets',
            data: Object.values(defectTypes),
            color: 'rgba(255, 99, 132, 0.8)'
        }]
    });
</script>
@endsection
