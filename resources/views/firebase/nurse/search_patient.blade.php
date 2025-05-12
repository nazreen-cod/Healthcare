
@extends('firebase.layoutnurse')

@section('title', 'Search Patient')
@section('content')
    <style>
        /* Compact Search Patient Styles */
        .search-container {
            padding: 10px 0;
        }

        .search-header {
            font-family: 'Orbitron', sans-serif;
            color: var(--success);
            margin-bottom: 1rem;
            position: relative;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            text-shadow: 0 0 15px rgba(0, 255, 157, 0.3);
            font-size: 1.3rem;
        }

        .search-header::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 60px;
            height: 2px;
            background: linear-gradient(to right, var(--success), transparent);
        }

        /* Compact Cyber Card */
        .cyber-card {
            background: rgba(16, 25, 36, 0.7);
            border-radius: 10px;
            border: 1px solid rgba(0, 255, 157, 0.2);
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
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

        /* Compact Form Controls */
        .form-control {
            background: rgba(16, 25, 36, 0.6);
            border: 1px solid rgba(0, 255, 157, 0.2);
            border-radius: 6px;
            color: white;
            padding: 8px 10px 8px 35px;
            font-size: 0.9rem;
            height: auto;
        }

        .form-control:focus {
            background: rgba(16, 25, 36, 0.8);
            border-color: var(--success);
            box-shadow: 0 0 0 3px rgba(0, 255, 157, 0.25);
        }

        .search-input-wrapper {
            position: relative;
        }

        .search-icon {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            color: var(--success);
            font-size: 0.9rem;
        }

        /* Compact Search Button */
        .btn-cyber-search {
            background: linear-gradient(45deg, var(--secondary), var(--success));
            border: none;
            color: white;
            border-radius: 6px;
            padding: 8px 15px;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            transition: all 0.3s;
        }

        .btn-cyber-search:hover {
            box-shadow: 0 0 15px rgba(0, 255, 157, 0.5);
            transform: translateY(-2px);
        }

        /* Compact Table */
        .table-cyber {
            width: 100%;
            color: #fff;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 1rem;
            font-size: 0.85rem;
        }

        .table-cyber thead th {
            background: rgba(0, 255, 157, 0.2);
            color: var(--success);
            padding: 8px 10px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-size: 0.75rem;
        }

        .table-cyber tbody tr {
            background: rgba(16, 25, 36, 0.6);
        }

        .table-cyber tbody tr:hover {
            background: rgba(0, 255, 157, 0.1);
        }

        .table-cyber td {
            padding: 6px 10px;
            border-bottom: 1px solid rgba(0, 255, 157, 0.1);
        }

        /* Compact Patient Card */
        .patient-card {
            margin-bottom: 15px;
            overflow: hidden;
        }

        .patient-header {
            background: linear-gradient(90deg, rgba(0, 255, 157, 0.2), rgba(0, 119, 255, 0.2));
            padding: 10px 15px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(0, 255, 157, 0.2);
        }

        .patient-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--secondary), var(--success));
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            color: white;
            font-size: 18px;
        }

        .patient-name {
            margin: 0;
            color: white;
            font-weight: 600;
            font-size: 1rem;
        }

        .patient-id {
            margin: 0;
            color: var(--success);
            font-size: 0.8rem;
        }

        .patient-body {
            padding: 10px 15px;
        }

        /* Tabs Navigation */
        .nav-tabs {
            border-bottom: 1px solid rgba(0, 255, 157, 0.2);
            display: flex;
            margin-bottom: 10px;
            overflow-x: auto;
            flex-wrap: nowrap;
            width: 100%;
        }

        .nav-tabs .nav-link {
            border: none;
            color: rgba(255, 255, 255, 0.6);
            padding: 6px 12px;
            font-size: 0.85rem;
            white-space: nowrap;
            margin-right: 5px;
            border-radius: 4px 4px 0 0;
        }

        .nav-tabs .nav-link.active {
            background-color: rgba(0, 255, 157, 0.1);
            color: var(--success);
            border-bottom: 2px solid var(--success);
        }

        .nav-tabs .nav-link:hover:not(.active) {
            color: white;
            background-color: rgba(0, 255, 157, 0.05);
        }

        .nav-tabs .nav-link i {
            margin-right: 5px;
        }

        /* Grid for patient details */
        .patient-info {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        @media (max-width: 768px) {
            .patient-info {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .patient-info {
                grid-template-columns: 1fr;
            }
        }

        .info-item {
            margin-bottom: 5px;
        }

        .info-label {
            color: var(--success);
            font-size: 0.8rem;
            font-weight: 500;
            margin-bottom: 2px;
            display: flex;
            align-items: center;
        }

        .info-label i {
            margin-right: 5px;
            width: 14px;
            text-align: center;
            font-size: 0.8rem;
        }

        .info-value {
            color: #e1e1e1;
            font-size: 0.85rem;
        }

        /* Action Buttons */
        .btn-cyber-action {
            background: linear-gradient(45deg, var(--secondary), var(--success));
            color: white;
            border: none;
            border-radius: 4px;
            padding: 5px 8px;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s;
            margin-right: 4px;
        }

        .btn-cyber-action i {
            margin-right: 4px;
            font-size: 0.7rem;
        }

        /* Collapsible Card */
        .collapsible-card {
            margin-bottom: 10px;
            border-radius: 8px;
            overflow: hidden;
        }

        .collapsible-header {
            padding: 8px 15px;
            background: rgba(0, 255, 157, 0.1);
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .collapsible-header:hover {
            background: rgba(0, 255, 157, 0.2);
        }

        .collapsible-body {
            padding: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .collapsible-body.open {
            max-height: 1000px;
        }

        .card-title {
            margin: 0 0 10px 0;
            font-size: 0.9rem;
            color: var(--success);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        /* No scrollbar for tabs */
        .nav-tabs::-webkit-scrollbar {
            height: 3px;
        }

        .nav-tabs::-webkit-scrollbar-thumb {
            background: rgba(0, 255, 157, 0.5);
            border-radius: 10px;
        }

        .tab-pane {
            padding: 10px 0;
        }

        /* Alert styles */
        .alert {
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }

        .alert-warning {
            background: rgba(255, 187, 0, 0.1);
            border-left: 3px solid #ffbb00;
            color: #ffbb00;
        }

        .alert-danger {
            background: rgba(255, 58, 58, 0.1);
            border-left: 3px solid #ff3a3a;
            color: #ff3a3a;
        }
    </style>

    <div class="container search-container">
        <h2 class="search-header"><i class="fas fa-search me-2"></i>Search Patient</h2>

        @if(session()->has("error"))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session()->get("error") }}
            </div>
        @endif

        <div class="cyber-card">
            <form method="GET" action="{{ route('firebase.nurse.searchPatient') }}" class="d-flex align-items-center gap-2">
                <div class="search-input-wrapper flex-grow-1">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="search" name="search" class="form-control"
                           placeholder="Enter Patient Name or ID No." required
                           value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn-cyber-search">
                    <i class="fas fa-search me-1"></i>Search
                </button>
            </form>
        </div>

        @if(isset($patients) && count($patients) > 0)
            <div class="table-responsive">
                <table class="table-cyber">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>ID No.</th>
                        <th>Gender/Age</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($patients as $index => $patient)
                        <tr>
                            <td>{{ $patient['name'] ?? 'N/A' }}</td>
                            <td>{{ $patient['id_no'] ?? 'N/A' }}</td>
                            <td>{{ $patient['gender'] ?? 'N/A' }}, {{ $patient['age'] ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('firebase.nurse.editPatient', ['id' => $patient['id']]) }}"
                                   class="btn-cyber-action">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="{{ route('firebase.nurse.sctest', ['id' => $patient['id']]) }}"
                                   class="btn-cyber-action">
                                    <i class="fas fa-file-medical"></i> Screening
                                </a>
                                <a href="{{ route('firebase.nurse.checkin', ['id' => $patient['id']]) }}"
                                   class="btn-cyber-action">
                                    <i class="fas fa-qrcode"></i> QR
                                </a>
                                <a href="#" class="btn-cyber-action"
                                   onclick="document.getElementById('patient-card-{{ $index }}').scrollIntoView({behavior: 'smooth'}); return false;">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            @foreach($patients as $index => $patient)
                <div id="patient-card-{{ $index }}" class="cyber-card patient-card">
                    <div class="patient-header">
                        <div class="patient-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="patient-name">{{ $patient['name'] ?? 'N/A' }}</h4>
                            <p class="patient-id">ID: {{ $patient['id_no'] ?? 'N/A' }} | {{ $patient['gender'] ?? 'N/A' }}, {{ $patient['age'] ?? 'N/A' }} yrs</p>
                        </div>
                        <div>
                            <a href="{{ route('firebase.nurse.editPatient', ['id' => $patient['id']]) }}" class="btn-cyber-action">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('firebase.nurse.sctest', ['id' => $patient['id']]) }}" class="btn-cyber-action">
                                <i class="fas fa-file-medical"></i> Screening
                            </a>
                            <a href="{{ route('firebase.nurse.checkin', ['id' => $patient['id']]) }}" class="btn-cyber-action">
                                <i class="fas fa-qrcode"></i> QR
                            </a>
                        </div>
                    </div>

                    <div class="patient-body">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#demographics-{{ $index }}" type="button">
                                    <i class="fas fa-id-card"></i> Demographics
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#medical-{{ $index }}" type="button">
                                    <i class="fas fa-heartbeat"></i> Medical
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#history-{{ $index }}" type="button">
                                    <i class="fas fa-history"></i> History
                                </button>
                            </li>
                            @if(isset($patient['bloodpm']) || isset($patient['tempreturec']))
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#vitals-{{ $index }}" type="button">
                                        <i class="fas fa-heartbeat"></i> Vitals
                                    </button>
                                </li>
                            @endif
                        </ul>

                        <div class="tab-content">
                            <!-- Demographics Tab -->
                            <div class="tab-pane fade show active" id="demographics-{{ $index }}">
                                <div class="patient-info">
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
                                </div>
                            </div>

                            <!-- Medical Tab -->
                            <div class="tab-pane fade" id="medical-{{ $index }}">
                                @if(!empty($patient['allergies']) || !empty($patient['medical_alerts']))
                                    <div style="background: rgba(255, 187, 0, 0.1); padding: 8px 10px; border-radius: 5px; margin-bottom: 10px; border-left: 3px solid #ffbb00;">
                                        @if(!empty($patient['allergies']))
                                            <div class="info-item">
                                                <div class="info-label"><i class="fas fa-allergies"></i> Allergies</div>
                                                <div class="info-value" style="color: #ffbb00;">{{ $patient['allergies'] }}</div>
                                            </div>
                                        @endif

                                        @if(!empty($patient['medical_alerts']))
                                            <div class="info-item">
                                                <div class="info-label"><i class="fas fa-exclamation-triangle"></i> Medical Alerts</div>
                                                <div class="info-value" style="color: #ffbb00;">{{ $patient['medical_alerts'] }}</div>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <div class="patient-info">
                                    <div class="info-item">
                                        <div class="info-label"><i class="fas fa-tint"></i> Blood Type</div>
                                        <div class="info-value">{{ $patient['blood_type'] ?? 'N/A' }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label"><i class="fas fa-weight"></i> Weight</div>
                                        <div class="info-value">{{ $patient['weight'] ?? 'N/A' }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label"><i class="fas fa-ruler-vertical"></i> Height</div>
                                        <div class="info-value">{{ $patient['height'] ?? 'N/A' }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label"><i class="fas fa-calculator"></i> BMI</div>
                                        <div class="info-value">{{ $patient['bmi'] ?? 'N/A' }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label"><i class="fas fa-pills"></i> Medications</div>
                                        <div class="info-value">{{ $patient['current_medications'] ?? 'N/A' }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label"><i class="fas fa-heartbeat"></i> Chronic Conditions</div>
                                        <div class="info-value">{{ $patient['chronic_conditions'] ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- History Tab -->
                            <div class="tab-pane fade" id="history-{{ $index }}">
                                <div class="info-item mb-3">
                                    <div class="info-label"><i class="fas fa-file-medical"></i> Medical History</div>
                                    <div class="info-value">{{ $patient['medical_history'] ?? 'No medical history recorded' }}</div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label"><i class="fas fa-users"></i> Family History</div>
                                    <div class="info-value">{{ $patient['family_history'] ?? 'No family history recorded' }}</div>
                                </div>
                            </div>

                            <!-- Vitals Tab -->
                            @if(isset($patient['bloodpm']) || isset($patient['tempreturec']))
                                <div class="tab-pane fade" id="vitals-{{ $index }}">
                                    <div class="patient-info">
                                        @if(isset($patient['bloodpm']))
                                            <div class="info-item">
                                                <div class="info-label"><i class="fas fa-heart"></i> Blood Pressure</div>
                                                <div class="info-value">{{ $patient['bloodpm'] }}</div>
                                            </div>
                                        @endif

                                        @if(isset($patient['tempreturec']))
                                            <div class="info-item">
                                                <div class="info-label"><i class="fas fa-thermometer-half"></i> Temperature</div>
                                                <div class="info-value">{{ $patient['tempreturec'] }}</div>
                                            </div>
                                        @endif

                                        @if(isset($patient['plusec']))
                                            <div class="info-item">
                                                <div class="info-label"><i class="fas fa-heartbeat"></i> Pulse</div>
                                                <div class="info-value">{{ $patient['plusec'] }}</div>
                                            </div>
                                        @endif

                                        @if(isset($patient['respiratingr']))
                                            <div class="info-item">
                                                <div class="info-label"><i class="fas fa-lungs"></i> Respiration</div>
                                                <div class="info-value">{{ $patient['respiratingr'] }}</div>
                                            </div>
                                        @endif

                                        @if(isset($patient['bmi']))
                                            <div class="info-item">
                                                <div class="info-label"><i class="fas fa-calculator"></i> BMI</div>
                                                <div class="info-value">{{ $patient['bmi'] }}</div>
                                            </div>
                                        @endif

                                        @if(isset($patient['bmi_status']))
                                            <div class="info-item">
                                                <div class="info-label"><i class="fas fa-info-circle"></i> BMI Status</div>
                                                <div class="info-value">{{ $patient['bmi_status'] }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @elseif(isset($patients))
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
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
                    const patientId = event.target.getAttribute('data-bs-target').split('-')[1];
                    localStorage.setItem(`activePatientTab-${patientId}`, event.target.getAttribute('data-bs-target'));
                });
            });

            // Restore last active tab for each patient card
            document.querySelectorAll('.patient-card').forEach(card => {
                const patientId = card.id.split('-')[2];
                const savedTab = localStorage.getItem(`activePatientTab-${patientId}`);
                if (savedTab) {
                    const tab = card.querySelector(`[data-bs-target="${savedTab}"]`);
                    if (tab) {
                        new bootstrap.Tab(tab).show();
                    }
                }
            });
        });
    </script>
@endsection
