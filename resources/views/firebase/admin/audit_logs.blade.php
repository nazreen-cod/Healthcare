@extends('firebase.admin.layout')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Audit Logs</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('firebase.admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Audit Logs</li>
        </ol>

        <!-- Filter Form -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-filter me-1"></i>
                Filter Logs
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('firebase.admin.audit_logs') }}">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Staff Type</label>
                            <select name="staff_type" class="form-select">
                                <option value="all" {{ $staffType === 'all' ? 'selected' : '' }}>All Staff Types</option>
                                <option value="doctor" {{ $staffType === 'doctor' ? 'selected' : '' }}>Doctors</option>
                                <option value="nurse" {{ $staffType === 'nurse' ? 'selected' : '' }}>Nurses</option>
                                <option value="pharmacist" {{ $staffType === 'pharmacist' ? 'selected' : '' }}>Pharmacists</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Action</label>
                            <select name="action" class="form-select">
                                <option value="all" {{ $action === 'all' ? 'selected' : '' }}>All Actions</option>
                                <option value="create" {{ $action === 'create' ? 'selected' : '' }}>Create</option>
                                <option value="update" {{ $action === 'update' ? 'selected' : '' }}>Update</option>
                                <option value="delete" {{ $action === 'delete' ? 'selected' : '' }}>Delete</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                        </div>
                    </div>
                    <div class="mb-0">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i> Apply Filters
                        </button>
                        <a href="{{ route('firebase.admin.audit_logs') }}" class="btn btn-secondary">
                            <i class="fas fa-undo me-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Audit Logs Table -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-history me-1"></i>
                Staff Change History
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="auditTable">
                        <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Action</th>
                            <th>Staff Type</th>
                            <th>Staff ID</th>
                            <th>Admin</th>
                            <th>Details</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($logs as $id => $log)
                            <tr>
                                <td>{{ $log['timestamp'] ?? 'N/A' }}</td>
                                <td>
                                    @if($log['action'] === 'create')
                                        <span class="badge bg-success">Create</span>
                                    @elseif($log['action'] === 'update')
                                        <span class="badge bg-primary">Update</span>
                                    @elseif($log['action'] === 'delete')
                                        <span class="badge bg-danger">Delete</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $log['action'] }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log['staff_type'] === 'doctor')
                                        <span class="badge bg-info">Doctor</span>
                                    @elseif($log['staff_type'] === 'nurse')
                                        <span class="badge bg-warning">Nurse</span>
                                    @elseif($log['staff_type'] === 'pharmacist')
                                        <span class="badge bg-secondary">Pharmacist</span>
                                    @else
                                        {{ $log['staff_type'] }}
                                    @endif
                                </td>
                                <td>{{ $log['staff_id'] }}</td>
                                <td>{{ $log['admin_name'] }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#detailsModal{{ $id }}">
                                        View Changes
                                    </button>

                                    <!-- Modal for details -->
                                    <div class="modal fade" id="detailsModal{{ $id }}" tabindex="-1" aria-labelledby="detailsModalLabel{{ $id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="detailsModalLabel{{ $id }}">
                                                        {{ ucfirst($log['action']) }} {{ ucfirst($log['staff_type']) }} Details
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- For updates, show changes -->
                                                    @if($log['action'] === 'update' && isset($log['changes']))
                                                        <h6>Changes Made:</h6>
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-bordered">
                                                                <thead>
                                                                <tr>
                                                                    <th>Field</th>
                                                                    <th>Old Value</th>
                                                                    <th>New Value</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @foreach($log['changes'] as $field => $change)
                                                                    @if(isset($change['changed']) && $change['changed'])
                                                                        <tr>
                                                                            <td>{{ $field }}</td>
                                                                            <td>{{ $change['from'] ?? 'N/A' }}</td>
                                                                            <td>{{ $change['to'] ?? 'N/A' }}</td>
                                                                        </tr>
                                                                    @endif
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <!-- For create, show created data -->
                                                    @elseif($log['action'] === 'create' && isset($log['created_data']))
                                                        <h6>Created {{ ucfirst($log['staff_type']) }} Data:</h6>
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-bordered">
                                                                <thead>
                                                                <tr>
                                                                    <th>Field</th>
                                                                    <th>Value</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @foreach($log['created_data'] as $field => $value)
                                                                    <tr>
                                                                        <td>{{ $field }}</td>
                                                                        <td>{{ $value }}</td>
                                                                    </tr>
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <!-- For delete, show deleted data -->
                                                    @elseif($log['action'] === 'delete' && isset($log['deleted_data']))
                                                        <h6>Deleted {{ ucfirst($log['staff_type']) }} Data:</h6>
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-bordered">
                                                                <thead>
                                                                <tr>
                                                                    <th>Field</th>
                                                                    <th>Value</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @foreach($log['deleted_data'] as $field => $value)
                                                                    <tr>
                                                                        <td>{{ $field }}</td>
                                                                        <td>{{ $value }}</td>
                                                                    </tr>
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @else
                                                        <p>No detailed information available for this action.</p>
                                                    @endif

                                                    <hr>
                                                    <h6>Additional Information:</h6>
                                                    <ul class="list-group list-group-flush">
                                                        <li class="list-group-item"><strong>IP Address:</strong> {{ $log['ip_address'] ?? 'Not recorded' }}</li>
                                                        <li class="list-group-item"><strong>User Agent:</strong> {{ $log['user_agent'] ?? 'Not recorded' }}</li>
                                                    </ul>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No audit logs found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#auditTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 25
            });
        });
    </script>
@endsection
