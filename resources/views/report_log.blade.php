@extends('layouts.app')

@section('content')
<!-- Include Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">


<style>
    body {
        font-family: 'Roboto', sans-serif;
    }

    .table th, .table td {
        font-size: 0.9rem; /* Smaller text size for table cells */
    }

    .table th {
        background-color: #343a40; /* Dark header background */
        color: #ffffff; /* White text for header */
    }

    .btn {
        font-size: 0.9rem; /* Smaller text size for buttons */
    }

    .modal-title, .modal-body label {
        font-size: 1rem; /* Consistent text size in modals */
    }

    .custom-file-label, .alert {
        font-size: 0.9rem; /* Adjust font size for file input label and alerts */
    }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<div class="container">
    <div class="row justify-content-center mt-3 mb-3">
        <div class="col-md-10">
            <div class="table-responsive">
                <h4>Report Log</h4>

                <table class="table table-bordered mt-3">
                    <thead class="thead-dark">
                        <tr>
                            <th>User</th>
                            <th>File Name</th>
                            <th>Uploaded At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportLogs as $reportLog)
                            <tr>
                                <td>{{ $reportLog->user_name }}</td>
                                <td>{{ $reportLog->file_name }}</td>
                                <td>{{ $reportLog->uploaded_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
