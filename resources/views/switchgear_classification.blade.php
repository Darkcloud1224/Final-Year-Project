@extends('layouts.app')

@section('content')

<style>
    .no-wrap {
        white-space: nowrap;
    }
</style>

<div class="container">
    <br></br>
    <button class="btn btn-primary print-button" onclick="printPage()">Print</button>
    <br></br>
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
            <h3>Assets</h3>
            <table class="table table-bordered mt-3" id="assetsTable">
                <thead class="thead-dark">
                    <tr>
                        <th class="no-wrap">Functional Location</th>
                        <th class="no-wrap">Switchgear Brand</th>
                        <th class="no-wrap">Report Date</th>
                        <th class="no-wrap">Target Date</th>
                        <th class="no-wrap">Defect</th>
                        <th class="no-wrap">Sub Defect</th>
                        <th>Criticality</th>
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
                        <td>{{ $asset->Health_Status }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>


<script>
    var rectifiedCount = {{ $rectifiedCount }};
    var notRectifiedCount = {{ $notRectifiedCount }};
    var allAssets = @json($allAssets);
    var rectifiedAssets = @json($rectifiedAssets);

    var assetsTable = $('#assetsTable').DataTable({
        data: @json($notRectifiedAssets), 
        columns: [
            { data: 'Functional_Location' },
            { data: 'Switchgear_Brand' },
            { data: 'Date' },
            { data: 'Target_Date' },
            { data: 'Defect' },
            { data: 'Defect1' },
            { data: 'Health_Status' }
        ]
        
    });

    Highcharts.chart('rectifiedChartContainer', {
        chart: {
            type: 'pie'
        },
        title: {
            text: 'Rectified vs Not Rectified Switchgear'
        },
        plotOptions: {
            series: {
                cursor: 'pointer',
                point: {
                    events: {
                        click: function () {
                            if (this.name === 'Rectified') {
                                assetsTable.clear().rows.add(rectifiedAssets).draw();
                            } else if (this.name === 'Not Rectified') {
                                assetsTable.clear().rows.add(@json($notRectifiedAssets)).draw();
                            }
                        }
                    }
                }
            }
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

    Highcharts.chart('defectsChartContainer', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Defects in Assets'
        },
        xAxis: {
            categories: @json(array_keys($defectTypes)), 
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
            data: @json(array_values($defectTypes)), 
            color: 'rgba(255, 99, 132, 0.8)'
        }],
        plotOptions: {
            series: {
                cursor: 'pointer',
                point: {
                    events: {
                        click: function () {
                            var selectedDefect = this.category;
                            var filteredAssets = allAssets.filter(function (asset) {
                                return asset.Defect1.trim().toUpperCase() === selectedDefect.toUpperCase();
                            });
                            assetsTable.clear().rows.add(filteredAssets).draw();
                        }
                    }
                }
            }
        }
    });

    function printPage() {
        window.print();
    }
</script>
@endsection
