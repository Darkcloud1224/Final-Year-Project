@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center mt-3 mb-3">
            <div class="col-md-10">
                <h2>Report Log</h2>
                <table class="table">
                    <thead>
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
@endsection
