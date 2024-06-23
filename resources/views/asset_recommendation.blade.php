@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Roboto', sans-serif;
    }
    .table {
        table-layout: auto; 
        width: 100%;
    }
    .table th, .table td {
        font-size: 0.9rem;
        white-space: nowrap; 
        overflow: hidden;
        text-overflow: ellipsis;
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

    .sortable:after {
        content: '\25b2'; 
        margin-left: 5px;
        position: absolute;
        right: 0;
    }

    .sortable.desc:after {
        content: '\25bc'; 
    }

    .health-status-header {
        white-space: nowrap; 
    }
</style>
@php
use Carbon\Carbon;
@endphp

<div class="container">
    <div class="card mt-3 mb-3">
        <div class="card-body">
            <div id="successMessage" class="alert alert-success" style="display:none;"></div>
            <div id="errorMessage" class="alert alert-danger" style="display:none;"></div>
            <div id="dateErrorMessageContainer" class="alert alert-danger" style="display:none;">
                <ul id="dateErrorMessagesList"></ul>
            </div>

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
            <small class="form-text text-muted">
                <a href="{{ asset('template.xlsx') }}" download>Use this template to fill in the switchgear record</a>
            </small>

            <form method="GET" action="{{ route('asset_recommendation') }}">
                <div class="form-row align-items-center">
                    <div class="col-sm-4 my-1">
                        <input type="text" class="form-control" name="search" placeholder="Search Functional Location" value="{{ request()->get('search') }}">
                    </div>
                    <div class="col-auto my-1">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                    <div class="col-auto my-1">
                        <a href="{{ route('export') }}" class="btn btn-success">Export Data</a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered mt-3">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Functional Location</th>
                            <th>Reported Date</th>
                            <th>Target Date</th>
                            <th>Switchgear Brand</th>
                            <th>Substation Name</th>
                            <th class="sortable health-status-header" data-column="health_status" data-order="asc">Criticality</th>
                            <th>Defect</th>
                            <th>TEV</th>
                            <th>Hotspot</th>
                            <th>Acknowledgment Status</th>
                            <th>Ongoing Status</th>
                            <th>Completed Status / Date</th>
                            <th>Update Status</th>
                            <th>Average</th>  
                            <th>Pending Days</th> 
                            <th>Actions</th>
 
                        </tr>
                    </thead>
                    <tbody id="assetTableBody">
                        @foreach($assets as $asset)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a href="#" class="popup-link" data-toggle="modal" data-target="#infoModal" data-info="{{ $asset->Functional_Location }}" data-defect="{{ $asset->Defect }}" data-defect1="{{ $asset->Defect1 }}" data-defect2="{{ $asset->Defect2 }}">{{ $asset->Functional_Location }}</a>
                            </td>
                            <td>{{ Carbon::parse($asset->Date)->format('d-m-Y') }}</td> 
                            <td>{{ $asset->Target_Date}}</td>
                            <td>{{ $asset->Switchgear_Brand }}</td>
                            <td>{{ $asset->Substation_Name }}</td>
                            <td>
                                @php
                                    $Health_Status = 'Unknown';
                            
                                    if (!$asset->completed_status && $asset->Target_Date) {
                                        $targetDate = \Carbon\Carbon::parse($asset->Target_Date);
                                        $pending_days = $targetDate->isPast() ? \Carbon\Carbon::now()->diffInDays($targetDate) : 0;
                                    } else {
                                        $pending_days = 0;
                                    }
                            
                                    if ($asset->completed_status) {
                                        $Health_Status = 'Clear';
                                    } elseif (!$asset->completed_status && $pending_days > 90) {
                                        $Health_Status = 'Critical';
                                    } elseif (!$asset->completed_status && $pending_days >= 31 && $pending_days <= 89) {
                                        $Health_Status = 'Major';
                                    } elseif (!$asset->completed_status && $pending_days >= 0 && $pending_days <= 30) {
                                        $Health_Status = 'Minor';
                                    }
                            
                                    $asset->Health_Status = $Health_Status;
                                    $asset->save();
                                @endphp
                            
                                @if ($Health_Status == 'Clear')
                                    <span class="badge badge-primary">Clear</span>
                                @elseif ($Health_Status == 'Minor')
                                    <span class="badge badge-success">Minor</span>
                                @elseif ($Health_Status == 'Major')
                                    <span class="badge badge-warning">Major</span>
                                @elseif ($Health_Status == 'Critical')
                                    <span class="badge badge-danger">Critical</span>
                                @endif
                            </td>
                            <td>{{ $asset->Defect1 }}</td>
                            <td>{{ $asset->TEV }}</td>
                            <td>{{ $asset->Hotspot }}</td>
                            <td>
                                @if ($asset->completed_status&& is_null($asset->acknowledgment_status))
                                    N/A
                                @elseif ($asset->acknowledgment_status)
                                    {{ \Carbon\Carbon::parse($asset->acknowledgment_status)->format('Y-m-d H:i:s') }}
                                @else
                                    <form action="{{ route('assets.acknowledge', $asset->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-primary btn-sm">Acknowledge</button>
                                    </form>
                                @endif
                            </td>
                            <td>
                                @if ($asset->completed_status && is_null($asset->ongoing_status))
                                    N/A
                                @else
                                    {{ $asset->ongoing_status }}
                                @endif
                            </td>
                            <td>{{ $asset->completed_status }}</td>
                            <td>
                                @if ($asset->completed_status)
                                    &#10004;
                                @elseif ($asset->acknowledgment_status)
                                    <button class="btn btn-primary btn-sm update-button" data-toggle="modal" data-target="#updateModal{{$asset->id}}">Update</button>
                                @endif
                            </td>
                            <td>{{ $asset->Average ?? '0'}} days</td>  
                            <td>{{ $asset->PendingDays ?? '0' }} days</td>  
                            <td>
                                @if ($asset->delete_request)
                                    <button class="btn btn-outline-info btn-sm" disabled>Delete Request Submitted</button>
                                @else
                                    <button class="btn btn-danger delete-button" data-toggle="modal" data-target="#deleteModal{{$asset->id}}" data-asset-id="{{ $asset->id }}">
                                        Delete
                                    </button>
                                @endif
                            </td>
                        </tr>

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
                                        <<div class="modal-body">
                                            <div class="form-group">
                                                <label for="rectifierName{{$asset->id}}">Rectifier's Name</label>
                                                <select class="form-control" id="rectifierName{{$asset->id}}" name="rectifierName" required>
                                                    @foreach($users as $user)
                                                    <option value="{{ $user->name }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
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

                        <div class="modal fade" id="deleteModal{{$asset->id}}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{$asset->id}}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('assets.delete', ['id' => $asset->id]) }}">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel{{$asset->id}}">Delete Asset</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="reason{{$asset->id}}">Reason for deletion:</label>
                                                <textarea class="form-control" id="reason{{$asset->id}}" name="reason" rows="3" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-danger">Yes, Delete</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    {{ $assets->links() }}
                </div>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="infoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="infoModalLabel">Asset Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="assetTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">General Info</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="history-tab" data-toggle="tab" href="#history" role="tab" aria-controls="history" aria-selected="false">History</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="defect-tab" data-toggle="tab" href="#defect" role="tab" aria-controls="defect" aria-selected="false">Defect Info</a>
                    </li>
                </ul>
                <div class="tab-content" id="assetTabsContent">
                    <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                        <form id="infoForm">
                            <div class="form-group">
                                <label for="functionalLocation">Functional Location</label>
                                <input type="text" class="form-control" id="functionalLocation" readonly>
                            </div>
                            <div class="form-group">
                                <label for="defect">Defect</label>
                                <input type="text" class="form-control" id="defect" readonly>
                            </div>
                            <div class="form-group">
                                <label for="defect1">Defect 1</label>
                                <input type="text" class="form-control" id="defect1" readonly>
                            </div>
                            <div class="form-group">
                                <label for="defect2">Defect 2</label>
                                <input type="text" class="form-control" id="defect2" readonly>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                        <p>This tab will contain history information.</p>
                    </div>
                    <div class="tab-pane fade" id="defect" role="tabpanel" aria-labelledby="defect-tab">
                        <form id="defectForm">
                            <div class="form-group">
                                <label for="defectDetails">Defect Details</label>
                                <textarea class="form-control" id="defectDetails" rows="3" readonly></textarea>
                            </div>
                            <div class="form-group">
                                <label for="defectSeverity">Defect Severity</label>
                                <input type="text" class="form-control" id="defectSeverity" readonly>
                            </div>
                            <div class="form-group">
                                <label for="defectReportedBy">Reported By</label>
                                <input type="text" class="form-control" id="defectReportedBy" readonly>
                            </div>
                        </form>
                    </div>
                </div>
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
    $('.delete-button').on('click', function() {
        var assetId = $(this).data('asset-id');
        console.log("Delete button clicked!");
        console.log("Asset ID:", assetId);
        var modalSelector = '#deleteModal' + assetId;
        console.log("Modal selector:", modalSelector);
        $(modalSelector).modal('show');
    });

    $(document).on('click', '.confirm-delete', function() {
        var assetId = $(this).data('asset-id');
        var reason = $('#reason' + assetId).val(); 

        $.ajax({
            url: "{{ route('assets.delete', ['id' => ':asset_id']) }}".replace(':asset_id', assetId),
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                reason: reason
            },
            success: function(response) {
                $('#deleteModal' + assetId).modal('hide');
                location.reload(); 
            },
            error: function(xhr) {
                console.error('Error deleting asset:', xhr);
            }
        });
    });

    $('.popup-link').on('click', function() {
        var functionalLocation = $(this).data('info');
        $('#functionalLocation').val(functionalLocation);
        var defect = $(this).data('defect');
        $('#defect').val(defect);
        var defect1 = $(this).data('defect1');
        $('#defect1').val(defect1);
        var defect2 = $(this).data('defect2');
        $('#defect2').val(defect2);
    });

    $('.sortable').on('click', function() {
        var column = $(this).data('column');
        var order = $(this).data('order');
        var newOrder = order === 'asc' ? 'desc' : 'asc';
        $(this).data('order', newOrder);
        sortTable(column, newOrder);
        $(this).toggleClass('desc', newOrder === 'desc');
    });

    function sortTable(column, order) {
        var rows = $('#assetTableBody tr').get();
        rows.sort(function(a, b) {
            var A = $(a).children('td').eq(4).text().toUpperCase();
            var B = $(b).children('td').eq(4).text().toUpperCase();

            if (order === 'asc') {
                return A > B ? 1 : (A < B ? -1 : 0);
            } else {
                return A < B ? 1 : (A > B ? -1 : 0);
            }
        });

        $.each(rows, function(index, row) {
            $('#assetTableBody').append(row);
        });
    }

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
});
</script>

@endsection
