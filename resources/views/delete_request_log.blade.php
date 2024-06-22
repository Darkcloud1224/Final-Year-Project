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
                            <th>Target Date</th>
                            <th>Reason</th>
                            <th>Requested</th>
                            <th>Approved</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deleterequestlog as $log)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $log->Date }}</td>
                            <td>{{ $log->Functional_Location }}</td>
                            <td>{{ $log->Date }}</td>
                            <td>{{ $log->reason }}</td>
                            <td>{{ $log->User_Name }}</td>
                            <td>{{ $log->Approved }}</td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
