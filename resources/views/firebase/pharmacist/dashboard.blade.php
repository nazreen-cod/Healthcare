
@extends('firebase.layoutpharmacist')

@section('title', 'Pharmacist Dashboard')

@section('content')
    <style>
        /* Core Dashboard Styles */
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 15px;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }

        .main-column, .side-column {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Header & Welcome Card */
        .welcome-card {
            background: rgba(16, 25, 36, 0.7);
            border-radius: 15px;
            border: 1px solid rgba(255, 187, 0, 0.2);
            padding: 25px;
            position: relative;
            overflow: hidden;
        }

        .welcome-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIj48Y2lyY2xlIGN4PSI1MCIgY3k9IjUwIiByPSI0MCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSJyZ2JhKDI1NSwgMTg3LCAwLCAwLjA1KSIgc3Ryb2tlLXdpZHRoPSIyIi8+PC9zdmc+');
            background-size: 300px 300px;
            background-position: center;
            opacity: 0.4;
            z-index: 0;
        }

        .welcome-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .welcome-text {
            font-family: 'Orbitron', sans-serif;
            font-size: 2rem;
            background: linear-gradient(to right, #ffbb00, #ff9800);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 10px;
            text-shadow: 0 2px 10px rgba(255, 187, 0, 0.2);
        }

        .welcome-subtitle {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 5px;
        }

        .digital-clock {
            font-family: 'Orbitron', sans-serif;
            text-align: right;
        }

        .digital-time {
            font-size: 2rem;
            font-weight: 700;
            color: #ffbb00;
        }

        .digital-date {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }

        /* Stats Cards */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        .stat-card {
            background: rgba(16, 25, 36, 0.7);
            border-radius: 12px;
            border: 1px solid rgba(255, 187, 0, 0.15);
            padding: 20px;
            position: relative;
            transition: all 0.3s;
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
            background: linear-gradient(to bottom, #ffbb00, #ff9800);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            border-color: rgba(255, 187, 0, 0.3);
        }

        .stat-icon {
            font-size: 2rem;
            color: #ffbb00;
            margin-bottom: 10px;
        }

        .stat-count {
            font-family: 'Orbitron', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 5px;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stat-progress {
            height: 6px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
            margin-top: 10px;
            overflow: hidden;
        }

        .stat-progress-bar {
            height: 100%;
            border-radius: 3px;
            background: linear-gradient(to right, #ffbb00, #ff9800);
        }

        /* Section Headers */
        .section-header {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.2rem;
            color: #ffbb00;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .section-header i {
            margin-right: 10px;
        }

        /* Modern Quick Actions */
        .modern-actions {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        .modern-action-btn {
            background: rgba(16, 25, 36, 0.7);
            border: 1px solid rgba(255, 187, 0, 0.15);
            border-radius: 10px;
            padding: 20px 10px;
            text-align: center;
            transition: all 0.3s ease;
            color: #e1e1e1;
            position: relative;
            overflow: hidden;
            text-decoration: none;
        }

        .modern-action-btn:hover {
            border-color: rgba(255, 187, 0, 0.4);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 187, 0, 0.15);
            color: white;
        }

        .modern-action-btn i {
            font-size: 2rem;
            color: #ffbb00;
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

        .notification-badge {
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

        /* Data Cards */
        .data-card {
            background: rgba(16, 25, 36, 0.7);
            border-radius: 12px;
            border: 1px solid rgba(255, 187, 0, 0.15);
            overflow: hidden;
        }

        .data-card-header {
            background: rgba(255, 187, 0, 0.1);
            padding: 15px 20px;
            font-family: 'Orbitron', sans-serif;
            color: #ffbb00;
            font-size: 1.1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .data-card-body {
            padding: 20px;
        }

        /* Tables */
        .modern-table {
            width: 100%;
            border-collapse: collapse;
        }

        .modern-table th {
            background: rgba(255, 187, 0, 0.1);
            color: rgba(255, 255, 255, 0.8);
            text-align: left;
            padding: 12px;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .modern-table td {
            padding: 12px;
            border-top: 1px solid rgba(255, 187, 0, 0.1);
            color: rgba(255, 255, 255, 0.7);
        }

        .modern-table tr:hover td {
            background: rgba(255, 187, 0, 0.05);
        }

        /* Status Badges */
        .status-badge {
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-pending {
            background: rgba(255, 187, 0, 0.15);
            color: #ffbb00;
        }

        .status-completed {
            background: rgba(0, 200, 83, 0.15);
            color: #00c853;
        }

        .status-urgent {
            background: rgba(255, 82, 82, 0.15);
            color: #ff5252;
        }

        /* Todo List */
        .todo-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .todo-item {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 187, 0, 0.1);
        }

        .todo-item:last-child {
            border-bottom: none;
        }

        .todo-checkbox {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 187, 0, 0.5);
            border-radius: 50%;
            margin-right: 15px;
            cursor: pointer;
            position: relative;
        }

        .todo-checkbox.checked::after {
            content: '';
            position: absolute;
            width: 10px;
            height: 10px;
            background: #ffbb00;
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .todo-text {
            flex-grow: 1;
            color: rgba(255, 255, 255, 0.7);
        }

        .todo-checkbox.checked + .todo-text {
            text-decoration: line-through;
            color: rgba(255, 255, 255, 0.4);
        }

        .todo-priority {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-left: 10px;
        }

        .priority-high {
            background: #ff5252;
        }

        .priority-medium {
            background: #ffbb00;
        }

        .priority-low {
            background: #00c853;
        }

        /* Inventory Chart */
        .inventory-chart-container {
            height: 250px;
            margin-top: 15px;
        }

        /* Responsive Styles */
        @media (max-width: 1200px) {
            .stats-row {
                grid-template-columns: repeat(2, 1fr);
            }

            .modern-actions {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 992px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .welcome-content {
                flex-direction: column;
                text-align: center;
            }

            .digital-clock {
                text-align: center;
                margin-top: 15px;
            }
        }

        @media (max-width: 576px) {
            .stats-row {
                grid-template-columns: 1fr;
            }

            .modern-actions {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="dashboard-container">
        <!-- Welcome Section with Digital Clock -->
        <div class="welcome-card">
            <div class="welcome-content">
                <div>
                    <h1 class="welcome-text">Welcome, {{ $fname }}!</h1>
                    <p class="welcome-subtitle">Pharmacy Management Dashboard</p>
                </div>
                <div class="digital-clock">
                    <div class="digital-time" id="live-time">00:00:00 AM</div>
                    <div class="digital-date">{{ now()->format('l, F j, Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Dashboard Main Grid -->
        <div class="dashboard-grid">
            <!-- Main Column -->
            <div class="main-column">
                <!-- Stats Overview -->
                <div>
                    <h2 class="section-header"><i class="fas fa-chart-pie"></i> Statistics Overview</h2>
                    <div class="stats-row">
                        <!-- Patients Today -->
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-count">{{ $patientsToday ?? 0 }}</div>
                            <div class="stat-label">Patients Today</div>
                            <div class="stat-progress">
                                <div class="stat-progress-bar" style="width: {{ min(($patientsToday ?? 0) * 5, 100) }}%;"></div>
                            </div>
                        </div>

                        <!-- Pending Prescriptions -->
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-prescription"></i>
                            </div>
                            <div class="stat-count">{{ $pendingCount ?? 0 }}</div>
                            <div class="stat-label">Pending Orders</div>
                            <div class="stat-progress">
                                <div class="stat-progress-bar" style="width: {{ min(($pendingCount ?? 0) * 10, 100) }}%;"></div>
                            </div>
                        </div>

                        <!-- Medications Dispensed -->
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-pills"></i>
                            </div>
                            <div class="stat-count">{{ $medicationsDispensed ?? 0 }}</div>
                            <div class="stat-label">Meds Dispensed</div>
                            <div class="stat-progress">
                                <div class="stat-progress-bar" style="width: {{ min(($medicationsDispensed ?? 0) * 2, 100) }}%;"></div>
                            </div>
                        </div>

                        <!-- Low Stock -->
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="stat-count">{{ $lowStockCount ?? 0 }}</div>
                            <div class="stat-label">Low Stock Items</div>
                            <div class="stat-progress">
                                <div class="stat-progress-bar" style="width: {{ min(($lowStockCount ?? 0) * 8, 100) }}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div>
                    <h2 class="section-header"><i class="fas fa-bolt"></i> Quick Actions</h2>
                    <div class="modern-actions">
                        <a href="{{ route('firebase.pharmacist.search') }}" class="modern-action-btn">
                            <i class="fas fa-search"></i>
                            <div class="modern-action-label">SEARCH</div>
                        </a>

                        <a href="{{ route('firebase.pharmacist.inventory') }}" class="modern-action-btn">
                            <i class="fas fa-boxes"></i>
                            <div class="modern-action-label">INVENTORY</div>
                            @if(isset($lowStockCount) && $lowStockCount > 0)
                                <span class="notification-badge">
                                {{ $lowStockCount }}
                            </span>
                            @endif
                        </a>

                        <a href="{{ route('firebase.pharmacist.inventory') }}" class="modern-action-btn">
                            <i class="fas fa-chart-bar"></i>
                            <div class="modern-action-label">REPORTS</div>
                        </a>
                    </div>
                </div>

                <!-- Recent Medication Pickups -->
                <div class="data-card">
                    <div class="data-card-header">
                        <span><i class="fas fa-clipboard-check me-2"></i> Recent Medication Pickups</span>
                        <a href="{{ route('firebase.pharmacist.inventory') }}" class="btn btn-sm" style="background: rgba(255, 187, 0, 0.2); color: #ffbb00; border: 1px solid rgba(255, 187, 0, 0.3);">
                            View All
                        </a>
                    </div>
                    <div class="data-card-body">
                        @if(isset($recentPickups) && count($recentPickups) > 0)
                            <div class="table-responsive">
                                <table class="modern-table">
                                    <thead>
                                    <tr>
                                        <th>Date/Time</th>
                                        <th>Patient</th>
                                        <th>Items</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($recentPickups as $pickup)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($pickup['picked_up_at'])->format('M d, H:i') }}</td>
                                            <td>{{ $pickup['patient_name'] }}</td>
                                            <td>
                                                @if(isset($pickup['medications']) && is_array($pickup['medications']))
                                                    {{ count($pickup['medications']) }}
                                                @else
                                                    1
                                                @endif
                                            </td>
                                            <td><span class="status-badge status-completed">Dispensed</span></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-clipboard-list mb-3" style="font-size: 2rem; color: #ffbb00;"></i>
                                <p class="mb-0">No recent medication pickups</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Side Column -->
            <div class="side-column">
                <!-- Pending Prescriptions -->
                <div class="data-card">
                    <div class="data-card-header">
                        <span><i class="fas fa-prescription me-2"></i> Pending Prescriptions</span>
                        <span class="badge bg-warning text-dark">{{ $pendingCount ?? 0 }}</span>
                    </div>
                    <div class="data-card-body">
                        <div class="table-responsive">
                            <table class="modern-table">
                                <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($pendingPrescriptions) && count($pendingPrescriptions) > 0)
                                    @foreach($pendingPrescriptions as $prescription)
                                        <tr>
                                            <td>{{ $prescription['patient_name'] }}</td>
                                            <td>{{ \Carbon\Carbon::parse($prescription['date'])->format('M d') }}</td>
                                            <td>
                                                <span class="status-badge
                                                    @if($prescription['priority'] == 'high') status-urgent
                                                    @elseif($prescription['priority'] == 'medium') status-pending
                                                    @else status-pending @endif">
                                                    {{ ucfirst($prescription['priority'] ?? 'Normal') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3" class="text-center">No pending prescriptions</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Medications -->
                <div class="data-card">
                    <div class="data-card-header">
                        <span><i class="fas fa-exclamation-triangle me-2"></i> Low Stock Medications</span>
                        <span class="badge bg-danger">{{ $lowStockCount ?? 0 }}</span>
                    </div>
                    <div class="data-card-body">
                        <div class="table-responsive">
                            <table class="modern-table">
                                <thead>
                                <tr>
                                    <th>Medication</th>
                                    <th>Current Stock</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($lowStockItems) && count($lowStockItems) > 0)
                                    @foreach($lowStockItems as $item)
                                        <tr>
                                            <td>{{ $item['medication_name'] }}</td>
                                            <td>{{ $item['quantity'] }}</td>
                                            <td>
                                                <span class="status-badge
                                                    @if($item['quantity'] <= 3) status-urgent
                                                    @else status-pending @endif">
                                                    {{ $item['quantity'] <= 3 ? 'Critical' : 'Low' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3" class="text-center">No low stock medications</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Daily Tasks -->
                <div class="data-card">
                    <div class="data-card-header">
                        <span><i class="fas fa-tasks me-2"></i> Daily Tasks</span>
                        <a href="#" class="btn btn-sm" style="background: rgba(255, 187, 0, 0.2); color: #ffbb00; border: 1px solid rgba(255, 187, 0, 0.3);">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                    <div class="data-card-body">
                        <ul class="todo-list">
                            <li class="todo-item">
                                <div class="todo-checkbox checked"></div>
                                <div class="todo-text">Check inventory levels</div>
                                <div class="todo-priority priority-high"></div>
                            </li>
                            <li class="todo-item">
                                <div class="todo-checkbox"></div>
                                <div class="todo-text">Process pending prescriptions</div>
                                <div class="todo-priority priority-high"></div>
                            </li>
                            <li class="todo-item">
                                <div class="todo-checkbox"></div>
                                <div class="todo-text">Order new supplies</div>
                                <div class="todo-priority priority-medium"></div>
                            </li>
                            <li class="todo-item">
                                <div class="todo-checkbox checked"></div>
                                <div class="todo-text">Update medication database</div>
                                <div class="todo-priority priority-low"></div>
                            </li>
                            <li class="todo-item">
                                <div class="todo-checkbox"></div>
                                <div class="todo-text">Staff meeting at 3 PM</div>
                                <div class="todo-priority priority-medium"></div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Live clock update
        function updateTime() {
            const timeElement = document.getElementById('live-time');
            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const seconds = now.getSeconds().toString().padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';
            const formattedHours = (hours % 12 || 12).toString().padStart(2, '0');

            timeElement.textContent = `${formattedHours}:${minutes}:${seconds} ${ampm}`;
        }

        // Update time every second
        setInterval(updateTime, 1000);
        updateTime(); // Initial call

        // Todo list functionality
        document.addEventListener('DOMContentLoaded', function() {
            const todoCheckboxes = document.querySelectorAll('.todo-checkbox');
            todoCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('click', function() {
                    this.classList.toggle('checked');
                });
            });

            // Counter animation for stat values
            const statCounts = document.querySelectorAll('.stat-count');
            statCounts.forEach(el => {
                const finalValue = parseFloat(el.textContent);
                if (!isNaN(finalValue)) {
                    let startValue = 0;
                    const duration = 1500;
                    const increment = finalValue / (duration / 30);
                    const animateCount = () => {
                        startValue += increment;
                        if (startValue < finalValue) {
                            el.textContent = Math.floor(startValue);
                            requestAnimationFrame(animateCount);
                        } else {
                            el.textContent = finalValue;
                        }
                    };
                    requestAnimationFrame(animateCount);
                }
            });
        });
    </script>
@endsection
