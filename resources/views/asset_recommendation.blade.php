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

            @if ($errors->any())
                <div class="alert alert-danger mt-3">
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                </div>
            @endif

            @if (session('success'))
                <div id="successMessage" class="alert alert-success mt-3">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div id="errorMessage" class="alert alert-danger mt-3">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('file_warning'))
                <div id="fileWarningMessage" class="alert alert-warning mt-3">
                    {{ session('file_warning') }}
                </div>
            @endif

            <div id="dateErrorMessageContainer" class="alert alert-danger mt-3" style="display: none;">
                <ul id="dateErrorMessagesList"></ul>
            </div>

            <!-- Table for displaying assets -->
            <table class="table table-bordered mt-3">
                <tr>
                    <th colspan="12">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            Assets
                            <div>
                                <a class="btn btn-danger" href="{{ route('export') }}" style="font-size: 13px;">Export Data</a>
                            </div>
                        </div>
                    </th>
                </tr>
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
                @foreach($assets as $asset)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <a href="#" class="popup-link" data-toggle="modal" data-target="#infoModal" data-info="{{ $asset->Functional_Location }}">{{ $asset->Functional_Location }}</a>
                    </td>
                    <td>{{ $asset->Switchgear_Brand }}</td>
                    <td>{{ $asset->Substation_Name }}</td>
                    <td>
                        @if ($asset->TEV > 0 && $asset->TEV < 5)
                        <?php $Health_Status = 'Minor'; ?>
                        @elseif ($asset->TEV >= 5 && $asset->TEV < 10)
                        <?php $Health_Status = 'Major'; ?>
                        @elseif ($asset->TEV >= 10)
                        <?php $Health_Status = 'Critical'; ?>
                        @else
                        <?php $Health_Status = 'Unknown'; ?>
                        @endif

                        <?php $asset->health_status = $Health_Status;
                        $asset->save(); ?>

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
                                <button type="submit" class="btn btn-primary">Acknowledge</button>
                            </form>
                        @endif
                    </td>
                    <td>{{ $asset->ongoing_status }}</td>
                    <td>{{ $asset->completed_status }}</td>
                    <td>
                        @if ($asset->completed_status)
                            &#10004;
                        @elseif ($asset->acknowledgment_status)
                            <button class="btn btn-primary update-button" data-toggle="modal" data-target="#updateModal{{$asset->id}}">Update</button>
                        @endif
                    </td>
                </tr>

                <!-- Modal for updating asset -->
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
            </table>
            <div class="p-4">
                <button class="flex items-center text-gray-600">
                    <svg class="w-2 h-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-2">Back</span>
                </button>
            </div>
            <div class="d-flex justify-content-center">
                {{ $assets->links() }}
            </div>
        </div>
    </div>
</div>

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







