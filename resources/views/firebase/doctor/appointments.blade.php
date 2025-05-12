
@extends('firebase.layoutdoctor')

@section('title', 'My Appointments')

@section('content')
    <style>
        .appointment-card {
            background: rgba(16, 25, 36, 0.7);
            border-radius: 12px;
            border: 1px solid rgba(0, 119, 255, 0.2);
            padding: 15px;
            margin-bottom: 15px;
            position: relative;
            overflow: hidden;
        }

        .appointment-date {
            font-family: 'Orbitron', sans-serif;
            color: var(--primary);
            margin-bottom: 1rem;
            font-size: 1.1rem;
            border-bottom: 1px solid rgba(0, 119, 255, 0.2);
            padding-bottom: 8px;
        }

        .appointment-item {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            margin-bottom: 10px;
            padding: 12px;
            position: relative;
            border-left: 4px solid;
        }

        .appointment-item.pending {
            border-left-color: #ffbb00;
        }

        .appointment-item.accepted {
            border-left-color: var(--primary);
        }

        .appointment-item.cancelled, .appointment-item.declined {
            border-left-color: #dc3545;
        }

        .appointment-item.completed {
            border-left-color: #0d6efd;
        }

        .appointment-time {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.1rem;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .appointment-patient {
            font-size: 0.9rem;
            color: #e1e1e1;
        }

        .appointment-status {
            position: absolute;
            top: 12px;
            right: 12px;
            padding: 3px 8px;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: bold;
        }

        .appointment-status.pending {
            background: rgba(255, 193, 7, 0.2);
            color: #ffbb00;
        }

        .appointment-status.accepted {
            background: rgba(0, 119, 255, 0.2);
            color: var(--primary);
        }

        .appointment-status.cancelled, .appointment-status.declined {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
        }

        .appointment-status.completed {
            background: rgba(13, 110, 253, 0.2);
            color: #0d6efd;
        }

        .status-pill {
            display: inline-block;
            padding: 0.25em 0.6em;
            font-size: 75%;
            font-weight: 700;
            border-radius: 10rem;
        }

        .stats-card {
            background: rgba(16, 25, 36, 0.7);
            border-radius: 12px;
            padding: 15px;
            border: 1px solid rgba(0, 119, 255, 0.2);
            position: relative;
            overflow: hidden;
            text-align: center;
            margin-bottom: 15px;
        }

        .stats-card h4 {
            color: var(--primary);
            margin-bottom: 1rem;
            font-size: 1rem;
        }

        .stats-value {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.6rem;
            color: var(--primary);
        }

        .filter-btn {
            padding: 5px 10px;
            margin: 0 3px;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(0, 119, 255, 0.2);
            color: #e1e1e1;
            border-radius: 6px;
            transition: all 0.3s;
        }

        .filter-btn.active {
            background: rgba(0, 119, 255, 0.2);
            color: var(--primary);
        }
    </style>

    <div class="container py-4">
        <h2 class="text-primary mb-4">
            <i class="fas fa-calendar-check me-2"></i> My Appointments
        </h2>

        <!-- Appointment Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <h4>Today's Appointments</h4>
                    <div class="stats-value">{{ $appointmentsToday }}</div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="stats-card">
                    <h4>Status Breakdown</h4>
                    <div class="d-flex justify-content-center">
                        <div class="mx-3">
                        <span class="status-pill" style="background: rgba(255, 193, 7, 0.2); color: #ffbb00;">
                            <i class="fas fa-clock me-1"></i> {{ $pendingAppointments }}
                        </span>
                            <div class="small mt-1">Pending</div>
                        </div>
                        <div class="mx-3">
                        <span class="status-pill" style="background: rgba(0, 119, 255, 0.2); color: var(--primary);">
                            <i class="fas fa-check me-1"></i> {{ $acceptedAppointments }}
                        </span>
                            <div class="small mt-1">Accepted</div>
                        </div>
                        <div class="mx-3">
                        <span class="status-pill" style="background: rgba(220, 53, 69, 0.2); color: #dc3545;">
                            <i class="fas fa-times me-1"></i> {{ $cancelledAppointments }}
                        </span>
                            <div class="small mt-1">Cancelled</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter buttons -->
        <div class="mb-4">
            <div class="d-flex flex-wrap align-items-center">
                <div class="me-3">Filter:</div>
                <button class="filter-btn active" data-filter="all">All</button>
                <button class="filter-btn" data-filter="pending">Pending</button>
                <button class="filter-btn" data-filter="accepted">Accepted</button>
                <button class="filter-btn" data-filter="cancelled">Cancelled</button>
            </div>
        </div>

        <!-- Appointments List -->
        @if(count($groupedAppointments) > 0)
            @foreach($groupedAppointments as $date => $appointments)
                <div class="appointment-card">
                    <div class="appointment-date">
                        @if($date === date('Y-m-d'))
                            <i class="fas fa-calendar-day me-2"></i> Today
                        @elseif($date === date('Y-m-d', strtotime('+1 day')))
                            <i class="fas fa-calendar-day me-2"></i> Tomorrow
                        @else
                            <i class="fas fa-calendar-alt me-2"></i> {{ date('l, F j, Y', strtotime($date)) }}
                        @endif
                    </div>

                    @foreach($appointments as $id => $appointment)
                        <div class="appointment-item {{ $appointment['status'] }}" data-status="{{ $appointment['status'] }}">
                            <div class="appointment-time">
                                <i class="fas fa-clock me-2"></i> {{ $appointment['time'] }}
                            </div>
                            <div class="appointment-patient">
                                <strong>Patient:</strong> {{ $appointment['patientName'] }}
                            </div>
                            <div class="appointment-patient">
                                <strong>Phone:</strong> {{ $appointment['patientPhone'] }}
                            </div>

                            @if(!empty($appointment['notes']))
                                <div class="mt-2 small text-light">
                                    <strong>Notes:</strong> {{ $appointment['notes'] }}
                                </div>
                            @endif

                            <span class="appointment-status {{ $appointment['status'] }}">
                            {{ ucfirst($appointment['status']) }}
                        </span>

                            @if($appointment['status'] === 'pending')
                                <div class="mt-3">
                                    <form action="{{ route('firebase.doctor.updateAppointment', $id) }}" method="post" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="accepted">
                                        <button type="submit" class="btn btn-sm" style="background: rgba(0, 119, 255, 0.2); color: var(--primary); border: 1px solid rgba(0, 119, 255, 0.4);">
                                            <i class="fas fa-check me-1"></i> Accept
                                        </button>
                                    </form>

                                    <form action="{{ route('firebase.doctor.updateAppointment', $id) }}" method="post" class="d-inline ms-2">
                                        @csrf
                                        <input type="hidden" name="status" value="declined">
                                        <button type="submit" class="btn btn-sm" style="background: rgba(220, 53, 69, 0.2); color: #dc3545; border: 1px solid rgba(220, 53, 69, 0.4);">
                                            <i class="fas fa-times me-1"></i> Decline
                                        </button>
                                    </form>
                                </div>
                            @elseif($appointment['status'] === 'accepted')
                                <div class="mt-3">
                                    <form action="{{ route('firebase.doctor.updateAppointment', $id) }}" method="post" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="btn btn-sm" style="background: rgba(13, 110, 253, 0.2); color: #0d6efd; border: 1px solid rgba(13, 110, 253, 0.4);">
                                            <i class="fas fa-check-double me-1"></i> Mark Completed
                                        </button>
                                    </form>

                                    <form action="{{ route('firebase.doctor.updateAppointment', $id) }}" method="post" class="d-inline ms-2">
                                        @csrf
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="btn btn-sm" style="background: rgba(220, 53, 69, 0.2); color: #dc3545; border: 1px solid rgba(220, 53, 69, 0.4);">
                                            <i class="fas fa-ban me-1"></i> Cancel
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
        @else
            <div class="text-center p-5 bg-dark rounded">
                <i class="fas fa-calendar-times mb-3" style="font-size: 3rem; color: var(--primary);"></i>
                <h4 class="text-light">No appointments found</h4>
                <p class="text-muted">You don't have any appointments scheduled.</p>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filter functionality
            const filterButtons = document.querySelectorAll('.filter-btn');
            const appointmentItems = document.querySelectorAll('.appointment-item');

            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    filterButtons.forEach(btn => btn.classList.remove('active'));

                    // Add active class to clicked button
                    this.classList.add('active');

                    const filter = this.getAttribute('data-filter');

                    // Show or hide appointments based on filter
                    appointmentItems.forEach(item => {
                        if (filter === 'all' || item.getAttribute('data-status') === filter) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            });
        });
    </script>
@endsection
