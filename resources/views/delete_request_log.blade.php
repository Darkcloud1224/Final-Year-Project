@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card mt-3">
        <div class="card-body">

            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered mt-3">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Date</th>
                            <th>Functional Location</th>
                            <th>Switchgear Brand</th>
                            <th>Substation Name</th>
                            <th>TEV</th>
                            <th>Hotspot</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deleterequestlog as $log)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $log->Date }}</td>
                            <td>{{ $log->Functional_Location }}</td>
                            <td>{{ $log->Switchgear_Brand }}</td>
                            <td>{{ $log->Substation_Name }}</td>
                            <td>{{ $log->TEV }}</td>
                            <td>{{ $log->Hotspot }}</td>
                            <td>{{ $log->reason }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
