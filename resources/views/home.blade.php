@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Add margin-top to create space -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Good</h5>
                    <p class="card-text">Total: {{ $goodCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Moderate</h5>
                    <p class="card-text">Total: {{ $moderateCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Critical</h5>
                    <p class="card-text">Total: {{ $criticalCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Google Charts -->
    <div id="chart_div" style="width: 100%; height: 400px;"></div>

    <!-- Load Google Charts library -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        // Load Google Charts
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        // Function to draw the chart
        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Year', 'Sales', 'Expenses'],
                ['2014', 1000, 400],
                ['2015', 1170, 460],
                ['2016', 660, 1120],
                ['2017', 1030, 540]
            ]);

            var options = {
                title: 'Company Performance',
                curveType: 'function',
                legend: { position: 'bottom' },
                explorer: {
                    actions: ['dragToZoom', 'rightClickToReset'],
                    axis: 'horizontal',
                    keepInBounds: true,
                    maxZoomIn: 10.0
                }
            };

            var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        }
    </script>
</div>
@endsection
