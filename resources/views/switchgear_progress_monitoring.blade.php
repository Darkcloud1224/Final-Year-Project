@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Switchgear Progress Monitoring</h1>

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
            <ul class="nav nav-tabs" id="assetTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="rectified-tab" data-toggle="tab" href="#rectified" role="tab" aria-controls="rectified" aria-selected="true">Rectified</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pending-tab" data-toggle="tab" href="#pending" role="tab" aria-controls="pending" aria-selected="false">Pending</a>
                </li>
            </ul>
            <div class="tab-content mt-3" id="assetTabsContent">
                <div class="tab-pane fade show active" id="rectified" role="tabpanel" aria-labelledby="rectified-tab">
                    <table class="table table-bordered mt-3">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Functional Location</th>
                                <th>Reported Date</th>
                                <th>Target Date</th>
                                <th>Switchgear Brand</th>
                                <th>Defect</th>
                                <th>Rectification Time (Days)</th>
                                <th>Criticality</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rectifiedAssets as $asset)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $asset->Functional_Location }}</td>
                                <td>{{ $asset->Date }}</td>
                                <td>{{ $asset->Target_Date }}</td>
                                <td>{{ $asset->Switchgear_Brand }}</td>
                                <td>{{ $asset->Defect1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($asset->completed_status)->diffInDays(\Carbon\Carbon::parse($asset->Date)) }}</td>
                                <td>{{ $asset->Health_Status }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                    <table class="table table-bordered mt-3">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Functional Location</th>
                                <th>Reported Date</th>
                                <th>Target Date</th>
                                <th>Switchgear Brand</th>
                                <th>Defect</th>
                                <th>Criticality</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendingAssets as $asset)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $asset->Functional_Location }}</td>
                                <td>{{ $asset->Date }}</td>
                                <td>{{ $asset->Target_Date }}</td>
                                <td>{{ $asset->Switchgear_Brand }}</td>
                                <td>{{ $asset->Defect1 }}</td>
                                <td>{{ $asset->Health_Status }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
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
    var colors = {
        'Clear': 'rgba(75, 192, 192, 0.8)',
        'Minor': 'rgba(255, 159, 64, 0.8)',
        'Major': 'rgba(54, 162, 235, 0.8)',
        'Critical': 'rgba(255, 99, 132, 0.8)'
    };

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
                    color: colors[key]
                };
            })
        }]
    });

    // Handle tab navigation state
    $(document).ready(function() {
        var activeTab = localStorage.getItem('activeTab');
        if (activeTab) {
            $('.nav-link[href="' + activeTab + '"]').tab('show');
        }

        $('.nav-link').on('click', function (e) {
            e.preventDefault();
            $(this).tab('show');
            var tabId = $(this).attr('href');
            localStorage.setItem('activeTab', tabId);
        });
    });
</script>
@endsection
