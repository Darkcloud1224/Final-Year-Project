@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Approval Log</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>User</th>
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
@endsection
