@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Switchgear Progress Monitoring</h1>

    <div class="row">
        <div class="col-md-4">
            <div id="progressChartContainer"></div>
        </div>
        <div class="col-md-4">
            <div id="rectificationTimeChartContainer"></div>
        </div>
        <div class="col-md-4">
            <div id="criticalityChartContainer"></div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <h3>Assets Progress</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Functional Location</th>
                        <th>Switchgear Brand</th>
                        <th>TEV</th>
                        <th>Status</th>
                        <th>Defect</th>
                        <th>Rectification Time (Days)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($assets as $asset)
                    <tr>
                        <td>{{ $asset->Functional_Location }}</td>
                        <td>{{ $asset->Switchgear_Brand }}</td>
                        <td>{{ $asset->TEV }}</td>
                        <td>
                            @if ($asset->completed_status)
                                Rectified
                            @else
                                Pending
                            @endif
                        </td>
                        <td>{{ $asset->Defect1 }}</td>
                        <td>
                            @if ($asset->completed_status)
                                {{ \Carbon\Carbon::parse($asset->completed_status)->diffInDays(\Carbon\Carbon::parse($asset->Date)) }}
                            @else
                                N/A
                            @endif
                        </td>
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
    var pendingCount = {{ $pendingCount }};
    var averageRectificationTimes = @json($averageRectificationTimes);
    var criticalityLevels = @json($criticalityLevels);

    // Rectified vs Pending Bar Chart
    Highcharts.chart('progressChartContainer', {
        chart: {
            type: 'bar'
        },
        title: {
            text: 'Rectified vs Pending Switchgear'
        },
        xAxis: {
            categories: ['Status']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Count'
            }
        },
        series: [{
            name: 'Rectified',
            data: [rectifiedCount],
            color: 'rgba(75, 192, 192, 0.8)'
        }, {
            name: 'Pending',
            data: [pendingCount],
            color: 'rgba(255, 99, 132, 0.8)'
        }]
    });

    // Average Rectification Time for Each Defect
    Highcharts.chart('rectificationTimeChartContainer', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Average Rectification Time by Defect'
        },
        xAxis: {
            categories: Object.keys(averageRectificationTimes),
            title: {
                text: 'Defect Types'
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Average Days'
            }
        },
        series: [{
            name: 'Average Rectification Time (Days)',
            data: Object.values(averageRectificationTimes),
            color: 'rgba(75, 192, 192, 0.8)'
        }]
    });

    // Criticality of the Assets
    Highcharts.chart('criticalityChartContainer', {
        chart: {
            type: 'pie'
        },
        title: {
            text: 'Criticality of the Assets'
        },
        series: [{
            name: 'Criticality',
            colorByPoint: true,
            data: Object.keys(criticalityLevels).map(function(key) {
                return {
                    name: key,
                    y: criticalityLevels[key],
                    color: (key === 'Critical') ? 'rgba(255, 99, 132, 0.8)' : 'rgba(75, 192, 192, 0.8)'
                };
            })
        }]
    });
</script>
@endsection
