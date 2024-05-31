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
    <div class="card mt-3 mb-3">
        <div class="card-body">
            <!-- Success and Error messages -->
            <div id="successMessage" class="alert alert-success" style="display:none;"></div>
            <div id="errorMessage" class="alert alert-danger" style="display:none;"></div>
            <div id="dateErrorMessageContainer" class="alert alert-danger" style="display:none;">
                <ul id="dateErrorMessagesList"></ul>
            </div>

            <!-- Form for importing data -->
            <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="input-group mb-3">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="fileInput" name="file" accept=".xlsx">
                        <label class="custom-file-label" for="fileInput">Choose file</label>
                    </div>
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Import Data</button>
                    </div>
                </div>
            </form>

            <!-- Search and filter forms -->
            <form method="GET" action="{{ route('asset_recommendation') }}">
                <div class="form-row align-items-center">
                    <div class="col-sm-4 my-1">
                        <input type="text" class="form-control" name="search" placeholder="Search Functional Location" value="{{ request()->get('search') }}">
                    </div>
                    <div class="col-sm-4 my-1">
                        <input type="text" class="form-control" name="brand" placeholder="Filter by Brand" value="{{ request()->get('brand') }}">
                    </div>
                    <div class="col-auto my-1">
                        <button type="submit" class="btn btn-primary">Search/Filter</button>
                    </div>
                </div>
            </form>

            <!-- Table for displaying assets -->
            <div class="table-responsive">
                <table class="table table-bordered mt-3">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Functional Location</th>
                            <th>Switchgear Brand</th>
                            <th>Substation Name</th>
                            <th>Health Status</th>
                            <th>TEV</th>
                            <th>Hotspot</th>
                            <th>Acknowledgment Status</th>
                            <th>Ongoing Status</th>
                            <th>Completed Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assets as $asset)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a href="#" class="popup-link" data-toggle="modal" data-target="#infoModal" data-info="{{ $asset->Functional_Location }}">{{ $asset->Functional_Location }}</a>
                            </td>
                            <td>{{ $asset->Switchgear_Brand }}</td>
                            <td>{{ $asset->Substation_Name }}</td>
                            <td>
                                @php
                                    $Health_Status = 'Unknown';
                                    if ($asset->TEV > 0 && $asset->TEV < 5)
                                        $Health_Status = 'Minor';
                                    elseif ($asset->TEV >= 5 && $asset->TEV < 10)
                                        $Health_Status = 'Major';
                                    elseif ($asset->TEV >= 10)
                                        $Health_Status = 'Critical';
                                @endphp

                                @if ($Health_Status == 'Minor')
                                    <span class="badge badge-success">Minor</span>
                                @elseif ($Health_Status == 'Major')
                                    <span class="badge badge-warning">Major</span>
                                @elseif ($Health_Status == 'Critical')
                                    <span class="badge badge-danger">Critical</span>
                                @endif
                            </td>
                            <td>{{ $asset->TEV }}</td>
                            <td>{{ $asset->Hotspot }}</td>
                            <td>
                                @if ($asset->acknowledgment_status)
                                    {{ \Carbon\Carbon::parse($asset->acknowledgment_status)->format('Y-m-d H:i:s') }}
                                @else
                                    <form action="{{ route('assets.acknowledge', $asset->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-primary btn-sm">Acknowledge</button>
                                    </form>
                                @endif
                            </td>
                            <td>{{ $asset->ongoing_status }}</td>
                            <td>{{ $asset->completed_status }}</td>
                            <td>
                                @if ($asset->completed_status)
                                    &#10004;
                                @elseif ($asset->acknowledgment_status)
                                    <button class="btn btn-primary btn-sm update-button" data-toggle="modal" data-target="#updateModal{{$asset->id}}">Update</button>
                                @endif
                            </td>
                        </tr>

                        <!-- Update Modal -->
                        <div class="modal fade" id="updateModal{{$asset->id}}" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel{{$asset->id}}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updateModalLabel{{$asset->id}}">Update Asset</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('assets.updateStatus', $asset->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="rectifierName{{$asset->id}}">Rectifier's Name</label>
                                                <input type="text" class="form-control" id="rectifierName{{$asset->id}}" name="rectifierName" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="progressDate{{$asset->id}}">Progress Date</label>
                                                <input type="date" class="form-control" id="progressDate{{$asset->id}}" name="progressDate" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="rectifyStatus{{$asset->id}}">Rectify Status</label>
                                                <select class="form-control" id="rectifyStatus{{$asset->id}}" name="rectifyStatus" required>
                                                    <option value="ongoing">Ongoing</option>
                                                    <option value="completed">Completed</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="infoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="infoModalLabel">Functional Location Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="infoForm">
                    <div class="form-group">
                        <label for="functionalLocation">Functional Location</label>
                        <input type="text" class="form-control" id="functionalLocation" readonly>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function() {
        $('.popup-link').on('click', function() {
            var functionalLocation = $(this).data('info');
            $('#functionalLocation').val(functionalLocation);
        });
    });

    document.getElementById('fileInput').addEventListener('change', function(e) {
        var fileName = e.target.files[0].name;
        var label = document.querySelector('.custom-file-label');
        label.textContent = fileName;
    });

    function showSuccessMessage(message) {
        var successMessage = document.getElementById('successMessage');
        successMessage.innerText = message;
        successMessage.style.display = 'block';
        setTimeout(function() {
            successMessage.style.display = 'none';
        }, 5000);
    }

    function showErrorMessage(message) {
        var errorMessage = document.getElementById('errorMessage');
        errorMessage.innerText = message;
        errorMessage.style.display = 'block';
        setTimeout(function() {
            errorMessage.style.display = 'none';
        }, 5000);
    }

    function showDateErrorMessage(messages) {
        var errorMessageContainer = document.getElementById('dateErrorMessageContainer');
        var errorMessagesList = document.getElementById('dateErrorMessagesList');

        errorMessagesList.innerHTML = '';

        messages.forEach(function(message) {
            var listItem = document.createElement('li');
            listItem.innerText = message;
            errorMessagesList.appendChild(listItem);
        });

        errorMessageContainer.style.display = 'block';

        setTimeout(function() {
            errorMessageContainer.style.display = 'none';
        }, 5000);
    }

    @if ($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            var errors = @json($errors->all());
            showDateErrorMessage(errors);
        });
    @endif

    @if(session('success'))
        showSuccessMessage('{{ session('success') }}');
    @endif

    @if(session('error'))
        showErrorMessage('{{ session('error') }}');
    @endif

    @if(session('file_warning'))
        showErrorMessage('{{ session('file_warning') }}');
    @endif
</script>
@endsection
