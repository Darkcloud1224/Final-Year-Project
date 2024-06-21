@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Roboto', sans-serif;
    }

    .table th, .table td {
        font-size: 0.9rem; 
    }

    .table th {
        background-color: #343a40; 
        color: #ffffff;
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
</style>

<div class="container">
    <div class="row justify-content-center mt-3 mb-3">
        <div class="col-md-10">
            <div class="table-responsive">
                <table class="table table-bordered mt-3">
                    <h4>Approval Log</h4>
                    <thead class="thead-dark">
                        <tr>
                            <th>Approved By</th>
                            <th>Functional Location</th>
                            <th>Action</th>
                            <th>Reasons</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($approvalLogs as $approvalLog)
                            <tr>
                                <td>{{ $approvalLog->User_Name }}</td>
                                <td>{{ $approvalLog->Asset_Name }}</td>
                                <td>{{ $approvalLog->Recitified_Action }}</td>
                                <td>{{ $approvalLog->reasons }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
@endsection
