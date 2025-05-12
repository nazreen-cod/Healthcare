@extends('firebase.layoutnurse')

@section('title', 'Manage Appointments')

@section('content')
    <style>
        .appointments-container {
            padding: 20px 0;
        }

        .page-header {
            font-family: 'Orbitron', sans-serif;
            color: var(--success);
            margin-bottom: 1rem;
            position: relative;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
            text-shadow: 0 0 15px rgba(0, 255, 157, 0.3);
        }

        .page-header::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 60px;
            height: 2px;
            background: linear-gradient(to right, var(--success), transparent);
        }

        .appointment-tabs {
            background: rgba(16, 25, 36, 0.7);
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid rgba(0, 255, 157, 0.2);
        }

        .appointment-tabs .nav-link {
            color: #e1e1e1;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            margin-right: 5px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .appointment-tabs .nav-link:hover {
            background: rgba(0, 255, 157, 0.1);
        }

        .appointment-tabs .nav-link.active {
            background: rgba(0, 255, 157, 0.2);
            color: var(--success);
            font-weight: 600;
        }

        .appointment-card {
            background: rgba(16, 25, 36, 0.7);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid rgba(0, 255, 157, 0.2);
            position: relative;
            overflow: hidden;
        }

        .appointment-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 3px;
            height: 100%;
        }

        .appointment-card.pending::before {
            background: linear-gradient(to bottom, #ffc107, transparent);
        }

        .appointment-card.accepted::before {
            background: linear-gradient(to bottom, var(--success), transparent);
        }

        .appointment-card.declined::before,
        .appointment-card.cancelled::before {
            background: linear-gradient(to bottom, #dc3545, transparent);
        }

        .appointment-card.rescheduled::before {
            background: linear-gradient(to bottom, #6f42c1, transparent);
        }

        .appointment-card.completed::before {
            background: linear-gradient(to bottom, #0d6efd, transparent);
        }

        .appointment-time {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--success);
        }

        .appointment-date {
            font-size: 0.9rem;
            color: #e1e1e1; /* Changed from #a0a0a0 for better visibility */
            margin-bottom: 10px;
        }

        .patient-info {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
            color: #e1e1e1; /* Added explicit color for better visibility */
        }

        .doctor-info {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            color: #e1e1e1; /* Added explicit color for better visibility */
        }

        .info-icon {
            font-size: 1.1rem;
            color: var(--success);
            width: 25px;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 500;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .badge-pending {
            background: rgba(255, 193, 7, 0.25); /* Increased opacity */
            color: #ffcb3d; /* Brightened color */
            border: 1px solid rgba(255, 193, 7, 0.5); /* Increased border opacity */
        }

        .badge-accepted {
            background: rgba(0, 255, 157, 0.25); /* Increased opacity */
            color: #00ff9d; /* Brightened color */
            border: 1px solid rgba(0, 255, 157, 0.5); /* Increased border opacity */
        }

        .badge-declined, .badge-cancelled {
            background: rgba(220, 53, 69, 0.25); /* Increased opacity */
            color: #ff647a; /* Brightened color */
            border: 1px solid rgba(220, 53, 69, 0.5); /* Increased border opacity */
        }

        .badge-rescheduled {
            background: rgba(111, 66, 193, 0.25); /* Increased opacity */
            color: #a47df8; /* Brightened color */
            border: 1px solid rgba(111, 66, 193, 0.5); /* Increased border opacity */
        }

        .badge-completed {
            background: rgba(13, 110, 253, 0.25); /* Increased opacity */
            color: #5a9eff; /* Brightened color */
            border: 1px solid rgba(13, 110, 253, 0.5); /* Increased border opacity */
        }

        .action-btn {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s;
            border: 1px solid;
            background: rgba(0, 0, 0, 0.3);
            margin-right: 5px;
        }

        .btn-accept {
            color: #00ff9d; /* Brightened color */
            border-color: rgba(0, 255, 157, 0.5);
        }

        .btn-decline {
            color: #ff647a; /* Brightened color */
            border-color: rgba(220, 53, 69, 0.5);
        }

        .btn-reschedule {
            color: #a47df8; /* Brightened color */
            border-color: rgba(111, 66, 193, 0.5);
        }

        .btn-complete {
            color: #5a9eff; /* Brightened color */
            border-color: rgba(13, 110, 253, 0.5);
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            background: rgba(16, 25, 36, 0.5);
            border-radius: 12px;
            border: 1px dashed rgba(0, 255, 157, 0.3);
        }

        .empty-icon {
            font-size: 3rem;
            color: var(--success);
            margin-bottom: 15px;
            opacity: 0.6;
        }

        /* Improved visibility for empty states */
        .empty-state h4 {
            color: #e1e1e1;
            margin-bottom: 10px;
        }

        .empty-state p.text-muted {
            color: #b8b8b8 !important; /* Override Bootstrap's text-muted */
        }

        /* Fix modal text colors */
        .modal-body p, .modal-body ul li {
            color: #e1e1e1;
        }

        .modal-body strong {
            color: #ffffff;
        }

        /* Fix for small text with reasons */
        .small.text-muted {
            color: #b8b8b8 !important;
        }

        /* Better visibility for form labels */
        .form-label {
            color: #e1e1e1;
        }

        /* Improved hover effects for action buttons */
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
        }

        .btn-accept:hover {
            background-color: rgba(0, 255, 157, 0.2);
        }

        .btn-decline:hover {
            background-color: rgba(220, 53, 69, 0.2);
        }

        .btn-reschedule:hover {
            background-color: rgba(111, 66, 193, 0.2);
        }

        .btn-complete:hover {
            background-color: rgba(13, 110, 253, 0.2);
        }
    </style>

    <div class="container appointments-container">
        <h2 class="page-header">
            <i class="fas fa-calendar-alt me-2"></i>Appointment Management
        </h2>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert"
                 style="background: rgba(0, 255, 157, 0.15); border-color: rgba(0, 255, 157, 0.4); color: #00ff9d;">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert"
                 style="background: rgba(220, 53, 69, 0.15); border-color: rgba(220, 53, 69, 0.4); color: #ff647a;">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="appointment-tabs">
            <ul class="nav nav-tabs border-0" id="appointmentTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button">
                        <i class="fas fa-clock me-2"></i>Pending
                        @if(isset($pendingAppointments) && count($pendingAppointments) > 0)
                            <span class="badge rounded-pill bg-warning text-dark">{{ count($pendingAppointments) }}</span>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button">
                        <i class="fas fa-calendar-day me-2"></i>Upcoming
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="past-tab" data-bs-toggle="tab" data-bs-target="#past" type="button">
                        <i class="fas fa-history me-2"></i>Past
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content" id="appointmentTabContent">
            <!-- Pending Appointments Tab -->
            <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                <div class="row">
                    @if(isset($pendingAppointments) && count($pendingAppointments) > 0)
                        @foreach($pendingAppointments as $appointment)
                            <div class="col-md-6 col-lg-4">
                                <div class="appointment-card pending">
                                    <div class="appointment-time">{{ $appointment['time'] }}</div>
                                    <div class="appointment-date">
                                        {{ $appointment['formattedDate'] }}
                                    </div>

                                    <div class="patient-info">
                                        <span class="info-icon"><i class="fas fa-user-injured"></i></span>
                                        <span>{{ $appointment['patientName'] ?? 'Unassigned Patient' }}</span>
                                    </div>

                                    <div class="doctor-info">
                                        <span class="info-icon"><i class="fas fa-user-md"></i></span>
                                        <span>Dr. {{ $appointment['doctorName'] }}</span>
                                    </div>

                                    <div class="mb-3">
                                        <span class="status-badge badge-pending">Pending</span>
                                    </div>

                                    <div class="appointment-actions">
                                        <button class="action-btn btn-accept" data-bs-toggle="modal" data-bs-target="#acceptModal{{ $appointment['id'] }}">
                                            <i class="fas fa-check me-1"></i> Accept
                                        </button>

                                        <button class="action-btn btn-decline" data-bs-toggle="modal" data-bs-target="#declineModal{{ $appointment['id'] }}">
                                            <i class="fas fa-times me-1"></i> Decline
                                        </button>

                                        <button class="action-btn btn-reschedule" data-bs-toggle="modal" data-bs-target="#rescheduleModal{{ $appointment['id'] }}">
                                            <i class="fas fa-calendar-alt me-1"></i> Reschedule
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Accept Modal -->
                            <div class="modal fade" id="acceptModal{{ $appointment['id'] }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content bg-dark text-light border-success">
                                        <div class="modal-header border-success">
                                            <h5 class="modal-title">Accept Appointment</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('firebase.nurse.appointment.update', $appointment['id']) }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <p>Confirm acceptance of appointment with:</p>
                                                <ul class="list-unstyled">
                                                    <li><strong>Patient:</strong> {{ $appointment['patientName'] ?? 'Unassigned' }}</li>
                                                    <li><strong>Doctor:</strong> Dr. {{ $appointment['doctorName'] }}</li>
                                                    <li><strong>Date & Time:</strong> {{ $appointment['formattedDate'] }} at {{ $appointment['time'] }}</li>
                                                </ul>
                                                <input type="hidden" name="status" value="accepted">
                                            </div>
                                            <div class="modal-footer border-success">
                                                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-success">Accept Appointment</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Decline Modal -->
                            <div class="modal fade" id="declineModal{{ $appointment['id'] }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content bg-dark text-light border-danger">
                                        <div class="modal-header border-danger">
                                            <h5 class="modal-title">Decline Appointment</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('firebase.nurse.appointment.update', $appointment['id']) }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <p>Please provide a reason for declining this appointment:</p>
                                                <ul class="list-unstyled mb-3">
                                                    <li><strong>Patient:</strong> {{ $appointment['patientName'] ?? 'Unassigned' }}</li>
                                                    <li><strong>Doctor:</strong> Dr. {{ $appointment['doctorName'] }}</li>
                                                    <li><strong>Date & Time:</strong> {{ $appointment['formattedDate'] }} at {{ $appointment['time'] }}</li>
                                                </ul>
                                                <input type="hidden" name="status" value="declined">
                                                <div class="mb-3">
                                                    <label for="reason" class="form-label">Reason for declining</label>
                                                    <textarea class="form-control bg-dark text-light" name="reason" id="reason" rows="3" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-danger">
                                                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Decline Appointment</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Reschedule Modal -->
                            <div class="modal fade" id="rescheduleModal{{ $appointment['id'] }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content bg-dark text-light" style="border-color: #6f42c1;">
                                        <div class="modal-header" style="border-color: #6f42c1;">
                                            <h5 class="modal-title">Reschedule Appointment</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('firebase.nurse.appointment.update', $appointment['id']) }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <p>Please select a new date and time for this appointment:</p>
                                                <ul class="list-unstyled mb-3">
                                                    <li><strong>Patient:</strong> {{ $appointment['patientName'] ?? 'Unassigned' }}</li>
                                                    <li><strong>Doctor:</strong> Dr. {{ $appointment['doctorName'] }}</li>
                                                    <li><strong>Current Date & Time:</strong> {{ $appointment['formattedDate'] }} at {{ $appointment['time'] }}</li>
                                                </ul>
                                                <input type="hidden" name="status" value="rescheduled">
                                                <div class="mb-3">
                                                    <label for="new_date" class="form-label">New Date</label>
                                                    <input type="date" class="form-control bg-dark text-light" id="new_date" name="new_date" required min="{{ date('Y-m-d') }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="new_time" class="form-label">New Time</label>
                                                    <input type="time" class="form-control bg-dark text-light" id="new_time" name="new_time" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="reason" class="form-label">Reason for rescheduling (optional)</label>
                                                    <textarea class="form-control bg-dark text-light" name="reason" id="reason" rows="2"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer" style="border-color: #6f42c1;">
                                                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn" style="background-color: #6f42c1; color: white;">Reschedule</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-12">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <h4>No Pending Appointments</h4>
                                <p class="text-muted">There are no appointments waiting for review at this time.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Upcoming Appointments Tab -->
            <div class="tab-pane fade" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
                <div class="row">
                    @if(isset($upcomingAppointments) && count($upcomingAppointments) > 0)
                        @foreach($upcomingAppointments as $appointment)
                            <div class="col-md-6 col-lg-4">
                                <div class="appointment-card {{ $appointment['status'] }}">
                                    <div class="appointment-time">{{ $appointment['time'] }}</div>
                                    <div class="appointment-date">
                                        {{ $appointment['formattedDate'] }}
                                    </div>

                                    <div class="patient-info">
                                        <span class="info-icon"><i class="fas fa-user-injured"></i></span>
                                        <span>{{ $appointment['patientName'] ?? 'Unassigned Patient' }}</span>
                                    </div>

                                    <div class="doctor-info">
                                        <span class="info-icon"><i class="fas fa-user-md"></i></span>
                                        <span>Dr. {{ $appointment['doctorName'] }}</span>
                                    </div>

                                    <div class="mb-3">
                                        @if($appointment['status'] === 'accepted')
                                            <span class="status-badge badge-accepted">Accepted</span>
                                        @elseif($appointment['status'] === 'rescheduled')
                                            <span class="status-badge badge-rescheduled">Rescheduled</span>
                                        @endif
                                    </div>

                                    <div class="appointment-actions">
                                        <button class="action-btn btn-complete" data-bs-toggle="modal" data-bs-target="#completeModal{{ $appointment['id'] }}">
                                            <i class="fas fa-check-double me-1"></i> Mark Complete
                                        </button>

                                        <button class="action-btn btn-decline" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $appointment['id'] }}">
                                            <i class="fas fa-ban me-1"></i> Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Complete Modal -->
                            <div class="modal fade" id="completeModal{{ $appointment['id'] }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content bg-dark text-light" style="border-color: #0d6efd;">
                                        <div class="modal-header" style="border-color: #0d6efd;">
                                            <h5 class="modal-title">Mark Appointment as Completed</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('firebase.nurse.appointment.update', $appointment['id']) }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <p>Confirm this appointment has been completed:</p>
                                                <ul class="list-unstyled">
                                                    <li><strong>Patient:</strong> {{ $appointment['patientName'] ?? 'Unassigned' }}</li>
                                                    <li><strong>Doctor:</strong> Dr. {{ $appointment['doctorName'] }}</li>
                                                    <li><strong>Date & Time:</strong> {{ $appointment['formattedDate'] }} at {{ $appointment['time'] }}</li>
                                                </ul>
                                                <input type="hidden" name="status" value="completed">
                                            </div>
                                            <div class="modal-footer" style="border-color: #0d6efd;">
                                                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Mark as Completed</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Cancel Modal -->
                            <div class="modal fade" id="cancelModal{{ $appointment['id'] }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content bg-dark text-light border-danger">
                                        <div class="modal-header border-danger">
                                            <h5 class="modal-title">Cancel Appointment</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('firebase.nurse.appointment.update', $appointment['id']) }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <p>Please provide a reason for cancelling this appointment:</p>
                                                <ul class="list-unstyled mb-3">
                                                    <li><strong>Patient:</strong> {{ $appointment['patientName'] ?? 'Unassigned' }}</li>
                                                    <li><strong>Doctor:</strong> Dr. {{ $appointment['doctorName'] }}</li>
                                                    <li><strong>Date & Time:</strong> {{ $appointment['formattedDate'] }} at {{ $appointment['time'] }}</li>
                                                </ul>
                                                <input type="hidden" name="status" value="cancelled">
                                                <div class="mb-3">
                                                    <label for="reason" class="form-label">Reason for cancelling</label>
                                                    <textarea class="form-control bg-dark text-light" name="reason" id="reason" rows="3" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-danger">
                                                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Back</button>
                                                <button type="submit" class="btn btn-danger">Cancel Appointment</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-12">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                                <h4>No Upcoming Appointments</h4>
                                <p class="text-muted">There are no accepted or scheduled appointments coming up.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Past Appointments Tab -->
            <div class="tab-pane fade" id="past" role="tabpanel" aria-labelledby="past-tab">
                <div class="row">
                    @if(isset($pastAppointments) && count($pastAppointments) > 0)
                        @foreach($pastAppointments as $appointment)
                            <div class="col-md-6 col-lg-4">
                                <div class="appointment-card {{ $appointment['status'] }}">
                                    <div class="appointment-time">{{ $appointment['time'] }}</div>
                                    <div class="appointment-date">
                                        {{ $appointment['formattedDate'] }}
                                    </div>

                                    <div class="patient-info">
                                        <span class="info-icon"><i class="fas fa-user-injured"></i></span>
                                        <span>{{ $appointment['patientName'] ?? 'Unassigned Patient' }}</span>
                                    </div>

                                    <div class="doctor-info">
                                        <span class="info-icon"><i class="fas fa-user-md"></i></span>
                                        <span>Dr. {{ $appointment['doctorName'] }}</span>
                                    </div>

                                    <div class="mb-3">
                                        @if($appointment['status'] === 'completed')
                                            <span class="status-badge badge-completed">Completed</span>
                                        @elseif($appointment['status'] === 'cancelled')
                                            <span class="status-badge badge-declined">Cancelled</span>
                                        @elseif($appointment['status'] === 'declined')
                                            <span class="status-badge badge-declined">Declined</span>
                                        @else
                                            <span class="status-badge" style="background: rgba(108, 117, 125, 0.25); color: #c8c8c8; border: 1px solid rgba(108, 117, 125, 0.5);">
                                            {{ ucfirst($appointment['status']) }}
                                        </span>
                                        @endif

                                        @if(isset($appointment['status_reason']))
                                            <div class="mt-2 small" style="color: #b8b8b8;">
                                                <i class="fas fa-info-circle me-1"></i> {{ $appointment['status_reason'] }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-12">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-history"></i>
                                </div>
                                <h4>No Past Appointments</h4>
                                <p class="text-muted">There are no past appointments in the system.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);

            // Initialize tooltips
            const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltips.forEach(tooltip => {
                new bootstrap.Tooltip(tooltip);
            });
        });
    </script>
@endsection
