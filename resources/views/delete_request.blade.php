@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card mt-3">
        <div class="card-body">
            <h5 class="card-title">Pending Delete Requests</h5>

            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered mt-3">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>User Requested</th>
                            <th>Functional Location</th>
                            <th>Reason</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingDeleteApprovals as $deleteRequest)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $deleteRequest->User_Name }}</td>
                            <td>{{ $deleteRequest->Functional_Location }}</td>
                            <td>{{ $deleteRequest->reason }}</td>
                            <td>
                                <form action="{{ route('delete_requests.approve', $deleteRequest->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                </form>
                                <form action="{{ route('delete_requests.reject', $deleteRequest->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
