@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card mt-3 mb-3">
        <div class="card-body">
            <!-- Form for importing data -->
            <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="input-group mb-3">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="fileInput" name="file" accept=".csv">
                        <label class="custom-file-label" for="fileInput">Choose file</label>
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">Import Data</button>
            </form>
            
            <!-- Table for displaying assets -->
            <table class="table table-bordered mt-3">
                <!-- Table headers -->
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
                
                <!-- Asset rows -->
                @foreach($assets as $asset)
                <tr>
                    <!-- Asset data columns -->
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <!-- Trigger modal on click -->
                        <a href="#" class="popup-link" data-toggle="modal" data-target="#infoModal" data-info="{{ $asset->Functional_Location }}">{{ $asset->Functional_Location }}</a>
                    </td>
                    <td>{{ $asset->Switchgear_Brand }}</td>
                    <td>{{ $asset->Substation_Name }}</td>
                    <td>
                        <!-- Health Status badge -->
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
                        <!-- Acknowledgment Status -->
                        @if ($asset->acknowledgment_status)
                            {{ \Carbon\Carbon::parse($asset->acknowledgment_status)->format('Y-m-d H:i:s') }}
                        @else
                            <form action="{{ route('assets.acknowledge', $asset->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary">Acknowledge</button>
                            </form>
                        @endif
                    </td>
                    <td>{{ $asset->ongoing_status }}</td>
                    <td>{{ $asset->completed_status }}</td>
                    <td>
                        <!-- Update button -->
                        @if ($asset->completed_status)
                            &#10004;
                        @elseif ($asset->acknowledgment_status)
                            <button class="btn btn-primary update-button" data-toggle="modal" data-target="#updateModal{{$asset->id}}">Update</button>
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
                                    <!-- Update form fields -->
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
            </table>
        </div>
    </div>
</div>

<!-- Single Modal for displaying Functional Location Information -->
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
                    <!-- Add more form fields as needed -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery from a CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>



<script>
    $(document).ready(function() {
        $('.popup-link').on('click', function() {
            var functionalLocation = $(this).data('info');
            console.log("Functional Location:", functionalLocation); // Debug log
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
