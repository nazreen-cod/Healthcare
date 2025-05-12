@extends('firebase.layoutdoctor')

@section('content')
    <style>
        /* Compact Medical Report Styles */
        .compact-card {
            background: rgba(16, 25, 36, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 8px;
            border: 1px solid rgba(0, 195, 255, 0.2);
            padding: 15px;
            margin-bottom: 15px;
            position: relative;
            overflow: hidden;
        }

        .compact-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 3px;
            height: 100%;
            background: linear-gradient(to bottom, var(--primary), var(--secondary));
        }

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .report-title {
            font-family: 'Orbitron', sans-serif;
            color: var(--primary);
            margin: 0;
            font-size: 1.2rem;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
        }

        .report-title i {
            margin-right: 8px;
        }

        /* Compact Tabs */
        .report-tabs {
            background: rgba(16, 25, 36, 0.4);
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 15px;
            border: 1px solid rgba(0, 195, 255, 0.1);
        }

        .report-tabs .nav-link {
            color: rgba(255, 255, 255, 0.7);
            padding: 8px 15px;
            border: none;
            border-radius: 0;
            font-size: 0.85rem;
            position: relative;
        }

        .report-tabs .nav-link.active {
            background: transparent;
            color: white;
        }

        .report-tabs .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(to right, var(--primary), var(--secondary));
        }

        .report-tabs .nav-link i {
            margin-right: 5px;
            width: 16px;
            text-align: center;
        }

        /* Compact Patient Info */
        .patient-info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }

        .patient-info-item {
            font-size: 0.85rem;
        }

        .info-label {
            color: var(--primary);
            font-weight: 500;
            margin-bottom: 2px;
            display: flex;
            align-items: center;
        }

        .info-label i {
            margin-right: 5px;
            width: 16px;
            text-align: center;
            font-size: 0.8rem;
        }

        .info-value {
            color: #e1e1e1;
            padding-left: 21px;
        }

        /* Vitals Row */
        .vitals-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
            border-top: 1px solid rgba(0, 195, 255, 0.1);
            border-bottom: 1px solid rgba(0, 195, 255, 0.1);
            padding: 10px 0;
        }

        .vital-item {
            display: flex;
            align-items: center;
            background: rgba(0, 119, 255, 0.1);
            border-radius: 4px;
            padding: 4px 8px;
            font-size: 0.8rem;
        }

        .vital-item.warning {
            background: rgba(255, 187, 0, 0.1);
        }

        .vital-item.danger {
            background: rgba(255, 58, 58, 0.1);
        }

        .vital-icon {
            color: var(--primary);
            margin-right: 5px;
            font-size: 0.85rem;
            width: 16px;
            text-align: center;
        }

        .vital-item.warning .vital-icon {
            color: #ffbb00;
        }

        .vital-item.danger .vital-icon {
            color: #ff3a3a;
        }

        .vital-label {
            color: var(--primary);
            margin-right: 5px;
            font-weight: 500;
            font-size: 0.75rem;
        }

        .vital-value {
            color: white;
        }

        /* Form Controls */
        .form-control {
            background: rgba(16, 25, 36, 0.6);
            border: 1px solid rgba(0, 195, 255, 0.2);
            border-radius: 6px;
            color: white;
            padding: 8px 12px;
            font-size: 0.9rem;
        }

        .form-control:focus {
            background: rgba(16, 25, 36, 0.8);
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(0, 195, 255, 0.1);
            color: white;
        }

        textarea.form-control {
            min-height: 80px;
        }

        /* Table */
        .table-compact {
            width: 100%;
            color: white;
            font-size: 0.85rem;
            margin: 0;
        }

        .table-compact th {
            color: var(--primary);
            font-weight: 500;
            padding: 6px 8px;
            border-bottom: 1px solid rgba(0, 195, 255, 0.1);
            width: 150px;
            vertical-align: top;
        }

        .table-compact td {
            padding: 6px 8px;
            border-bottom: 1px solid rgba(0, 195, 255, 0.1);
        }

        /* Submit Button */
        .btn-cyber {
            background: linear-gradient(45deg, var(--secondary), var(--primary));
            border: none;
            color: white;
            border-radius: 6px;
            padding: 8px 20px;
            font-weight: 500;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-size: 0.85rem;
            transition: all 0.3s;
        }

        .btn-cyber:hover {
            box-shadow: 0 0 15px rgba(0, 195, 255, 0.3);
            transform: translateY(-2px);
            color: white;
        }

        /* Alert */
        .alert {
            border: none;
            border-radius: 6px;
            padding: 8px 15px;
            margin-bottom: 15px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }

        .alert-danger {
            background: rgba(255, 58, 58, 0.1);
            color: #ff3a3a;
            border-left: 3px solid #ff3a3a;
        }

        .alert-success {
            background: rgba(46, 213, 115, 0.1);
            color: #2ed573;
            border-left: 3px solid #2ed573;
        }

        .alert i {
            margin-right: 8px;
            font-size: 1rem;
        }

        /* Section Header */
        .section-header {
            color: var(--primary);
            font-size: 1rem;
            margin: 0 0 10px 0;
            display: flex;
            align-items: center;
        }

        .section-header i {
            margin-right: 8px;
        }

        /* For mobile */
        @media (max-width: 768px) {
            .patient-info-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .patient-info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="container py-3">
        <div class="report-header">
            <h2 class="report-title"><i class="fas fa-file-medical"></i>Medical Report</h2>
            @foreach($patients as $patient)
                <div>
                    <span class="badge bg-info">{{ $patient['id_no'] ?? 'N/A' }}</span>
                </div>
            @endforeach
        </div>

        @if(session()->has("error"))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                {{ session()->get("error") }}
            </div>
        @endif

        @if(session()->has("success"))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session()->get("success") }}
            </div>
        @endif

        <form method="POST" action="{{ route('firebase.doctor.storeMedicalReport') }}">
            @csrf
            @foreach($patients as $patient)
                <!-- Hidden Fields -->
                <input type="hidden" name="name" value="{{ $patient['name'] ?? '' }}">
                <input type="hidden" name="id_no" value="{{ $patient['id_no'] ?? '' }}">
                <input type="hidden" name="gender" value="{{ $patient['gender'] ?? '' }}">
                <input type="hidden" name="age" value="{{ $patient['age'] ?? '' }}">
                <input type="hidden" name="dob" value="{{ $patient['dob'] ?? '' }}">
                <input type="hidden" name="nationality" value="{{ $patient['nationality'] ?? '' }}">
                <input type="hidden" name="race" value="{{ $patient['race'] ?? '' }}">
                <input type="hidden" name="allergies" value="{{ $patient['allergies'] ?? '' }}">
                <input type="hidden" name="medical_alerts" value="{{ $patient['medical_alerts'] ?? '' }}">
                <input type="hidden" name="bloodpm" value="{{ $patient['bloodpm'] ?? '' }}">
                <input type="hidden" name="bmi" value="{{ $patient['bmi'] ?? '' }}">
                <input type="hidden" name="bmi_status" value="{{ $patient['bmi_status'] ?? '' }}">
                <input type="hidden" name="plusec" value="{{ $patient['plusec'] ?? '' }}">
                <input type="hidden" name="respiratingr" value="{{ $patient['respiratingr'] ?? '' }}">
                <input type="hidden" name="tempreturec" value="{{ $patient['tempreturec'] ?? '' }}">
                <input type="hidden" name="weight" value="{{ $patient['weight'] ?? '' }}">
                <input type="hidden" name="height" value="{{ $patient['height'] ?? '' }}">
                <input type="hidden" name="patient_id" value="{{ $patient['id'] ?? '' }}">
            @endforeach

            <!-- Tabs -->
            <ul class="nav nav-tabs report-tabs" id="medicalReportTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                        <i class="fas fa-user"></i> Overview
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="clinical-tab" data-bs-toggle="tab" data-bs-target="#clinical" type="button" role="tab">
                        <i class="fas fa-notes-medical"></i> Clinical
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="additional-tab" data-bs-toggle="tab" data-bs-target="#additional" type="button" role="tab">
                        <i class="fas fa-clipboard-list"></i> Additional
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="medicalReportTabContent">
                <!-- Overview Tab -->
                <div class="tab-pane fade show active" id="overview" role="tabpanel">
                    <div class="compact-card">
                        @foreach($patients as $patient)
                            <!-- Alerts Section -->
                            @if(!empty($patient['allergies']) || !empty($patient['medical_alerts']))
                                <div class="mb-3">
                                    @if(!empty($patient['allergies']))
                                        <div class="alert alert-danger mb-2">
                                            <i class="fas fa-allergies"></i>
                                            <strong>Allergies:</strong> {{ $patient['allergies'] }}
                                        </div>
                                    @endif

                                    @if(!empty($patient['medical_alerts']))
                                        <div class="alert alert-danger mb-2">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <strong>Medical Alerts:</strong> {{ $patient['medical_alerts'] }}
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Vital Signs -->
                            <div class="vitals-row">
                                @php
                                    $bpStatus = '';
                                    if (!empty($patient['bloodpm'])) {
                                        $bp = explode('/', $patient['bloodpm']);
                                        $systolic = isset($bp[0]) ? (int)trim($bp[0]) : 0;
                                        $diastolic = isset($bp[1]) ? (int)trim(explode(' ', $bp[1])[0]) : 0;

                                        if ($systolic >= 140 || $diastolic >= 90) {
                                            $bpStatus = 'danger';
                                        } elseif ($systolic >= 120 || $diastolic >= 80) {
                                            $bpStatus = 'warning';
                                        }
                                    }

                                    $tempStatus = '';
                                    if (!empty($patient['tempreturec'])) {
                                        $temp = (float)trim(explode(' ', $patient['tempreturec'])[0]);
                                        if ($temp >= 38.0) {
                                            $tempStatus = 'danger';
                                        } elseif ($temp >= 37.5 || $temp < 36.0) {
                                            $tempStatus = 'warning';
                                        }
                                    }

                                    $pulseStatus = '';
                                    if (!empty($patient['plusec'])) {
                                        $pulse = (int)trim(explode(' ', $patient['plusec'])[0]);
                                        if ($pulse > 100 || $pulse < 60) {
                                            $pulseStatus = 'warning';
                                        }
                                    }

                                    $respStatus = '';
                                    if (!empty($patient['respiratingr'])) {
                                        $resp = (int)trim(explode(' ', $patient['respiratingr'])[0]);
                                        if ($resp > 20 || $resp < 12) {
                                            $respStatus = 'warning';
                                        }
                                    }

                                    $bmiStatus = '';
                                    if (!empty($patient['bmi_status'])) {
                                        $bmiStatus = strtolower($patient['bmi_status']) === 'normal' ? '' :
                                                    (strtolower($patient['bmi_status']) === 'obese' ? 'danger' : 'warning');
                                    }
                                @endphp

                                <div class="vital-item {{ $bpStatus }}">
                                    <span class="vital-icon"><i class="fas fa-heart"></i></span>
                                    <span class="vital-label">BP:</span>
                                    <span class="vital-value">{{ $patient['bloodpm'] ?? 'N/A' }}</span>
                                </div>

                                <div class="vital-item {{ $tempStatus }}">
                                    <span class="vital-icon"><i class="fas fa-thermometer-half"></i></span>
                                    <span class="vital-label">Temp:</span>
                                    <span class="vital-value">{{ $patient['tempreturec'] ?? 'N/A' }}</span>
                                </div>

                                <div class="vital-item {{ $pulseStatus }}">
                                    <span class="vital-icon"><i class="fas fa-heartbeat"></i></span>
                                    <span class="vital-label">Pulse:</span>
                                    <span class="vital-value">{{ $patient['plusec'] ?? 'N/A' }}</span>
                                </div>

                                <div class="vital-item {{ $respStatus }}">
                                    <span class="vital-icon"><i class="fas fa-lungs"></i></span>
                                    <span class="vital-label">Resp:</span>
                                    <span class="vital-value">{{ $patient['respiratingr'] ?? 'N/A' }}</span>
                                </div>

                                <div class="vital-item {{ $bmiStatus }}">
                                    <span class="vital-icon"><i class="fas fa-calculator"></i></span>
                                    <span class="vital-label">BMI:</span>
                                    <span class="vital-value">{{ $patient['bmi'] ?? 'N/A' }} ({{ $patient['bmi_status'] ?? '' }})</span>
                                </div>

                                <div class="vital-item">
                                    <span class="vital-icon"><i class="fas fa-weight"></i></span>
                                    <span class="vital-label">Weight:</span>
                                    <span class="vital-value">{{ $patient['weight'] ?? 'N/A' }}</span>
                                </div>

                                <div class="vital-item">
                                    <span class="vital-icon"><i class="fas fa-ruler-vertical"></i></span>
                                    <span class="vital-label">Height:</span>
                                    <span class="vital-value">{{ $patient['height'] ?? 'N/A' }}</span>
                                </div>
                            </div>

                            <!-- Patient Demographics -->
                            <h4 class="section-header"><i class="fas fa-id-card"></i> Patient Information</h4>
                            <div class="patient-info-grid">
                                <div class="patient-info-item">
                                    <div class="info-label"><i class="fas fa-user"></i> Name</div>
                                    <div class="info-value">{{ $patient['name'] ?? 'N/A' }}</div>
                                </div>

                                <div class="patient-info-item">
                                    <div class="info-label"><i class="fas fa-venus-mars"></i> Gender</div>
                                    <div class="info-value">{{ $patient['gender'] ?? 'N/A' }}</div>
                                </div>

                                <div class="patient-info-item">
                                    <div class="info-label"><i class="fas fa-birthday-cake"></i> Age</div>
                                    <div class="info-value">{{ $patient['age'] ?? 'N/A' }}</div>
                                </div>

                                <div class="patient-info-item">
                                    <div class="info-label"><i class="fas fa-calendar-alt"></i> DOB</div>
                                    <div class="info-value">{{ $patient['dob'] ?? 'N/A' }}</div>
                                </div>

                                <div class="patient-info-item">
                                    <div class="info-label"><i class="fas fa-map-marker-alt"></i> Location</div>
                                    <div class="info-value">{{ $patient['location'] ?? 'N/A' }}</div>
                                </div>

                                <div class="patient-info-item">
                                    <div class="info-label"><i class="fas fa-flag"></i> Nationality</div>
                                    <div class="info-value">{{ $patient['nationality'] ?? 'N/A' }}</div>
                                </div>

                                <div class="patient-info-item">
                                    <div class="info-label"><i class="fas fa-users"></i> Race</div>
                                    <div class="info-value">{{ $patient['race'] ?? 'N/A' }}</div>
                                </div>

                                <div class="patient-info-item">
                                    <div class="info-label"><i class="fas fa-phone"></i> Contact</div>
                                    <div class="info-value">{{ $patient['contact'] ?? 'N/A' }}</div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Clinical Tab -->
                <div class="tab-pane fade" id="clinical" role="tabpanel">
                    <div class="compact-card">
                        <h4 class="section-header"><i class="fas fa-file-medical"></i> Clinical Summary</h4>
                        <div class="mb-3">
                            <textarea class="form-control" name="clinical_summary" rows="4" placeholder="Enter clinical summary, diagnosis, and findings...">{{ old('clinical_summary') }}</textarea>
                        </div>

                        <h4 class="section-header"><i class="fas fa-history"></i> Medical & Family History</h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-primary">Medical / Surgical History</label>
                                <textarea class="form-control" name="medical_history" placeholder="Enter medical and surgical history...">{{ old('medical_history') }}</textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label text-primary">Family History</label>
                                <textarea class="form-control" name="family_history" placeholder="Enter family medical history...">{{ old('family_history') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Tab -->
                <div class="tab-pane fade" id="additional" role="tabpanel">
                    <div class="compact-card">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="section-header"><i class="fas fa-procedures"></i> Admission Details</h4>
                                <table class="table-compact">
                                    <tr>
                                        <th>Admission Date</th>
                                        <td><input type="datetime-local" class="form-control form-control-sm" name="admission_date"></td>
                                    </tr>
                                    <tr>
                                        <th>Principal Doctor</th>
                                        <td><input type="text" class="form-control form-control-sm" name="principal_doctor"></td>
                                    </tr>
                                    <tr>
                                        <th>Reason</th>
                                        <td><textarea class="form-control form-control-sm" name="reason_for_admission" rows="2"></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>Principal Diagnosis</th>
                                        <td><textarea class="form-control form-control-sm" name="principal_diagnosis" rows="2"></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>Secondary Diagnosis</th>
                                        <td><textarea class="form-control form-control-sm" name="secondary_diagnosis" rows="2"></textarea></td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <h4 class="section-header"><i class="fas fa-clipboard-check"></i> Discharge & Procedures</h4>
                                <table class="table-compact">
                                    <tr>
                                        <th>Discharge Date</th>
                                        <td><input type="date" class="form-control form-control-sm" name="discharge_date"></td>
                                    </tr>
                                    <tr>
                                        <th>Condition</th>
                                        <td><textarea class="form-control form-control-sm" name="condition_at_discharge" rows="2"></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>Procedures</th>
                                        <td><textarea class="form-control form-control-sm" name="operation_procedures" rows="2"></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>Other Diagnosis</th>
                                        <td><textarea class="form-control form-control-sm" name="other_diagnosis" rows="2"></textarea></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="d-flex justify-content-center mt-3 mb-3">
                <button type="submit" class="btn-cyber">
                    <i class="fas fa-save me-2"></i> Save Medical Report
                </button>
            </div>
        </form>
    </div>

    <!-- JavaScript for tabs persistence -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Store active tab
            const tabEls = document.querySelectorAll('button[data-bs-toggle="tab"]');
            tabEls.forEach(tabEl => {
                tabEl.addEventListener('shown.bs.tab', event => {
                    localStorage.setItem('activeReportTab', event.target.getAttribute('id'));
                });
            });

            // Restore active tab
            const activeTab = localStorage.getItem('activeReportTab');
            if (activeTab) {
                const tab = document.querySelector('#' + activeTab);
                if (tab) {
                    new bootstrap.Tab(tab).show();
                }
            }
        });
    </script>
@endsection
