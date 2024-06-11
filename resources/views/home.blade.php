@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Filter Form 
    <form method="GET" action="{{ route('asset_recommendation') }}" class="form-inline mb-4">
        <div class="form-group mx-sm-3 mb-2">
            <label for="search" class="sr-only">Functional Location</label>
            <input type="text" class="form-control" id="search" name="search" placeholder="Functional Location" value="{{ request('search') }}">
        </div>
        <div class="form-group mx-sm-3 mb-2">
            <label for="brand" class="sr-only">Brand</label>
            <input type="text" class="form-control" id="brand" name="brand" placeholder="Brand" value="{{ request('brand') }}">
        </div>
        <button type="submit" class="btn btn-primary mb-2">Filter</button>
    </form> -->

    <div class="row">
        <!-- Health Status Pie Chart -->
        <div id="healthStatusChart" class="col-md-4" style="height: 400px;"></div>

        <!-- Key Metrics (Total Switchgear, Average TEV, Average Hotspot, etc.) -->
        <div class="col-md-8">
            <h3>Key Metrics</h3>
            <ul>
                <li>Total Switchgear: {{ $assets->count() }}</li>
                <li>Average TEV: {{ $assets->avg('TEV') }}</li>
                <li>Average Hotspot: {{ $assets->avg('hotspot') }}</li>
            </ul>
        </div>
    </div>
    <div class="row">
        <!-- TEV and Hotspot Trends Line Chart -->
        <div id="tevHotspotTrends" class="col-md-6" style="height: 400px;"></div>

        <!-- TEV and Hotspot Box Plot -->
        <div id="tevHotspotBoxPlot" class="col-md-6" style="height: 400px;"></div>
    </div>
    <div class="row">
        <!-- Defects Heatmap -->
        <div id="defectsHeatmap" class="col-md-12" style="height: 400px;"></div>
    </div>
    <div class="row">
        <!-- Health Status by Location Bar Chart -->
        <div id="healthStatusByLocation" class="col-md-6" style="height: 400px;"></div>

        <!-- Health Status by Brand Bar Chart -->
        <div id="healthStatusByBrand" class="col-md-6" style="height: 400px;"></div>
    </div>
    <div class="row">
        <!-- TEV vs Hotspot Scatter Plot -->
        <div id="tevHotspotScatter" class="col-md-6" style="height: 400px;"></div>

        <!-- Multi-attribute Comparison Radar Chart -->
        <div id="multiAttributeRadar" class="col-md-6" style="height: 400px;"></div>
    </div>
</div>

<!-- Load Highcharts library -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/modules/heatmap.js"></script>

<script>
    // Health Status Pie Chart
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
            }, array_keys($healthStatusData), $healthStatusData)) !!}
        }]
    });

    Highcharts.chart('tevHotspotTrends', {
        chart: {
            type: 'line'
        },
        title: {
            text: 'TEV and Hotspot Trends'
        },
        xAxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'] // Example categories
        },
        yAxis: {
            title: {
                text: 'Values'
            }
        },
        series: [{
            name: 'TEV',
            data: {!! json_encode($assets->pluck('TEV')) !!}
        }, {
            name: 'Hotspot',
            data: {!! json_encode($assets->pluck('hotspot')) !!}
        }]
    });

    // TEV and Hotspot Box Plot
    Highcharts.chart('tevHotspotBoxPlot', {
        chart: {
            type: 'boxplot'
        },
        title: {
            text: 'TEV and Hotspot Distributions'
        },
        xAxis: {
            categories: ['TEV', 'Hotspot']
        },
        yAxis: {
            title: {
                text: 'Values'
            }
        },
        series: [{
            name: 'Observations',
            data: [
                {!! json_encode($assets->pluck('TEV')->toArray()) !!},
                {!! json_encode($assets->pluck('hotspot')->toArray()) !!}
            ]
        }]
    });

    // Defects Heatmap
    Highcharts.chart('defectsHeatmap', {
        chart: {
            type: 'heatmap'
        },
        title: {
            text: 'Defects Severity and Frequency'
        },
        xAxis: {
            categories: ['Location 1', 'Location 2', 'Location 3'] // Example categories
        },
        yAxis: {
            categories: ['Defect 1', 'Defect 2', 'Defect 3'],
            title: null
        },
        colorAxis: {
            min: 0,
            minColor: '#FFFFFF',
            maxColor: '#FF0000'
        },
        series: [{
            name: 'Defects',
            borderWidth: 1,
            data: [
                [0, 0, 5], [0, 1, 10], [0, 2, 15],
                [1, 0, 10], [1, 1, 5], [1, 2, 0],
                [2, 0, 0], [2, 1, 5], [2, 2, 10]
            ],
            dataLabels: {
                enabled: true,
                color: '#000000'
            }
        }]
    });

    // Health Status by Location Bar Chart
    Highcharts.chart('healthStatusByLocation', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Health Status by Location'
        },
        xAxis: {
            categories: {!! json_encode($assets->pluck('Functional_Location')->unique()->toArray()) !!}
        },
        yAxis: {
            title: {
                text: 'Count'
            }
        },
        series: [{
            name: 'Health Status',
            data: {!! json_encode($assets->groupBy('Functional_Location')->map(function ($group) {
                return $group->groupBy('health_status')->map->count();
            })) !!}
        }]
    });

    // Health Status by Brand Bar Chart
    Highcharts.chart('healthStatusByBrand', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Health Status by Brand'
        },

        xAxis: {
            categories: {!! json_encode($assets->pluck('Switchgear_Brand')->unique()->toArray()) !!}
        },
        yAxis: {
            title: {
                text: 'Count'
            }
        },
        series: [{
            name: 'Health Status',
            data: {!! json_encode($assets->groupBy('Switchgear_Brand')->map(function ($group) {
                return $group->groupBy('health_status')->map->count();
            })) !!}
        }]
    });

    Highcharts.chart('tevHotspotScatter', {
        chart: {
            type: 'scatter',
            zoomType: 'xy'
        },
        title: {
            text: 'TEV vs Hotspot'
        },
        xAxis: {
            title: {
                enabled: true,
                text: 'TEV'
            },
            startOnTick: true,
            endOnTick: true,
            showLastLabel: true
        },
        yAxis: {
            title: {
                text: 'Hotspot'
            }
        },
        series: [{
            name: 'TEV vs Hotspot',
            data: {!! json_encode($tevHotspotData) !!}
        }]
    });

    Highcharts.chart('multiAttributeRadar', {
        chart: {
            polar: true,
            type: 'line'
        },
        title: {
            text: 'Multi-attribute Comparison'
        },
        xAxis: {
            categories: ['Attribute 1', 'Attribute 2', 'Attribute 3', 'Attribute 4', 'Attribute 5']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Value'
            }
        },
        series: [{
            name: 'Asset 1',
            data: [5, 10, 15, 20, 25]
        }, {
            name: 'Asset 2',
            data: [15, 20, 10, 30, 25]
        }]
    });
</script>
@endsection
