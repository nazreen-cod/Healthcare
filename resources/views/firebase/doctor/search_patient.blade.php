@extends('firebase.layoutdoctor')

@section('title', 'Search Patient')

@section('content')
    <style>
        /* Compact Design - Minimize Scrolling */
        .search-container {
            padding: 10px 0;
        }

        .search-header {
            font-family: 'Orbitron', sans-serif;
            color: var(--primary);
            margin-bottom: 1rem;
            font-size: 1.2rem;
            letter-spacing: 1px;
            position: relative;
            display: inline-block;
        }

        .search-header::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 60px;
            height: 2px;
            background: linear-gradient(to right, var(--primary), transparent);
        }

        /* Compact Card */
        .cyber-card {
            background: rgba(16, 25, 36, 0.7);
            border-radius: 8px;
            border: 1px solid rgba(0, 195, 255, 0.2);
            padding: 15px;
            margin-bottom: 15px;
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
            width: 3px;
            height: 100%;
            background: linear-gradient(to bottom, var(--primary), var(--secondary));
        }

        /* Compact Form */
        .compact-form {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: stretch;
        }

        .compact-form .search-input-wrapper {
            position: relative;
            flex: 1;
            min-width: 200px;
        }

        .compact-form .search-type {
            width: 120px;
        }

        .compact-form .search-btn {
            width: auto;
        }

        .form-control {
            background: rgba(16, 25, 36, 0.6);
            border: 1px solid rgba(0, 195, 255, 0.2);
            border-radius: 6px;
            color: white;
            height: 38px;
            padding: 6px 12px;
            padding-left: 32px;
            font-size: 0.9rem;
        }

        .search-icon {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            color: var(--primary);
            font-size: 0.9rem;
        }

        /* Search Button */
        .btn-cyber-search {
            background: linear-gradient(45deg, var(--secondary), var(--primary));
            border: none;
            color: white;
            border-radius: 6px;
            padding: 8px 15px;
            font-weight: 500;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-size: 0.85rem;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* Compact Table */
        .table-cyber {
            width: 100%;
            color: #fff;
            font-size: 0.85rem;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 1rem;
        }

        .table-cyber thead th {
            background: rgba(0, 119, 255, 0.2);
            color: var(--primary);
            padding: 8px 10px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-size: 0.8rem;
            border-bottom: 1px solid rgba(0, 195, 255, 0.2);
        }

        .table-cyber tbody tr {
            background: rgba(16, 25, 36, 0.6);
        }

        .table-cyber tbody tr:hover {
            background: rgba(0, 119, 255, 0.1);
        }

        .table-cyber td {
            padding: 8px 10px;
            border-bottom: 1px solid rgba(0, 195, 255, 0.1);
        }

        /* Compact Patient Card */
        .patient-card {
            margin-bottom: 15px;
            background: rgba(16, 25, 36, 0.7);
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid rgba(0, 195, 255, 0.2);
        }

        .patient-header {
            background: linear-gradient(90deg, rgba(0, 119, 255, 0.2), rgba(0, 195, 255, 0.2));
            padding: 8px 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(0, 195, 255, 0.2);
        }

        .patient-info-header {
            display: flex;
            align-items: center;
        }

        .patient-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            color: white;
            font-size: 16px;
        }

        .patient-name {
            margin: 0;
            font-weight: 600;
            font-size: 1rem;
            color: white;
        }

        .patient-id {
            margin: 0;
            font-size: 0.8rem;
            color: var(--primary);
        }

        .patient-tabs {
            padding: 0 5px;
            background: rgba(16, 25, 36, 0.4);
            border-bottom: 1px solid rgba(0, 195, 255, 0.2);
        }

        .patient-tabs .nav-link {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.8rem;
            padding: 6px 10px;
            border: none;
            border-radius: 0;
        }

        .patient-tabs .nav-link.active {
            background: transparent;
            color: white;
            position: relative;
        }

        .patient-tabs .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(to right, var(--primary), var(--secondary));
        }

        .patient-tabs .nav-link i {
            margin-right: 5px;
            width: 14px;
            text-align: center;
        }

        .tab-content {
            padding: 12px;
        }

        /* Compact Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 10px;
            font-size: 0.85rem;
        }

        .info-grid .info-item {
            margin-bottom: 0;
        }

        .info-item .info-label {
            color: var(--primary);
            font-weight: 500;
            font-size: 0.8rem;
            margin-bottom: 2px;
            display: flex;
            align-items: center;
        }

        .info-item .info-label i {
            margin-right: 5px;
            width: 14px;
            text-align: center;
            font-size: 0.8rem;
        }

        .info-item .info-value {
            color: #e1e1e1;
            padding-left: 19px;
        }

        /* Vital Signs */
        .vitals-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
        }

        .vital-card {
            background: rgba(0, 119, 255, 0.1);
            border-radius: 6px;
            padding: 8px 10px;
            border-left: 2px solid var(--primary);
            font-size: 0.8rem;
        }

        .vital-card.warning {
            background: rgba(255, 187, 0, 0.1);
            border-left-color: #ffbb00;
        }

        .vital-card.danger {
            background: rgba(255, 58, 58, 0.1);
            border-left-color: #ff3a3a;
        }

        .vital-header {
            display: flex;
            align-items: center;
            color: var(--primary);
            font-size: 0.75rem;
            margin-bottom: 3px;
        }

        .vital-header i {
            margin-right: 5px;
            width: 14px;
            text-align: center;
        }

        .vital-value {
            font-size: 0.95rem;
            font-weight: 600;
            color: white;
        }

        .vital-status {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.7);
        }

        /* Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 500;
        }

        .badge-normal {
            background: rgba(0, 195, 255, 0.15);
            color: var(--primary);
        }

        .badge-warning {
            background: rgba(255, 187, 0, 0.15);
            color: #ffbb00;
        }

        .badge-danger {
            background: rgba(255, 58, 58, 0.15);
            color: #ff3a3a;
        }

        .status-badge i {
            margin-right: 3px;
            font-size: 0.7rem;
        }

        /* Actions Bar */
        .actions-bar {
            display: flex;
            gap: 6px;
            padding: 8px 12px;
            background: rgba(16, 25, 36, 0.4);
            border-top: 1px solid rgba(0, 195, 255, 0.1);
        }

        .btn-sm-action {
            background: linear-gradient(45deg, var(--secondary), var(--primary));
            border: none;
            color: white;
            border-radius: 4px;
            padding: 5px 8px;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
        }

        .btn-sm-action i {
            margin-right: 3px;
        }

        /* Medical Alert */
        .alert-box {
            background: rgba(255, 187, 0, 0.1);
            border-left: 2px solid #ffbb00;
            border-radius: 4px;
            padding: 6px 10px;
            margin-bottom: 10px;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
        }

        .alert-box.danger {
            background: rgba(255, 58, 58, 0.1);
            border-left-color: #ff3a3a;
        }

        .alert-box i {
            color: #ffbb00;
            margin-right: 8px;
            font-size: 0.9rem;
        }

        .alert-box.danger i {
            color: #ff3a3a;
        }

        /* Dashboard Cards */
        .dashboard-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }

        .dash-card {
            background: rgba(16, 25, 36, 0.7);
            border-radius: 6px;
            border: 1px solid rgba(0, 195, 255, 0.2);
            padding: 10px;
            flex: 1;
            min-width: 120px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .dash-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            margin: 5px 0;
        }

        .dash-label {
            color: var(--primary);
            font-size: 0.75rem;
        }

        .dash-icon {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
        }

        /* Recent Patients */
        .recent-list {
            margin-top: 5px;
        }

        .recent-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 10px;
            border-bottom: 1px solid rgba(0, 195, 255, 0.1);
        }

        .recent-info {
            display: flex;
            align-items: center;
        }

        .recent-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: rgba(0, 119, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 8px;
            color: var(--primary);
            font-size: 0.8rem;
        }

        .recent-name {
            font-weight: 500;
            color: white;
            margin: 0;
            font-size: 0.85rem;
        }

        .recent-details {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.75rem;
            margin: 0;
        }

        /* Animation */
        .animate-in {
            animation: fadeIn 0.4s ease forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .info-grid, .vitals-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .info-grid, .vitals-grid {
                grid-template-columns: 1fr;
            }

            .patient-tabs .nav-link {
                padding: 6px 8px;
                font-size: 0.75rem;
            }
        }
    </style>

    <div class="container search-container">
        <h2 class="search-header mb-3"><i class="fas fa-search me-2"></i>Patient Search</h2>

        @if(session()->has("error"))
            <div class="alert-box danger animate-in">
                <i class="fas fa-exclamation-circle"></i>
                {{ session()->get("error") }}
            </div>
        @endif

        <!-- Dashboard Cards for Quick Access -->
        @if(!isset($patients) || empty($patients))
            <div class="dashboard-row animate-in">
                <div class="dash-card">
                    <div class="dash-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="dash-value">{{ $todayAppointments ?? 0 }}</div>
                    <div class="dash-label">Today's Appointments</div>
                </div>

                <div class="dash-card">
                    <div class="dash-icon">
                        <i class="fas fa-file-medical"></i>
                    </div>
                    <div class="dash-value">{{ $pendingPatients ?? 0 }}</div>
                    <div class="dash-label">Pending Reports</div>
                </div>

                <div class="dash-card">
                    <div class="dash-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="dash-value">{{ $totalPatients ?? 0 }}</div>
                    <div class="dash-label">Total Patients</div>
                </div>
            </div>

            <!-- Recent Patients Needing Reports -->
            @if(isset($recentPatients) && count($recentPatients) > 0)
                <div class="cyber-card animate-in">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div style="color: var(--primary); font-size: 0.9rem; font-weight: 500;">
                            <i class="fas fa-history me-2"></i>Recent Patients Needing Reports
                        </div>
                    </div>

                    <div class="recent-list">
                        @foreach($recentPatients as $rPatient)
                            <div class="recent-item">
                                <div class="recent-info">
                                    <div class="recent-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <h6 class="recent-name">{{ $rPatient['name'] }}</h6>
                                        <p class="recent-details">
                                            {{ $rPatient['gender'] }}, {{ $rPatient['age'] }} | ID: {{ $rPatient['id_no'] }}
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ route('firebase.doctor.medical_report', ['id' => $rPatient['id']]) }}"
                                   class="btn-sm-action">
                                    <i class="fas fa-file-medical"></i> Report
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif

        <!-- Compact Search Form -->
        <div class="cyber-card animate-in">
            <form method="GET" action="{{ route('firebase.doctor.searchPatient') }}">
                <div class="compact-form">
                    <div class="search-input-wrapper">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="search" name="search" class="form-control"
                               placeholder="Patient Name, ID No, Visit No." required
                               value="{{ $searchQuery ?? '' }}">
                    </div>

                    <select name="search_type" class="form-control search-type">
                        <option value="all" {{ isset($searchType) && $searchType == 'all' ? 'selected' : '' }}>All Fields</option>
                        <option value="name" {{ isset($searchType) && $searchType == 'name' ? 'selected' : '' }}>Name</option>
                        <option value="id_no" {{ isset($searchType) && $searchType == 'id_no' ? 'selected' : '' }}>ID</option>
                        <option value="visit" {{ isset($searchType) && $searchType == 'visit' ? 'selected' : '' }}>Visit</option>
                    </select>

                    <button type="submit" class="btn-cyber-search search-btn">
                        <i class="fas fa-search me-1"></i>Search
                    </button>
                </div>
            </form>
        </div>

        <!-- Results Table -->
        @if(isset($patients) && count($patients) > 0)
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div style="color: var(--primary); font-size: 0.9rem; font-weight: 500;">
                    <i class="fas fa-clipboard-list me-2"></i>Search Results
                    <span style="font-size: 0.8rem; color: rgba(255, 255, 255, 0.7);">
                    ({{ count($patients) }} {{ Str::plural('patient', count($patients)) }})
                </span>
                </div>
            </div>

            <div class="table-responsive animate-in mb-3">
                <table class="table-cyber">
                    <thead>
                    <tr>
                        <th>Patient</th>
                        <th>ID No.</th>
                        <th>Gender/Age</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($patients as $patient)
                        <tr>
                            <td>{{ $patient['name'] ?? 'N/A' }}</td>
                            <td>{{ $patient['id_no'] ?? 'N/A' }}</td>
                            <td>{{ $patient['gender'] ?? 'N/A' }}, {{ $patient['age'] ?? 'N/A' }}</td>
                            <td style="white-space: nowrap;">
                                <a href="{{ route('firebase.doctor.medical_report', ['id' => $patient['id']]) }}"
                                   class="btn-sm-action">
                                    <i class="fas fa-file-medical"></i> Report
                                </a>

                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Patient Cards with Tabs -->
            @foreach($patients as $key => $patient)
                <div class="patient-card animate-in">
                    <div class="patient-header">
                        <div class="patient-info-header">
                            <div class="patient-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h4 class="patient-name">{{ $patient['name'] ?? 'N/A' }}</h4>
                                <p class="patient-id">ID: {{ $patient['id_no'] ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div>
                            @if(isset($patient['has_report']) && $patient['has_report'])
                                <span class="status-badge badge-normal">
                                <i class="fas fa-check-circle"></i> Has Report
                            </span>
                            @else
                                <span class="status-badge badge-warning">
                                <i class="fas fa-exclamation-circle"></i> Needs Report
                            </span>
                            @endif
                        </div>
                    </div>

                    <ul class="nav nav-tabs patient-tabs" id="patientTab-{{ $key }}" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="vitals-tab-{{ $key }}" data-bs-toggle="tab"
                                    data-bs-target="#vitals-{{ $key }}" type="button" role="tab">
                                <i class="fas fa-heartbeat"></i> Vitals
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="info-tab-{{ $key }}" data-bs-toggle="tab"
                                    data-bs-target="#info-{{ $key }}" type="button" role="tab">
                                <i class="fas fa-id-card"></i> Info
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="medical-tab-{{ $key }}" data-bs-toggle="tab"
                                    data-bs-target="#medical-{{ $key }}" type="button" role="tab">
                                <i class="fas fa-notes-medical"></i> Medical
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="patientTabContent-{{ $key }}">
                        <!-- Vitals Tab -->
                        <div class="tab-pane fade show active" id="vitals-{{ $key }}" role="tabpanel">
                            @if(!empty($patient['allergies']) || !empty($patient['medical_alerts']))
                                @if(!empty($patient['allergies']))
                                    <div class="alert-box">
                                        <i class="fas fa-allergies"></i>
                                        <strong>Allergies:</strong> {{ $patient['allergies'] }}
                                    </div>
                                @endif

                                @if(!empty($patient['medical_alerts']))
                                    <div class="alert-box danger">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Medical Alerts:</strong> {{ $patient['medical_alerts'] }}
                                    </div>
                                @endif
                            @endif

                            <div class="vitals-grid">
                                <!-- Blood Pressure -->
                                @php
                                    $bpClass = isset($patient['vital_signs']['blood_pressure']['status']) &&
                                              (strpos(strtolower($patient['vital_signs']['blood_pressure']['status'] ?? ''), 'normal') !== false) ?
                                              '' : (strpos(strtolower($patient['vital_signs']['blood_pressure']['status'] ?? ''), 'stage 2') !== false ||
                                              strpos(strtolower($patient['vital_signs']['blood_pressure']['status'] ?? ''), 'crisis') !== false ?
                                              'danger' : 'warning');
                                @endphp
                                <div class="vital-card {{ $bpClass }}">
                                    <div class="vital-header">
                                        <i class="fas fa-heart"></i> Blood Pressure
                                    </div>
                                    <div class="vital-value">{{ $patient['bloodpm'] ?? $patient['vital_signs']['blood_pressure']['value'] ?? 'N/A' }}</div>
                                    <div class="vital-status">{{ $patient['vital_signs']['blood_pressure']['status'] ?? '' }}</div>
                                </div>

                                <!-- Temperature -->
                                @php
                                    $tempClass = isset($patient['vital_signs']['temperature']['status']) &&
                                                (strpos(strtolower($patient['vital_signs']['temperature']['status'] ?? ''), 'normal') !== false) ?
                                                '' : (strpos(strtolower($patient['vital_signs']['temperature']['status'] ?? ''), 'high') !== false ||
                                                strpos(strtolower($patient['vital_signs']['temperature']['status'] ?? ''), 'severe') !== false ?
                                                'danger' : 'warning');
                                @endphp
                                <div class="vital-card {{ $tempClass }}">
                                    <div class="vital-header">
                                        <i class="fas fa-thermometer-half"></i> Temperature
                                    </div>
                                    <div class="vital-value">{{ $patient['tempreturec'] ?? $patient['vital_signs']['temperature']['value'] ?? 'N/A' }}</div>
                                    <div class="vital-status">{{ $patient['vital_signs']['temperature']['status'] ?? '' }}</div>
                                </div>

                                <!-- Pulse Rate -->
                                @php
                                    $pulseClass = isset($patient['vital_signs']['pulse']['status']) &&
                                                 (strpos(strtolower($patient['vital_signs']['pulse']['status'] ?? ''), 'normal') !== false) ?
                                                 '' : 'warning';
                                @endphp
                                <div class="vital-card {{ $pulseClass }}">
                                    <div class="vital-header">
                                        <i class="fas fa-heartbeat"></i> Pulse Rate
                                    </div>
                                    <div class="vital-value">{{ $patient['plusec'] ?? $patient['vital_signs']['pulse']['value'] ?? 'N/A' }}</div>
                                    <div class="vital-status">{{ $patient['vital_signs']['pulse']['status'] ?? '' }}</div>
                                </div>

                                <!-- Respiratory Rate -->
                                @php
                                    $respClass = isset($patient['vital_signs']['respiratory_rate']['status']) &&
                                                (strpos(strtolower($patient['vital_signs']['respiratory_rate']['status'] ?? ''), 'normal') !== false) ?
                                                '' : 'warning';
                                @endphp
                                <div class="vital-card {{ $respClass }}">
                                    <div class="vital-header">
                                        <i class="fas fa-lungs"></i> Respiratory Rate
                                    </div>
                                    <div class="vital-value">{{ $patient['respiratingr'] ?? $patient['vital_signs']['respiratory_rate']['value'] ?? 'N/A' }}</div>
                                    <div class="vital-status">{{ $patient['vital_signs']['respiratory_rate']['status'] ?? '' }}</div>
                                </div>

                                <!-- BMI -->
                                @php
                                    $bmiClass = isset($patient['vital_signs']['bmi']['status']) ?
                                               (strtolower($patient['vital_signs']['bmi']['status'] ?? '') == 'normal' ?
                                               '' : (strtolower($patient['vital_signs']['bmi']['status'] ?? '') == 'obese' ?
                                               'danger' : 'warning')) : '';
                                @endphp
                                <div class="vital-card {{ $bmiClass }}">
                                    <div class="vital-header">
                                        <i class="fas fa-calculator"></i> BMI
                                    </div>
                                    <div class="vital-value">{{ $patient['bmi'] ?? $patient['vital_signs']['bmi']['value'] ?? 'N/A' }}</div>
                                    <div class="vital-status">{{ $patient['bmi_status'] ?? $patient['vital_signs']['bmi']['status'] ?? '' }}</div>
                                </div>

                                <!-- Weight & Height -->
                                <div class="vital-card">
                                    <div class="vital-header">
                                        <i class="fas fa-ruler-combined"></i> Height/Weight
                                    </div>
                                    <div class="vital-value">{{ $patient['height'] ?? 'N/A' }} / {{ $patient['weight'] ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Info Tab -->
                        <div class="tab-pane fade" id="info-{{ $key }}" role="tabpanel">
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label"><i class="fas fa-venus-mars"></i> Gender</div>
                                    <div class="info-value">{{ $patient['gender'] ?? 'N/A' }}</div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label"><i class="fas fa-birthday-cake"></i> Age</div>
                                    <div class="info-value">{{ $patient['age'] ?? 'N/A' }}</div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label"><i class="fas fa-calendar-alt"></i> DOB</div>
                                    <div class="info-value">{{ $patient['dob'] ?? 'N/A' }}</div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label"><i class="fas fa-id-badge"></i> Visit No.</div>
                                    <div class="info-value">{{ $patient['visit_no'] ?? 'N/A' }}</div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label"><i class="fas fa-map-marker-alt"></i> Location</div>
                                    <div class="info-value">{{ $patient['location'] ?? 'N/A' }}</div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label"><i class="fas fa-phone"></i> Contact</div>
                                    <div class="info-value">{{ $patient['contact'] ?? 'N/A' }}</div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label"><i class="fas fa-flag"></i> Nationality</div>
                                    <div class="info-value">{{ $patient['nationality'] ?? 'N/A' }}</div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label"><i class="fas fa-users"></i> Race</div>
                                    <div class="info-value">{{ $patient['race'] ?? 'N/A' }}</div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label"><i class="fas fa-clock"></i> Screened</div>
                                    <div class="info-value">{{ $patient['screening_date'] ?? 'N/A' }}</div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label"><i class="fas fa-user-nurse"></i> By</div>
                                    <div class="info-value">{{ $patient['screened_by'] ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Medical Tab -->
                        <div class="tab-pane fade" id="medical-{{ $key }}" role="tabpanel">
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <div class="info-item mb-2">
                                        <div class="info-label"><i class="fas fa-history"></i> Medical History</div>
                                        <div class="info-value" style="white-space: pre-line;">{{ $patient['medical_history'] ?? 'No medical history recorded.' }}</div>
                                    </div>

                                    <div class="info-item mb-2">
                                        <div class="info-label"><i class="fas fa-users"></i> Family History</div>
                                        <div class="info-value" style="white-space: pre-line;">{{ $patient['family_history'] ?? 'No family history recorded.' }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="info-item mb-2">
                                        <div class="info-label"><i class="fas fa-heartbeat"></i> Chronic Conditions</div>
                                        <div class="info-value" style="white-space: pre-line;">{{ $patient['chronic_conditions'] ?? 'None recorded.' }}</div>
                                    </div>

                                    <div class="info-item mb-2">
                                        <div class="info-label"><i class="fas fa-pills"></i> Current Medications</div>
                                        <div class="info-value" style="white-space: pre-line;">{{ $patient['current_medications'] ?? 'None recorded.' }}</div>
                                    </div>

                                    <div class="info-item">
                                        <div class="info-label"><i class="fas fa-tint"></i> Blood Type</div>
                                        <div class="info-value">{{ $patient['blood_type'] ?? 'Not recorded' }}</div>
                                    </div>
                                </div>
                            </div>

                            @if(isset($patient['has_report']) && $patient['has_report'])
                                <div class="alert-box">
                                    <i class="fas fa-file-medical"></i>
                                    <div>
                                        <strong>Medical Report:</strong> Created on {{ $patient['report_date'] ?? 'unknown date' }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="actions-bar">
                        <a href="{{ route('firebase.doctor.medical_report', ['id' => $patient['id']]) }}"
                           class="btn-sm-action">
                            <i class="fas fa-file-medical"></i>
                            {{ isset($patient['has_report']) && $patient['has_report'] ? 'Update Report' : 'New Report' }}
                        </a>



                        @if(isset($patient['has_report']) && $patient['has_report'])

                        @endif
                    </div>
                </div>
            @endforeach

        @elseif(isset($patients))
            <div class="alert-box animate-in">
                <i class="fas fa-exclamation-triangle"></i>
                No patients found matching your search criteria.
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Store active tab when clicked
            const tabEls = document.querySelectorAll('[data-bs-toggle="tab"]');
            tabEls.forEach(tabEl => {
                tabEl.addEventListener('shown.bs.tab', event => {
                    const tabId = event.target.getAttribute('id');
                    localStorage.setItem(`activeDocTab-${tabId}`, 'true');
                });
            });

            // Restore active tabs
            tabEls.forEach(tabEl => {
                const tabId = tabEl.getAttribute('id');
                if (localStorage.getItem(`activeDocTab-${tabId}`) === 'true') {
                    new bootstrap.Tab(tabEl).show();
                }
            });
        });
    </script>
@endsection
