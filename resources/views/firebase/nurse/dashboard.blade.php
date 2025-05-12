
@extends('firebase.layoutnurse')

@section('title', 'Nurse Dashboard')

@section('content')
    <style>
        /* Dashboard Styles - Compact Version */
        .dashboard-container {
            padding: 10px 0;
            max-width: 1400px;
        }

        .dashboard-header {
            font-family: 'Orbitron', sans-serif;
            color: var(--success);
            margin-bottom: 0.8rem;
            position: relative;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
            text-shadow: 0 0 15px rgba(0, 255, 157, 0.3);
            font-size: 1.5rem;
        }

        .dashboard-header::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 60px;
            height: 2px;
            background: linear-gradient(to right, var(--success), transparent);
        }

        /* Grid Layout */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 3fr 1fr;
            gap: 12px;
            margin-top: 10px;
        }

        .main-content, .sidebar-content {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* Card Styles */
        .cyber-card, .welcome-card {
            background: rgba(16, 25, 36, 0.7);
            border-radius: 12px;
            border: 1px solid rgba(0, 255, 157, 0.2);
            padding: 15px;
            margin-bottom: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }

        .cyber-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, var(--success), var(--secondary));
        }

        /* Stats Grid */
        .stat-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 15px;
        }

        .stat-card {
            background: rgba(16, 25, 36, 0.7);
            border-radius: 12px;
            padding: 15px;
            border: 1px solid rgba(0, 255, 157, 0.2);
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
            background: linear-gradient(to bottom, var(--success), var(--secondary));
        }

        .stat-icon {
            font-size: 1.8rem;
            color: var(--success);
            margin-bottom: 8px;
        }

        .stat-title {
            font-size: 0.7rem;
            color: #a0a0a0;
            margin-bottom: 3px;
            text-transform: uppercase;
        }

        .stat-value {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--success);
            margin-bottom: 3px;
        }

        .stat-description {
            font-size: 0.7rem;
            color: #e1e1e1;
        }

        /* Modern Quick Actions */
        .modern-actions {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-top: 10px;
        }

        .modern-action-btn {
            background: rgba(10, 15, 20, 0.8);
            border: 1px solid rgba(0, 255, 157, 0.2);
            border-radius: 8px;
            padding: 20px 10px;
            text-align: center;
            transition: all 0.3s ease;
            color: #e1e1e1;
            position: relative;
            overflow: hidden;
            text-decoration: none;
        }

        .modern-action-btn:hover {
            border-color: rgba(0, 255, 157, 0.4);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 255, 157, 0.15);
            color: white;
        }

        .modern-action-btn i {
            font-size: 2rem;
            color: var(--success);
            margin-bottom: 10px;
            display: block;
        }

        .modern-action-label {
            text-transform: uppercase;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 1px;
            color: #e1e1e1;
        }

        /* Update notification badge for this new layout */
        .modern-action-btn .notification-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: rgba(255, 193, 7, 0.9);
            color: #000;
            border-radius: 50%;
            padding: 0.25rem 0.5rem;
            font-size: 0.7rem;
            font-weight: bold;
            min-width: 1.5rem;
        }

        /* Digital Clock */
        .digital-clock-card {
            background: rgba(16, 25, 36, 0.7);
            border-radius: 12px;
            border: 1px solid rgba(0, 255, 157, 0.2);
            padding: 15px;
            margin-bottom: 12px;
            text-align: center;
        }

        .digital-time {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--success);
            text-shadow: 0 0 15px rgba(0, 255, 157, 0.5);
        }

        .digital-date {
            font-size: 1rem;
            color: #e1e1e1;
            margin-bottom: 5px;
        }

        .digital-greeting {
            font-size: 0.9rem;
            color: var(--success);
            margin-top: 5px;
        }

        /* Activity Table */
        .table {
            margin-bottom: 0;
        }

        .table th, .table td {
            padding: 0.5rem 0.7rem;
            font-size: 0.85rem;
            border-color: rgba(0, 255, 157, 0.1);
        }

        /* Task Items */
        .task-item {
            background: rgba(0, 255, 157, 0.1);
            border-radius: 6px;
            border-left: 3px solid var(--success);
            padding: 8px;
            margin-bottom: 8px;
        }

        .task-content {
            font-size: 0.85rem;
        }

        /* Section Headers */
        .section-header {
            color: var(--success);
            font-family: 'Orbitron', sans-serif;
            font-size: 1.1rem !important;
            margin-bottom: 10px !important;
        }

        /* Form Controls */
        .form-control {
            background: rgba(16, 25, 36, 0.6);
            border-color: rgba(0, 255, 157, 0.2);
            color: white;
            font-size: 0.9rem;
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

        /* Responsive */
        @media (max-width: 991.98px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .stat-row, .modern-actions {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 575.98px) {
            .stat-row, .modern-actions {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="container dashboard-container">
        <h2 class="dashboard-header"><i class="fas fa-tachometer-alt me-2"></i>Nurse Dashboard</h2>

        <div class="dashboard-grid">
            <div class="main-content">
                <!-- Stats Cards -->
                <div class="stat-row">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-user-injured"></i>
                        </div>
                        <div class="stat-title">PATIENTS</div>
                        <div class="stat-value">{{ $patientCount ?? 0 }}</div>
                        <div class="stat-description">Total registered</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-procedures"></i>
                        </div>
                        <div class="stat-title">APPOINTMENTS</div>
                        <div class="stat-value">{{ $appointmentsToday > 0 ? $appointmentsToday: '' }}</div>

                        <!-- Appointment status breakdown - with yellow, green, red indicators -->
                        <div class="d-flex justify-content-center mt-1 mb-2">
                            <span style="color: #ffbb00; margin: 0 3px;" title="Pending">
                                <i class="fas fa-clock"></i> {{ $pendingAppointments > 0 ? $pendingAppointments : '' }}
                            </span>
                            <span style="color: var(--success); margin: 0 5px;" title="Accepted">
                                <i class="fas fa-check"></i> {{ $acceptedAppointments ?? 0 }}
                            </span>
                            <span style="color: #dc3545; margin: 0 3px;" title="Cancelled">
                                <i class="fas fa-times"></i> {{ $cancelledAppointments ?? 0 }}
                            </span>
                        </div>

                        <div class="stat-description">Today</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <div class="stat-title">VITALS</div>
                        <div class="stat-value">{{ 'N/A' }}</div>
                        <div class="stat-description">Recorded today</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <div class="stat-title">AVG BP</div>
                        <div class="stat-value">{{ 'N/A' }}</div>
                        <div class="stat-description">Today's average</div>
                    </div>
                </div>

                <!-- Quick Actions - Updated Modern Version -->
                <div class="cyber-card">
                    <h3 class="section-header">
                        <i class="fas fa-bolt me-2" style="color: var(--success);"></i>Quick Actions
                    </h3>
                    <div class="modern-actions">
                        <a href="{{ route('firebase.nurse.register_patient') }}" class="modern-action-btn">
                            <i class="fas fa-user-plus"></i>
                            <div class="modern-action-label">REGISTER</div>
                        </a>

                        <a href="{{ route('firebase.nurse.search') }}" class="modern-action-btn">
                            <i class="fas fa-search"></i>
                            <div class="modern-action-label">SEARCH</div>
                        </a>

                        <a href="{{ route('firebase.nurse.search') }}" class="modern-action-btn">
                            <i class="fas fa-file-medical"></i>
                            <div class="modern-action-label">RECORDS</div>
                        </a>

                        <a href="{{ route('firebase.nurse.appointments') }}" class="modern-action-btn">
                            <i class="fas fa-calendar-alt"></i>
                            <div class="modern-action-label">APPOINTMENTS</div>
                            @if(isset($pendingAppointments) && $pendingAppointments > 0)
                                <span class="notification-badge">
                                    {{ $pendingAppointments }}
                                </span>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- Recent Appointments -->
                <div class="cyber-card">
                    <h3 class="section-header">
                        <i class="fas fa-clock me-2"></i>Recent Appointments
                    </h3>

                    @if(isset($recentAppointments) && count($recentAppointments) > 0)
                        <div class="table-responsive">
                            <table class="table table-dark table-hover mb-0" style="background: transparent; border-radius: 8px; overflow: hidden;">
                                <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
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
                                        <td>{{ $appointment['doctorName'] }}</td>
                                        <td>
                                            @if($appointment['status'] === 'pending')
                                                <span class="badge" style="background: rgba(255, 193, 7, 0.2); color: #ffbb00;">Pending</span>
                                            @elseif($appointment['status'] === 'accepted')
                                                <span class="badge" style="background: rgba(0, 255, 157, 0.2); color: var(--success);">Accepted</span>
                                            @elseif($appointment['status'] === 'completed')
                                                <span class="badge" style="background: rgba(0, 123, 255, 0.2); color: #0d6efd;">Completed</span>
                                            @elseif($appointment['status'] === 'cancelled' || $appointment['status'] === 'declined')
                                                <span class="badge" style="background: rgba(220, 53, 69, 0.2); color: #dc3545;">{{ ucfirst($appointment['status']) }}</span>
                                            @elseif($appointment['status'] === 'rescheduled')
                                                <span class="badge" style="background: rgba(111, 66, 193, 0.2); color: #6f42c1;">Rescheduled</span>
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
                            <a href="{{ route('firebase.nurse.appointments') }}" class="btn btn-sm" style="background: rgba(0, 0, 0, 0.3); border: 1px solid var(--success); color: var(--success);">
                                <i class="fas fa-calendar-check me-1"></i> View All Appointments
                            </a>
                        </div>
                    @else
                        <div class="text-center p-4" style="background: rgba(0, 0, 0, 0.2); border-radius: 8px;">
                            <i class="fas fa-calendar-alt mb-3" style="font-size: 2rem; color: var(--success);"></i>
                            <p class="mb-2">No recent appointments</p>
                            <a href="{{ route('firebase.nurse.appointments') }}" class="btn btn-sm" style="background: rgba(0, 0, 0, 0.3); border: 1px solid var(--success); color: var(--success);">
                                <i class="fas fa-calendar-plus me-1"></i> Manage Appointments
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="sidebar-content">
                <!-- Digital Clock -->
                <div class="digital-clock-card">
                    <div class="digital-time" id="digital-time">00:00:00</div>
                    <div class="digital-date" id="digital-date">Loading date...</div>
                    <div class="digital-greeting" id="digital-greeting">Welcome back</div>
                </div>

                <!-- Today's Tasks -->
                <div class="cyber-card">
                    <h3 class="section-header">
                        <i class="fas fa-tasks me-2"></i>Today's Tasks
                    </h3>

                    <div class="task-list">
                        <div class="task-item d-flex align-items-center mb-2">
                            <div class="task-icon me-2">
                                <i class="fas fa-syringe" style="color: var(--success);"></i>
                            </div>
                            <div class="task-content">
                                <div style="color: #e1e1e1;">Administer medications</div>
                                <div style="color: #a0a0a0; font-size: 0.8rem;">8:00 AM - 9:30 AM</div>
                            </div>
                        </div>

                        <div class="task-item d-flex align-items-center mb-2">
                            <div class="task-icon me-2">
                                <i class="fas fa-clipboard-list" style="color: var(--success);"></i>
                            </div>
                            <div class="task-content">
                                <div style="color: #e1e1e1;">Patient vitals check</div>
                                <div style="color: #a0a0a0; font-size: 0.8rem;">10:00 AM - 11:30 AM</div>
                            </div>
                        </div>

                        <div class="task-item d-flex align-items-center">
                            <div class="task-icon me-2">
                                <i class="fas fa-user-md" style="color: var(--success);"></i>
                            </div>
                            <div class="task-content">
                                <div style="color: #e1e1e1;">Doctor rounds assistance</div>
                                <div style="color: #a0a0a0; font-size: 0.8rem;">1:00 PM - 2:30 PM</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Patient Messages Widget -->
                <div class="cyber-card">
                    <h3 class="section-header mb-3">
                        <i class="fas fa-comments me-2"></i>Patient Messages
                    </h3>

                    @if(isset($recentChats) && count($recentChats) > 0)
                        <div class="recent-chats">
                            @foreach($recentChats as $chat)
                                <a href="{{ route('firebase.nurse.viewChat', ['chatId' => $chat['chat_id']]) }}"
                                   class="chat-item d-flex align-items-center p-2 mb-2 text-decoration-none"
                                   style="background: rgba(0, 0, 0, 0.2); border-radius: 8px; border: 1px solid rgba(0, 255, 157, 0.2);">
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--secondary), var(--success)); color: black; display: flex; justify-content: center; align-items: center; margin-right: 10px;">
                                        {{ strtoupper(substr($chat['patient_name'] ?? 'U', 0, 1)) }}
                                    </div>
                                    <div style="flex: 1; overflow: hidden;">
                                        <div class="d-flex justify-content-between">
                                            <span style="color: var(--success); font-weight: 500;">{{ $chat['patient_name'] }}</span>
                                            @if($chat['unread_count'] > 0)
                                                <span style="background: var(--success); color: black; font-size: 0.7rem; padding: 1px 6px; border-radius: 10px;">
                                                    {{ $chat['unread_count'] }}
                                                </span>
                                            @endif
                                        </div>
                                        <div style="color: #e1e1e1; font-size: 0.85rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            @if(isset($chat['last_message']))
                                                <span style="color: rgba(255, 255, 255, 0.6);">
                                                    {{ $chat['last_message']['isFromNurse'] ? 'You: ' : '' }}
                                                </span>
                                                {{ $chat['last_message']['text'] }}
                                                <span style="color: rgba(255, 255, 255, 0.5); font-size: 0.75rem; margin-left: 5px;">
                                                    {{ \Carbon\Carbon::createFromTimestampMs($chat['last_message']['timestamp'])->format('g:i A') }}
                                                </span>
                                            @else
                                                <span style="font-style: italic; color: rgba(255, 255, 255, 0.4);">No messages yet</span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <div class="text-center mt-2">
                            <a href="{{ route('firebase.nurse.allChats') }}" class="btn btn-sm" style="background: rgba(0, 0, 0, 0.3); border: 1px solid var(--success); color: var(--success);">
                                <i class="fas fa-inbox me-1"></i> All Conversations
                            </a>
                        </div>
                    @else
                        <div class="text-center p-4" style="background: rgba(0, 0, 0, 0.2); border-radius: 8px;">
                            <i class="fas fa-comments mb-3" style="font-size: 2rem; color: var(--success);"></i>
                            <p class="mb-2">No recent messages</p>
                            <a href="{{ route('firebase.nurse.search') }}" class="btn btn-sm" style="background: rgba(0, 0, 0, 0.3); border: 1px solid var(--success); color: var(--success);">
                                <i class="fas fa-search me-1"></i> Find Patients
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Notes -->
                <div class="cyber-card">
                    <h3 class="section-header">
                        <i class="fas fa-sticky-note me-2"></i>Quick Notes
                    </h3>

                    <div class="form-group">
                        <textarea class="form-control bg-dark text-light mb-2" rows="3" placeholder="Add your notes here..."></textarea>
                        <button class="btn text-light w-100" style="background: rgba(0, 255, 157, 0.2); border: 1px solid rgba(0, 255, 157, 0.4);">
                            <i class="fas fa-save me-2"></i> Save Note
                        </button>
                    </div>
                </div>
            </div>
        </div> <!-- End of dashboard-grid -->
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize clock and date
            updateClock();
            setInterval(updateClock, 1000);

            // Animate counter for stat values
            const statValues = document.querySelectorAll('.stat-value');
            statValues.forEach(el => {
                if(el.textContent.includes('/') || el.textContent === 'N/A') return;

                const target = parseInt(el.textContent);
                if (!isNaN(target)) {
                    const duration = 1500;
                    const step = target / (duration / 30);
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

        // Function to update the clock and date
        function updateClock() {
            try {
                const now = new Date();

                // Update time with leading zeros
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');

                const timeElement = document.getElementById('digital-time');
                if (timeElement) {
                    timeElement.textContent = `${hours}:${minutes}:${seconds}`;
                }

                // Update date display in long format
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                const dateString = now.toLocaleDateString('en-US', options);

                const dateElement = document.getElementById('digital-date');
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

                const greetingElement = document.getElementById('digital-greeting');
                if (greetingElement) {
                    greetingElement.textContent = `${greeting}, {{ $fname ?? 'Nurse' }}`;
                }
            } catch (error) {
                console.error("Clock update error:", error);
            }
        }
    </script>
@endsection
