@extends('firebase.layoutadmin')

@section('title', 'Admin Dashboard')

@section('content')
    <style>
        /* Dashboard specific styles */
        .welcome-header {
            font-family: 'Orbitron', sans-serif;
            color: var(--primary);
            letter-spacing: 2px;
            margin-bottom: 30px;
            text-shadow: 0 0 10px rgba(0, 195, 255, 0.5);
            position: relative;
            display: inline-block;
        }

        .welcome-header::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 80px;
            height: 2px;
            background: linear-gradient(to right, var(--primary), transparent);
        }

        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(21, 32, 43, 0.8);
            border-radius: 12px;
            overflow: hidden;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 195, 255, 0.2);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            transition: all 0.3s;
            position: relative;
            padding: 25px;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4), 0 0 15px rgba(0, 195, 255, 0.3);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, var(--primary), var(--accent));
        }

        .stat-card .stat-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 48px;
            opacity: 0.2;
            color: var(--primary);
        }

        .stat-card .stat-value {
            font-family: 'Orbitron', sans-serif;
            font-size: 36px;
            font-weight: 700;
            margin: 10px 0;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-card .stat-label {
            font-size: 16px;
            color: #fff;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .quick-actions {
            margin-bottom: 30px;
        }

        .action-header {
            font-family: 'Orbitron', sans-serif;
            color: var(--primary);
            font-size: 20px;
            margin-bottom: 15px;
            letter-spacing: 1px;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .action-button {
            background: rgba(21, 32, 43, 0.8);
            border: 1px solid rgba(0, 195, 255, 0.2);
            border-radius: 8px;
            padding: 15px;
            color: white;
            text-align: center;
            transition: all 0.3s;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .action-button:hover {
            background: rgba(0, 119, 255, 0.2);
            border-color: var(--primary);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3), 0 0 10px rgba(0, 195, 255, 0.3);
            color: var(--primary);
        }

        .action-button i {
            font-size: 24px;
        }

        .recent-activity {
            background: rgba(21, 32, 43, 0.8);
            border-radius: 12px;
            overflow: hidden;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 195, 255, 0.2);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }

        .activity-header {
            background: linear-gradient(90deg, rgba(21, 32, 43, 0.9), rgba(16, 25, 36, 0.9));
            color: var(--primary);
            font-family: 'Orbitron', sans-serif;
            padding: 15px 20px;
            font-size: 18px;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(0, 195, 255, 0.2);
        }

        .activity-list {
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .activity-item {
            padding: 15px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            transition: all 0.3s;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-item:hover {
            background: rgba(0, 119, 255, 0.1);
        }

        .activity-item .activity-time {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 4px;
        }

        .activity-item .activity-icon {
            margin-right: 10px;
            width: 30px;
            height: 30px;
            background: rgba(0, 119, 255, 0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
        }

        /* Animated elements */
        @keyframes pulse {
            0% { opacity: 0.7; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.05); }
            100% { opacity: 0.7; transform: scale(1); }
        }

        .pulse-slow {
            animation: pulse 3s infinite;
        }

        /* System status indicator */
        .system-status {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 8px 15px;
            background: rgba(0, 255, 157, 0.2);
            border-radius: 20px;
            color: var(--success);
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-dot {
            width: 10px;
            height: 10px;
            background: var(--success);
            border-radius: 50%;
            display: inline-block;
            animation: pulse 2s infinite;
        }

        /* Dashboard Container - Similar to Pharmacist Dashboard */
        .dashboard-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }

        .main-content {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .sidebar-content {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .dashboard-card {
            background: rgba(21, 32, 43, 0.8);
            border-radius: 12px;
            overflow: hidden;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 195, 255, 0.2);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }

        .dashboard-card-header {
            background: linear-gradient(90deg, rgba(21, 32, 43, 0.9), rgba(16, 25, 36, 0.9));
            color: var(--primary);
            font-family: 'Orbitron', sans-serif;
            padding: 15px 20px;
            font-size: 18px;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(0, 195, 255, 0.2);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dashboard-card-body {
            padding: 20px;
        }

        .system-overview {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .overview-item {
            background: rgba(16, 25, 36, 0.5);
            border-radius: 8px;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            border: 1px solid rgba(0, 195, 255, 0.1);
        }

        .overview-icon {
            width: 45px;
            height: 45px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
        }

        .overview-info {
            flex: 1;
        }

        .overview-value {
            font-size: 20px;
            font-weight: 700;
            color: white;
            margin-bottom: 5px;
        }

        .overview-label {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .staff-list {
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .staff-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .staff-item:last-child {
            border-bottom: none;
        }

        .staff-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            margin-right: 15px;
        }

        .staff-info {
            flex: 1;
        }

        .staff-name {
            font-size: 16px;
            color: white;
            margin-bottom: 3px;
            font-weight: 500;
        }

        .staff-role {
            font-size: 12px;
            color: var(--primary);
        }

        .system-metrics {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .metric-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .metric-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .metric-label {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
        }

        .metric-value {
            font-size: 14px;
            color: white;
            font-weight: 600;
        }

        .metric-bar {
            height: 5px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            overflow: hidden;
        }

        .metric-progress {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }

        /* For smaller screens */
        @media (max-width: 992px) {
            .dashboard-container {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="container position-relative">
        <!-- System status indicator -->
        <div class="system-status">
            <span class="status-dot"></span>
            System Online
        </div>

        <!-- Welcome header -->
        <h1 class="welcome-header mb-4">Welcome, {{ $fname }}</h1>

        <!-- Dashboard stats -->
        <div class="dashboard-stats">
            <div class="stat-card">
                <i class="fas fa-user-md stat-icon"></i>
                <div class="stat-label">Registered Doctors</div>
                <div class="stat-value">{{ $doctorCount ?? 12 }}</div>
                <div class="progress mt-2" style="height: 5px;">
                    <div class="progress-bar bg-info" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>

            <div class="stat-card">
                <i class="fas fa-user-nurse stat-icon"></i>
                <div class="stat-label">Registered Nurses</div>
                <div class="stat-value">{{ $nurseCount ?? 24 }}</div>
                <div class="progress mt-2" style="height: 5px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>

            <div class="stat-card">
                <i class="fas fa-prescription-bottle-alt stat-icon"></i>
                <div class="stat-label">Pharmacists</div>
                <div class="stat-value">{{ $pharmacistCount ?? 8 }}</div>
                <div class="progress mt-2" style="height: 5px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 45%" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>

            <div class="stat-card">
                <i class="fas fa-procedures stat-icon"></i>
                <div class="stat-label">Active Patients</div>
                <div class="stat-value">{{ $patientCount ?? 132 }}</div>
                <div class="progress mt-2" style="height: 5px;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: 85%" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>

        <!-- Dashboard Container - Similar to Pharmacist Dashboard -->
        <div class="dashboard-container">
            <!-- Main Content Area -->
            <div class="main-content">
                <!-- System Overview -->
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <i class="fas fa-chart-line"></i> System Overview
                    </div>
                    <div class="dashboard-card-body">
                        <div class="system-overview">
                            <div class="overview-item">
                                <div class="overview-icon">
                                    <i class="fas fa-hospital-user"></i>
                                </div>
                                <div class="overview-info">
                                    <div class="overview-value">{{ $patientCount ?? 132 }}</div>
                                    <div class="overview-label">Total Patients</div>
                                </div>
                            </div>

                            <div class="overview-item">
                                <div class="overview-icon">
                                    <i class="fas fa-file-medical"></i>
                                </div>
                                <div class="overview-info">
                                    <div class="overview-value">{{ $reportsCount ?? 98 }}</div>
                                    <div class="overview-label">Medical Reports</div>
                                </div>
                            </div>

                            <div class="overview-item">
                                <div class="overview-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="overview-info">
                                    <div class="overview-value">{{ $appointmentsCount ?? 45 }}</div>
                                    <div class="overview-label">Appointments</div>
                                </div>
                            </div>

                            <div class="overview-item">
                                <div class="overview-icon">
                                    <i class="fas fa-pills"></i>
                                </div>
                                <div class="overview-info">
                                    <div class="overview-value">{{ $prescriptionsCount ?? 76 }}</div>
                                    <div class="overview-label">Prescriptions</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick actions -->
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <i class="fas fa-bolt"></i> Quick Actions
                    </div>
                    <div class="dashboard-card-body">
                        <div class="action-buttons">
                            <a href="{{ route('firebase.admin.reg_doctor') }}" class="action-button">
                                <i class="fas fa-user-md"></i>
                                <span>Register Doctor</span>
                            </a>

                            <a href="{{ route('firebase.admin.reg_nurse') }}" class="action-button">
                                <i class="fas fa-user-nurse"></i>
                                <span>Register Nurse</span>
                            </a>

                            <a href="{{ route('firebase.admin.reg_pharmacist') }}" class="action-button">
                                <i class="fas fa-prescription-bottle-alt"></i>
                                <span>Register Pharmacist</span>
                            </a>

                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <i class="fas fa-history"></i> Recent Activity
                    </div>
                    <div class="dashboard-card-body">
                        <ul class="activity-list">
                            <li class="activity-item">
                                <div class="d-flex align-items-center">
                                <span class="activity-icon">
                                    <i class="fas fa-user-plus"></i>
                                </span>
                                    <div>
                                        <div>New doctor added to the system</div>
                                        <div class="activity-time">Today, 09:45 AM</div>
                                    </div>
                                </div>
                            </li>
                            <li class="activity-item">
                                <div class="d-flex align-items-center">
                                <span class="activity-icon">
                                    <i class="fas fa-user-edit"></i>
                                </span>
                                    <div>
                                        <div>Nurse information updated</div>
                                        <div class="activity-time">Yesterday, 04:30 PM</div>
                                    </div>
                                </div>
                            </li>
                            <li class="activity-item">
                                <div class="d-flex align-items-center">
                                <span class="activity-icon">
                                    <i class="fas fa-clipboard-check"></i>
                                </span>
                                    <div>
                                        <div>System maintenance completed</div>
                                        <div class="activity-time">April 15, 2025, 11:20 AM</div>
                                    </div>
                                </div>
                            </li>
                            <li class="activity-item">
                                <div class="d-flex align-items-center">
                                <span class="activity-icon">
                                    <i class="fas fa-server"></i>
                                </span>
                                    <div>
                                        <div>Database backup performed</div>
                                        <div class="activity-time">April 14, 2025, 02:15 PM</div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Sidebar Content -->
            <div class="sidebar-content">
                <!-- Recent Staff -->
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <i class="fas fa-user-friends"></i> Recent Staff
                    </div>
                    <div class="dashboard-card-body">
                        <ul class="staff-list">
                            @if(isset($recentStaff) && count($recentStaff) > 0)
                                @foreach($recentStaff as $staff)
                                    <li class="staff-item">
                                        <div class="staff-avatar">
                                            <i class="fas {{ $staff['role'] == 'doctor' ? 'fa-user-md' : ($staff['role'] == 'nurse' ? 'fa-user-nurse' : 'fa-prescription-bottle-alt') }}"></i>
                                        </div>
                                        <div class="staff-info">
                                            <div class="staff-name">{{ $staff['name'] }}</div>
                                            <div class="staff-role">{{ ucfirst($staff['role']) }}</div>
                                        </div>
                                    </li>
                                @endforeach
                            @else
                                <li class="staff-item">
                                    <div class="staff-avatar">
                                        <i class="fas fa-user-md"></i>
                                    </div>
                                    <div class="staff-info">
                                        <div class="staff-name">No staff data available</div>
                                        <div class="staff-role">Please refresh</div>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- System Metrics -->
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <i class="fas fa-server"></i> System Metrics
                    </div>
                    <div class="dashboard-card-body">
                        <div class="system-metrics">
                            <div class="metric-item">
                                <div class="metric-header">
                                    <div class="metric-label">CPU Usage</div>
                                    <div class="metric-value">42%</div>
                                </div>
                                <div class="metric-bar">
                                    <div class="metric-progress" style="width: 42%"></div>
                                </div>
                            </div>

                            <div class="metric-item">
                                <div class="metric-header">
                                    <div class="metric-label">Memory Usage</div>
                                    <div class="metric-value">68%</div>
                                </div>
                                <div class="metric-bar">
                                    <div class="metric-progress" style="width: 68%"></div>
                                </div>
                            </div>

                            <div class="metric-item">
                                <div class="metric-header">
                                    <div class="metric-label">Storage</div>
                                    <div class="metric-value">35%</div>
                                </div>
                                <div class="metric-bar">
                                    <div class="metric-progress" style="width: 35%"></div>
                                </div>
                            </div>

                            <div class="metric-item">
                                <div class="metric-header">
                                    <div class="metric-label">Database</div>
                                    <div class="metric-value">53%</div>
                                </div>
                                <div class="metric-bar">
                                    <div class="metric-progress" style="width: 53%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calendar -->
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <i class="fas fa-calendar-alt"></i> Calendar
                    </div>
                    <div class="dashboard-card-body">
                        <div class="text-center mb-3">
                            <h5 class="m-0">April 2025</h5>
                        </div>
                        <table class="table table-sm table-dark" style="background: transparent; margin-bottom: 0;">
                            <thead>
                            <tr>
                                <th>Su</th>
                                <th>Mo</th>
                                <th>Tu</th>
                                <th>We</th>
                                <th>Th</th>
                                <th>Fr</th>
                                <th>Sa</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td></td>
                                <td></td>
                                <td>1</td>
                                <td>2</td>
                                <td>3</td>
                                <td>4</td>
                                <td>5</td>
                            </tr>
                            <tr>
                                <td>6</td>
                                <td>7</td>
                                <td>8</td>
                                <td>9</td>
                                <td>10</td>
                                <td>11</td>
                                <td>12</td>
                            </tr>
                            <tr>
                                <td>13</td>
                                <td>14</td>
                                <td>15</td>
                                <td>16</td>
                                <td>17</td>
                                <td style="background: rgba(0, 119, 255, 0.2); color: var(--primary); font-weight: bold;">18</td>
                                <td>19</td>
                            </tr>
                            <tr>
                                <td>20</td>
                                <td>21</td>
                                <td>22</td>
                                <td>23</td>
                                <td>24</td>
                                <td>25</td>
                                <td>26</td>
                            </tr>
                            <tr>
                                <td>27</td>
                                <td>28</td>
                                <td>29</td>
                                <td>30</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add some interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            // Animation for stat cards
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        card.style.transition = 'all 0.5s ease';
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 50);
                }, index * 100);
            });

            // Simulate loading stats with counting animation
            const statValues = document.querySelectorAll('.stat-value, .overview-value');
            statValues.forEach(el => {
                const target = parseInt(el.textContent);
                if (!isNaN(target)) {
                    const duration = 2000; // 2 seconds
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

            // Animate dashboard cards
            const dashboardCards = document.querySelectorAll('.dashboard-card');
            dashboardCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        card.style.transition = 'all 0.5s ease';
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 50);
                }, 400 + (index * 100)); // Start after stat cards finish
            });
        });
    </script>
@endsection
