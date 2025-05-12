
@extends('firebase.layoutdoctor')

@section('title', 'Doctor Dashboard')

@section('content')
    <style>
        /* Dashboard specific styles - Compact Version */
        .welcome-header {
            font-family: 'Orbitron', sans-serif;
            color: var(--primary);
            margin-bottom: 1rem;
            position: relative;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
            text-shadow: 0 0 15px rgba(0, 195, 255, 0.3);
            font-size: 1.5rem;
        }

        .welcome-header::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 60px;
            height: 2px;
            background: linear-gradient(to right, var(--primary), transparent);
        }

        /* Dashboard Container Layout */
        .dashboard-container {
            display: grid;
            grid-template-columns: 3fr 1fr;
            gap: 15px;
            margin-top: 10px;
        }

        .main-content {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .sidebar-content {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        /* Stat Cards - Compact */
        .stat-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 15px;
        }

        .stat-card {
            background: rgba(16, 25, 36, 0.7);
            border-radius: 10px;
            border: 1px solid rgba(0, 195, 255, 0.2);
            padding: 15px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, var(--primary), var(--secondary));
        }

        .stat-icon {
            font-size: 1.8rem;
            color: var(--primary);
            margin-bottom: 8px;
            text-shadow: 0 0 10px rgba(0, 195, 255, 0.5);
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: white;
            margin-bottom: 3px;
            font-family: 'Orbitron', sans-serif;
        }

        .stat-label {
            color: #b0b0b0;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Dashboard Card - Compact */
        .dashboard-card {
            background: rgba(16, 25, 36, 0.7);
            border-radius: 10px;
            border: 1px solid rgba(0, 195, 255, 0.2);
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .dashboard-card-header {
            background: rgba(10, 20, 30, 0.8);
            padding: 10px 15px;
            border-bottom: 1px solid rgba(0, 195, 255, 0.2);
            font-family: 'Orbitron', sans-serif;
            color: var(--primary);
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dashboard-card-body {
            padding: 15px;
        }

        /* Quick Actions - Compact */
        .action-buttons {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }

        .action-button {
            background: rgba(16, 25, 36, 0.7);
            border: 1px solid rgba(0, 195, 255, 0.2);
            border-radius: 8px;
            padding: 12px 8px;
            text-align: center;
            transition: all 0.3s ease;
            color: #e1e1e1;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .action-button i {
            font-size: 1.5rem;
            margin-bottom: 8px;
            color: var(--primary);
            z-index: 2;
        }

        .action-button span {
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1px;
            font-weight: 500;
            z-index: 2;
        }

        /* Activity List - Compact */
        .activity-list {
            background: transparent;
        }

        .activity-item {
            padding: 10px 15px;
            border-bottom: 1px solid rgba(0, 195, 255, 0.1);
            display: flex;
            align-items: center;
        }

        .activity-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: rgba(0, 195, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            color: var(--primary);
            font-size: 0.9rem;
        }

        .activity-title {
            color: white;
            margin-bottom: 2px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .activity-time {
            color: #b0b0b0;
            font-size: 0.75rem;
        }

        /* Digital Clock - Compact */
        .digital-clock {
            text-align: center;
            padding: 10px;
        }

        .time-display {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.2rem;
            color: var(--primary);
            text-shadow: 0 0 15px rgba(0, 195, 255, 0.5);
            margin-bottom: 5px;
        }

        .date-display {
            font-size: 1rem;
            color: #b0b0b0;
            margin-bottom: 5px;
        }

        .time-greeting {
            color: white;
            font-size: 0.9rem;
            margin-top: 10px;
        }

        /* Calendar - Compact */
        .calendar-widget {
            padding: 0 10px 10px;
        }

        .calendar-header {
            text-align: center;
            margin-bottom: 10px;
            color: white;
            font-family: 'Orbitron', sans-serif;
            font-size: 1rem;
        }

        .calendar-table {
            width: 100%;
            border-collapse: collapse;
        }

        .calendar-table th {
            color: var(--primary);
            font-weight: 500;
            padding: 5px;
            text-align: center;
            font-size: 0.8rem;
        }

        .calendar-table td {
            padding: 6px;
            text-align: center;
            color: #b0b0b0;
            border-radius: 5px;
            transition: all 0.2s;
            font-size: 0.8rem;
        }

        /* Appointments - Compact */
        .upcoming-appointments {
            margin-top: 10px;
        }

        .appointment-item {
            display: flex;
            align-items: center;
            padding: 8px;
            border-left: 3px solid var(--primary);
            background: rgba(0, 119, 255, 0.1);
            margin-bottom: 8px;
            border-radius: 0 5px 5px 0;
        }

        .appointment-time {
            background: rgba(0, 119, 255, 0.2);
            color: var(--primary);
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-right: 8px;
        }

        .appointment-patient {
            color: white;
            font-size: 0.8rem;
        }

        /* Status indicators for appointment card */
        .appointment-status-indicator {
            display: flex;
            justify-content: center;
            margin-top: 5px;
            margin-bottom: 8px;
        }

        .status-icon {
            margin: 0 5px;
            font-size: 0.8rem;
        }

        .status-pending {
            color: #ffbb00;
        }

        .status-accepted {
            color: var(--primary);
        }

        .status-cancelled {
            color: #dc3545;
        }

        /* Badge for pending notifications */
        .notification-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: rgba(255, 193, 7, 0.9);
            color: #000;
            border-radius: 50%;
            padding: 0.25rem 0.5rem;
            font-size: 0.7rem;
            font-weight: bold;
        }

        /* Progress Bar */
        .progress {
            height: 4px !important;
            background: rgba(0, 195, 255, 0.1);
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .dashboard-container {
                grid-template-columns: 1fr;
            }

            .stat-cards {
                grid-template-columns: repeat(2, 1fr);
            }

            .action-buttons {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>

    <div class="container-fluid">
        <h1 class="welcome-header"><i class="fas fa-user-md me-2"></i>Doctor Dashboard</h1>

        <div class="dashboard-container">
            <div class="main-content">
                <!-- Stats Overview -->
                <div class="stat-cards">
                    <div class="stat-card">
                        <i class="fas fa-user-injured stat-icon"></i>
                        <div class="stat-value">{{$appointmentsToday > 0 ? $appointmentsToday: '' }}</div>
                        <div class="stat-label">Patients Today</div>
                    </div>

                    <!-- Appointment Stats Card -->
                    <div class="stat-card">
                        <i class="fas fa-calendar-check stat-icon"></i>
                        <div class="stat-value">{{ $appointmentsToday > 0 ? $appointmentsToday: '' }}</div>

                        <!-- Appointment status breakdown - match exactly with the image -->
                        <div class="appointment-status-indicator">
        <span class="status-icon status-pending" title="Pending" style="color: #ffbb00;">
            <i class="fas fa-clock"></i> {{ $pendingAppointments ?? 0}}
        </span>
                            <span class="status-icon status-accepted" title="Accepted" style="color: var(--primary);">
            <i class="fas fa-check"></i> {{ $acceptedAppointments ?? 0 }}
        </span>
                            <span class="status-icon status-cancelled" title="Cancelled" style="color: #dc3545;">
            <i class="fas fa-times"></i> {{ $cancelledAppointments ?? 0 }}
        </span>
                        </div>

                        <div class="stat-label">Appointment</div>
                    </div>

                    <div class="stat-card">
                        <i class="fas fa-clipboard-check stat-icon"></i>
                        <div class="stat-value">{{ 'N/A' }}</div>
                        <div class="stat-label">Pending Reports</div>
                    </div>

                    <div class="stat-card">
                        <i class="fas fa-heartbeat stat-icon"></i>
                        <div class="stat-value">{{ 'N/A' }}</div>
                        <div class="stat-label">Consultations</div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <i class="fas fa-bolt"></i> Quick Actions
                    </div>
                    <div class="dashboard-card-body">
                        <div class="action-buttons">
                            <a href="{{ route('firebase.doctor.search') }}" class="action-button">
                                <i class="fas fa-search"></i>
                                <span>Search</span>
                            </a>

                            <a href="{{ route('firebase.doctor.search') }}" class="action-button">
                                <i class="fas fa-prescription-bottle-alt"></i>
                                <span>Prescribe</span>
                            </a>

                            <a href="{{ route('firebase.doctor.search') }}" class="action-button">
                                <i class="fas fa-notes-medical"></i>
                                <span>Records</span>
                            </a>

                            <a href="{{ route('firebase.doctor.appointments') }}" class="action-button">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Appointments</span>
                                @if(isset($pendingAppointments) && $pendingAppointments > 0)
                                    <span class="notification-badge">
                                        {{ $pendingAppointments }}
                                    </span>
                                @endif
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Recent Appointments -->
                @if(isset($recentAppointments) && count($recentAppointments) > 0)
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <i class="fas fa-calendar-check"></i> Recent Appointments
                        </div>
                        <div class="dashboard-card-body">
                            <div class="table-responsive">
                                <table class="table table-dark table-hover mb-0" style="background: transparent; border-radius: 8px; overflow: hidden;">
                                    <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Patient</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($recentAppointments as $appointment)
                                        <tr>
                                            <td>
                                                {{ \Carbon\Carbon::parse($appointment['date'])->format('M d') }} at {{ $appointment['time'] }}
                                            </td>
                                            <td>{{ $appointment['patientName'] ?? 'Unknown Patient' }}</td>
                                            <td>
                                                @if($appointment['status'] === 'pending')
                                                    <span class="badge" style="background: rgba(255, 193, 7, 0.2); color: #ffbb00;">Pending</span>
                                                @elseif($appointment['status'] === 'accepted')
                                                    <span class="badge" style="background: rgba(0, 119, 255, 0.2); color: var(--primary);">Accepted</span>
                                                @elseif($appointment['status'] === 'completed')
                                                    <span class="badge" style="background: rgba(0, 123, 255, 0.2); color: #0d6efd;">Completed</span>
                                                @elseif($appointment['status'] === 'cancelled' || $appointment['status'] === 'declined')
                                                    <span class="badge" style="background: rgba(220, 53, 69, 0.2); color: #dc3545;">{{ ucfirst($appointment['status']) }}</span>
                                                @else
                                                    <span class="badge" style="background: rgba(108, 117, 125, 0.2); color: #adb5bd;">{{ ucfirst($appointment['status']) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="text-center mt-2">
                                <a href="{{ route('firebase.doctor.appointments') }}" class="btn btn-sm" style="background: rgba(0, 0, 0, 0.3); border: 1px solid var(--primary); color: var(--primary);">
                                    <i class="fas fa-calendar-check me-1"></i> View All Appointments
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Recent Activity if no appointments -->
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <i class="fas fa-history"></i> Recent Activity
                        </div>
                        <div class="dashboard-card-body">
                            <div class="activity-list">
                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-user-check"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title">Patient check-in: Alex Johnson</div>
                                        <div class="activity-time">Today, {{ date('g:i A') }}</div>
                                    </div>
                                </div>

                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-file-medical"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title">Medical report updated for Sarah Williams</div>
                                        <div class="activity-time">Yesterday, 3:45 PM</div>
                                    </div>
                                </div>

                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-pills"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title">Prescription created for Michael Brown</div>
                                        <div class="activity-time">Yesterday, 10:30 AM</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="sidebar-content">
                <!-- Digital Clock -->
                <div class="dashboard-card">
                    <div class="dashboard-card-body">
                        <div class="digital-clock">
                            <div class="time-display" id="timeDisplay">00:00:00</div>
                            <div class="date-display" id="dateDisplay">Loading...</div>
                            <div class="time-greeting" id="timeGreeting">Good day, Dr. {{ $fname }}</div>
                        </div>
                    </div>
                </div>

                <!-- Today's Appointments -->
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <i class="fas fa-calendar-check"></i> Today's Appointments
                    </div>
                    <div class="dashboard-card-body">
                        <div class="upcoming-appointments">
                            @if(isset($todaysAppointments) && count($todaysAppointments) > 0)
                                @foreach($todaysAppointments as $appointment)
                                    <div class="appointment-item">
                                        <div class="appointment-time">{{ $appointment['time'] }}</div>
                                        <div class="appointment-patient">{{ $appointment['patientName'] }}</div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-3">
                                    <i class="fas fa-calendar-day mb-2" style="font-size: 2rem; color: var(--primary);"></i>
                                    <p class="mb-0">No appointments scheduled for today</p>
                                </div>
                            @endif
                        </div>

                        @if(isset($todaysAppointments) && count($todaysAppointments) > 0)
                            <div class="text-center mt-2">
                                <a href="{{ route('firebase.doctor.appointments') }}" class="btn btn-sm" style="background: rgba(0, 0, 0, 0.3); border: 1px solid var(--primary); color: var(--primary);">
                                    <i class="fas fa-calendar-check me-1"></i> View All
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Today's Tasks -->
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <i class="fas fa-tasks"></i> Today's Tasks
                    </div>
                    <div class="dashboard-card-body">
                        <div class="upcoming-appointments">
                            <div class="appointment-item">
                                <div class="appointment-time">9:30 AM</div>
                                <div class="appointment-patient">Morning rounds</div>
                            </div>
                            <div class="appointment-item">
                                <div class="appointment-time">11:15 AM</div>
                                <div class="appointment-patient">Staff meeting</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize the clock
            updateClock();
            setInterval(updateClock, 1000);

            // Animation for stat cards
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 80);
            });

            // Animate counter for stat values
            const statValues = document.querySelectorAll('.stat-value');
            statValues.forEach(el => {
                const target = parseInt(el.textContent);
                if (!isNaN(target)) {
                    const duration = 1500; // 1.5 seconds - faster animation
                    const step = target / (duration / 30); // 30 fps
                    let current = 0;

                    const timer = setInterval(() => {
                        current += step;
                        if (current >= target) {
                            el.textContent = target;
                            clearInterval(timer);
                        } else {
                            el.textContent = Math.floor(current);
                        }
                    }, 30);
                }
            });
        });

        // Clock function
        function updateClock() {
            try {
                const now = new Date();

                // Update time display (with leading zeros)
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');

                const timeElement = document.getElementById('timeDisplay');
                if (timeElement) {
                    timeElement.textContent = `${hours}:${minutes}:${seconds}`;
                }

                // Update date display - shorter format
                const options = { weekday: 'short', month: 'short', day: 'numeric' };
                const dateString = now.toLocaleDateString('en-US', options);

                const dateElement = document.getElementById('dateDisplay');
                if (dateElement) {
                    dateElement.textContent = dateString;
                }

                // Update greeting based on time of day
                let greeting = "Good morning";
                if (now.getHours() >= 12 && now.getHours() < 18) {
                    greeting = "Good afternoon";
                } else if (now.getHours() >= 18) {
                    greeting = "Good evening";
                }

                const greetingElement = document.getElementById('timeGreeting');
                if (greetingElement) {
                    greetingElement.textContent = `${greeting}, Dr. {{ $fname }}`;
                }
            } catch (error) {
                console.error("Clock update error:", error);
            }
        }
    </script>
@endsection
