@extends('layouts.app')

@section('content')
<style>
    body {
        font-family: 'Roboto', sans-serif;
    }

    .table th, .table td {
        font-size: 0.9rem;
    }

    .btn {
        font-size: 0.9rem;
    }

    .modal-title, .modal-body label {
        font-size: 1rem;
    }

    .custom-file-label, .alert {
        font-size: 0.9rem;
    }

    /* Custom CSS for the Actions column */
    .actions-column {
        width: 180px; /* Adjust the width as needed */
    }
</style>

<div class="container">
    <div class="row justify-content-center mt-3 mb-5">
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
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Functional Location</th>
                                    <th>Switchgear Brand</th>
                                    <th>Substation Name</th>
                                    <th>TEV</th>
                                    <th>Hotspot</th>
                                    <th class="actions-column">Actions</th> <!-- Apply custom class -->
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
