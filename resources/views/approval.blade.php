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

    .actions-column {
        width: 180px; 
    }

    .no-wrap {
        white-space: nowrap;
    }
</style>

<div class="container">
    <div class="row justify-content-center mt-3 mb-5">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    <h4>Approval</h4>
                    <br></br>
                    @if ($pendingApprovals->isEmpty())
                        <p>No pending approvals.</p>
                    @else
                    <table class="table table-bordered w-100">
                        <thead class="thead-dark">
                                <tr>
                                    <th class="no-wrap">Reported Date</th>
                                    <th>Functional Location</th>
                                    <th>Switchgear Brand</th>
                                    <th>Substation Name</th>
                                    <th>TEV</th>
                                    <th>Hotspot</th>
                                    <th class="no-wrap">Target Date</th>
                                    <th>Completed Date</th>
                                    <th class="actions-column">Actions</th> 
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendingApprovals as $approval)
                                    <tr>
                                        <td>{{ $approval->Date }}</td>
                                        <td>{{ $approval->Functional_Location }}</td>
                                        <td>{{ $approval->Switchgear_Brand }}</td>
                                        <td>{{ $approval->Substation_Name }}</td>
                                        <td>{{ $approval->TEV }}</td>
                                        <td>{{ $approval->Hotspot }}</td>
                                        <td>{{ $approval->Target_Date }}</td>
                                        <td>{{ $approval->completed_status }}</td>
                                        <td>
                                            <button type="button" class="btn btn-success btn-approve" data-action="{{ route('approval.approve', $approval->id) }}">Approve</button>
                                            <button type="button" class="btn btn-danger btn-reject" data-action="{{ route('approval.reject', $approval->id) }}">Reject</button>
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

<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Rejection Reason</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejectionReason">Reason for rejection:</label>
                        <textarea class="form-control" id="rejectionReason" name="reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="approveForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">Approval Reason</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="approvalReason">Reason for approval:</label>
                        <textarea class="form-control" id="approvalReason" name="reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function() {
        var rejectFormAction;
        var approveFormAction;

        $('.btn-reject').on('click', function(event) {
            event.preventDefault();
            var button = $(this);
            rejectFormAction = button.data('action');
            $('#rejectModal').modal('show');
        });

        $('.btn-approve').on('click', function(event) {
            event.preventDefault();
            var button = $(this);
            approveFormAction = button.data('action');
            $('#approveModal').modal('show');
        });

        $('#rejectForm').on('submit', function(event) {
            event.preventDefault();
            var reason = $('#rejectionReason').val();
            if (reason.trim() === '') {
                alert('Please provide a reason for rejection.');
                return;
            }
            var form = $(this);
            form.attr('action', rejectFormAction);
            $('<input>').attr({
                type: 'hidden',
                name: 'reason',
                value: reason
            }).appendTo(form);
            form.off('submit').submit();
        });

        $('#approveForm').on('submit', function(event) {
            event.preventDefault();
            var reason = $('#approvalReason').val();
            if (reason.trim() === '') {
                alert('Please provide a reason for approval.');
                return;
            }
            var form = $(this);
            form.attr('action', approveFormAction);
            $('<input>').attr({
                type: 'hidden',
                name: 'reason',
                value: reason
            }).appendTo(form);
            form.off('submit').submit();
        });

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
    });
</script>

@endsection
