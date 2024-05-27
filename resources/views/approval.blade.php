@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center mt-3 mb-3">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Pending Approvals</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($pendingApprovals->isEmpty())
                        <p>No pending approvals.</p>
                    @else
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Functional Location</th>
                                    <th>Switchgear Brand</th>
                                    <th>Substation Name</th>
                                    <th>TEV</th>
                                    <th>Hotspot</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendingApprovals as $approval)
                                    <tr>
                                        <td>{{ $approval->Functional_Location }}</td>
                                        <td>{{ $approval->Switchgear_Brand }}</td>
                                        <td>{{ $approval->Substation_Name }}</td>
                                        <td>{{ $approval->TEV }}</td>
                                        <td>{{ $approval->Hotspot }}</td>
                                        <td>
                                            <form action="{{ route('approval.approve', $approval->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success">Approve</button>
                                            </form>
                                            <form action="{{ route('approval.reject', $approval->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-danger">Reject</button>
                                            </form>
                                        </td>
                                        
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
