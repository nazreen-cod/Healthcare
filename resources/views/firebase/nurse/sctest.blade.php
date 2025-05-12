@extends('firebase.layoutnurse')

@section('title', 'Screening Test')

@section('content')
    <style>
        /* Compact Screening Test Styles */
        .sctest-container {
            padding: 10px 0;
        }

        .sctest-header {
            font-family: 'Orbitron', sans-serif;
            color: var(--success);
            margin-bottom: 0.8rem;
            position: relative;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            text-shadow: 0 0 15px rgba(0, 255, 157, 0.3);
            font-size: 1.3rem;
        }

        .sctest-header::after {
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
            border-radius: 8px;
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
            width: 3px;
            height: 100%;
            background: linear-gradient(to bottom, var(--success), var(--secondary));
        }

        /* Compact Form Controls */
        .form-control, .input-group-text {
            background: rgba(16, 25, 36, 0.6);
            border: 1px solid rgba(0, 255, 157, 0.2);
            border-radius: 6px;
            color: white;
            padding: 6px 10px;
            font-size: 0.9rem;
            height: auto;
        }

        .form-control:focus {
            background: rgba(16, 25, 36, 0.8);
            border-color: var(--success);
            box-shadow: 0 0 0 2px rgba(0, 255, 157, 0.25);
        }

        .form-label {
            color: var(--success);
            font-weight: 500;
            margin-bottom: 3px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
        }

        .form-label i {
            margin-right: 5px;
            width: 16px;
            text-align: center;
            font-size: 0.8rem;
        }

        .input-group-text {
            background: rgba(0, 255, 157, 0.1);
            color: var(--success);
            border-color: rgba(0, 255, 157, 0.3);
            padding: 0 10px;
        }

        /* Compact Nav Tabs */
        .nav-tabs {
            border-bottom: 1px solid rgba(0, 255, 157, 0.2);
            margin-bottom: 0;
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
        }

        .nav-tabs .nav-link {
            color: rgba(255, 255, 255, 0.7);
            border: none;
            border-radius: 6px 6px 0 0;
            padding: 8px 12px;
            font-size: 0.9rem;
            margin-right: 3px;
            white-space: nowrap;
        }

        .nav-tabs .nav-link.active {
            color: white;
            background: transparent;
            border-color: transparent;
        }

        .nav-tabs .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(to right, var(--success), var(--primary));
        }

        .tab-content {
            background: rgba(16, 25, 36, 0.7);
            padding: 15px;
            border-radius: 0 0 8px 8px;
            border: 1px solid rgba(0, 255, 157, 0.2);
            border-top: none;
        }

        /* Compact Patient Info Grid */
        .patient-info {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
        }

        @media (max-width: 1199.98px) {
            .patient-info {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 991.98px) {
            .patient-info {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 575.98px) {
            .patient-info {
                grid-template-columns: 1fr;
            }
        }

        .info-group {
            margin-bottom: 8px;
        }

        .info-label {
            color: var(--success);
            font-weight: 500;
            font-size: 0.8rem;
            margin-bottom: 1px;
            display: flex;
            align-items: center;
        }

        .info-label i {
            margin-right: 4px;
            width: 14px;
            text-align: center;
            font-size: 0.75rem;
        }

        .info-value {
            color: #e1e1e1;
            padding-left: 18px;
            font-size: 0.85rem;
        }

        /* Compact Section Header */
        .section-header {
            font-family: 'Orbitron', sans-serif;
            color: var(--success);
            font-size: 1rem;
            margin: 10px 0 8px;
            display: flex;
            align-items: center;
            padding-bottom: 5px;
            border-bottom: 1px solid rgba(0, 255, 157, 0.1);
        }

        .section-header i {
            margin-right: 6px;
            font-size: 0.9rem;
        }

        /* Compact Submit Button */
        .btn-cyber-submit {
            background: linear-gradient(45deg, var(--secondary), var(--success));
            border: none;
            color: white;
            border-radius: 6px;
            padding: 8px 15px;
            font-weight: 500;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-family: 'Orbitron', sans-serif;
            font-size: 0.9rem;
        }

        .btn-cyber-submit:hover {
            box-shadow: 0 0 15px rgba(0, 255, 157, 0.5);
            transform: translateY(-2px);
        }

        /* Compact Alert */
        .alert {
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }

        .alert-success {
            background: rgba(0, 255, 157, 0.1);
            border-left: 3px solid var(--success);
            color: var(--success);
        }

        .alert-danger {
            background: rgba(255, 58, 58, 0.1);
            border-left: 3px solid #ff3a3a;
            color: #ff3a3a;
        }

        /* Form Row for Side-by-Side Fields */
        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 10px;
        }

        @media (max-width: 767.98px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .form-group {
            margin-bottom: 10px;
        }

        /* Vitals Dashboard */
        .vitals-dashboard {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-bottom: 10px;
        }

        @media (max-width: 991.98px) {
            .vitals-dashboard {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .vital-card {
            background: rgba(0, 255, 157, 0.05);
            border-radius: 6px;
            padding: 8px;
            border-left: 2px solid var(--success);
        }

        .vital-label {
            font-size: 0.75rem;
            color: var(--success);
            margin-bottom: 3px;
            display: flex;
            align-items: center;
        }

        .vital-label i {
            margin-right: 4px;
            width: 14px;
            text-align: center;
        }

        .vital-inputs {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .vital-input {
            flex: 1;
        }

        .vital-unit {
            width: auto;
            white-space: nowrap;
        }

        /* BMI Status Colors */
        .bmi-normal { color: var(--success); }
        .bmi-warning { color: #ffbb00; }
        .bmi-danger { color: #ff3a3a; }

        /* No scrollbar for tabs */
        .nav-tabs::-webkit-scrollbar {
            height: 3px;
        }

        .nav-tabs::-webkit-scrollbar-thumb {
            background: rgba(0, 255, 157, 0.5);
            border-radius: 10px;
        }

        /* Patient Overview */
        .patient-overview {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            background: rgba(0, 255, 157, 0.05);
            padding: 10px;
            border-radius: 6px;
        }

        .patient-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--secondary), var(--success));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .patient-overview-info {
            flex: 1;
        }

        .patient-name {
            font-weight: 600;
            font-size: 1rem;
            margin: 0;
            color: white;
        }

        .patient-details {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.7);
            margin: 0;
        }

        /* Allergies warning */
        .allergies-alert {
            background: rgba(255, 187, 0, 0.1);
            border-left: 3px solid #ffbb00;
            color: #ffbb00;
            padding: 8px 10px;
            font-size: 0.85rem;
            border-radius: 4px;
            margin: 10px 0;
            display: flex;
            align-items: center;
        }

        .allergies-alert i {
            margin-right: 8px;
            font-size: 1rem;
        }
    </style>

    <div class="container sctest-container">
        <h2 class="sctest-header"><i class="fas fa-file-medical me-2"></i>Screening Test</h2>

        @if(session()->has("error") || session()->has("success"))
            <div class="cyber-card">
                @if(session()->has("error"))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session()->get("error") }}
                    </div>
                @endif

                @if(session()->has("success"))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>{{ session()->get("success") }}
                    </div>
                @endif
            </div>
        @endif

        <form method="POST" action="{{ route('firebase.nurse.store_sctest', ['id' => $id]) }}">
            @csrf
            <!-- Hidden Fields to Store Patient Data -->
            <input type="hidden" name="name" value="{{ $patient['name'] ?? '' }}">
            <input type="hidden" name="id_no" value="{{ $patient['id_no'] ?? '' }}">
            <input type="hidden" name="gender" value="{{ $patient['gender'] ?? '' }}">
            <input type="hidden" name="age" value="{{ $patient['age'] ?? '' }}">
            <input type="hidden" name="dob" value="{{ $patient['dob'] ?? '' }}">
            <input type="hidden" name="nationality" value="{{ $patient['nationality'] ?? '' }}">
            <input type="hidden" name="race" value="{{ $patient['race'] ?? '' }}">
            <input type="hidden" name="allergies" value="{{ $patient['allergies'] ?? '' }}">
            <input type="hidden" name="medical_alerts" value="{{ $patient['medical_alerts'] ?? '' }}">

            <!-- Patient Overview Card -->
            <div class="cyber-card">
                <div class="patient-overview">
                    <div class="patient-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="patient-overview-info">
                        <h4 class="patient-name">{{ $patient['name'] ?? 'N/A' }}</h4>
                        <p class="patient-details">
                            {{ $patient['gender'] ?? 'N/A' }}, {{ $patient['age'] ?? 'N/A' }}yrs | ID: {{ $patient['id_no'] ?? 'N/A' }}
                        </p>
                    </div>
                    <button type="button" class="btn-cyber-submit" data-bs-toggle="collapse" data-bs-target="#patientDetails">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>

                <!-- Collapsible Patient Details -->
                <div class="collapse" id="patientDetails">
                    @if(!empty($patient['allergies']) || !empty($patient['medical_alerts']))
                        <div class="allergies-alert">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>
                                @if(!empty($patient['allergies']))
                                    <strong>Allergies:</strong> {{ $patient['allergies'] }}<br>
                                @endif
                                @if(!empty($patient['medical_alerts']))
                                    <strong>Medical Alerts:</strong> {{ $patient['medical_alerts'] }}
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="section-header">
                        <i class="fas fa-id-card"></i> Patient Information
                    </div>

                    <div class="patient-info">
                        <div class="info-group">
                            <div class="info-label"><i class="fas fa-venus-mars"></i> Gender</div>
                            <div class="info-value">{{ $patient['gender'] ?? 'N/A' }}</div>
                        </div>

                        <div class="info-group">
                            <div class="info-label"><i class="fas fa-birthday-cake"></i> Age</div>
                            <div class="info-value">{{ $patient['age'] ?? 'N/A' }}</div>
                        </div>

                        <div class="info-group">
                            <div class="info-label"><i class="fas fa-calendar-alt"></i> DoB</div>
                            <div class="info-value">{{ $patient['dob'] ?? 'N/A' }}</div>
                        </div>

                        <div class="info-group">
                            <div class="info-label"><i class="fas fa-map-marker-alt"></i> Location</div>
                            <div class="info-value">{{ $patient['location'] ?? 'N/A' }}</div>
                        </div>

                        <div class="info-group">
                            <div class="info-label"><i class="fas fa-flag"></i> Nationality</div>
                            <div class="info-value">{{ $patient['nationality'] ?? 'N/A' }}</div>
                        </div>

                        <div class="info-group">
                            <div class="info-label"><i class="fas fa-users"></i> Race</div>
                            <div class="info-value">{{ $patient['race'] ?? 'N/A' }}</div>
                        </div>

                        <div class="info-group">
                            <div class="info-label"><i class="fas fa-phone"></i> Contact</div>
                            <div class="info-value">{{ $patient['contact'] ?? 'N/A' }}</div>
                        </div>

                        <div class="info-group">
                            <div class="info-label"><i class="fas fa-tint"></i> Blood Type</div>
                            <div class="info-value">{{ $patient['blood_type'] ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Screening Test Card -->
            <div class="cyber-card">
                <div class="section-header">
                    <i class="fas fa-heartbeat"></i> Vital Signs
                </div>

                <div class="vitals-dashboard">
                    <!-- Blood Pressure -->
                    <div class="vital-card">
                        <div class="vital-label"><i class="fas fa-heart"></i> Blood Pressure</div>
                        <div class="vital-inputs">
                            <input type="number" id="bloodpm_systolic" name="bloodpm_systolic" class="form-control vital-input" min="70" max="220" placeholder="Systolic" required>
                            <span class="vital-unit">/</span>
                            <input type="number" id="bloodpm_diastolic" name="bloodpm_diastolic" class="form-control vital-input" min="40" max="120" placeholder="Diastolic" required>
                            <span class="vital-unit">mmHg</span>
                        </div>
                    </div>

                    <!-- Temperature -->
                    <div class="vital-card">
                        <div class="vital-label"><i class="fas fa-thermometer-half"></i> Temperature</div>
                        <div class="vital-inputs">
                            <input type="number" id="tempreturec" name="tempreturec" class="form-control vital-input" min="35" max="42" step="0.1" placeholder="Temp" required>
                            <span class="vital-unit">°C</span>
                        </div>
                    </div>

                    <!-- Pulse -->
                    <div class="vital-card">
                        <div class="vital-label"><i class="fas fa-heartbeat"></i> Pulse Rate</div>
                        <div class="vital-inputs">
                            <input type="number" id="plusec" name="plusec" class="form-control vital-input" min="40" max="200" placeholder="Pulse" required>
                            <span class="vital-unit">BPM</span>
                        </div>
                    </div>

                    <!-- Respirations -->
                    <div class="vital-card">
                        <div class="vital-label"><i class="fas fa-lungs"></i> Respiratory Rate</div>
                        <div class="vital-inputs">
                            <input type="number" id="respiratingr" name="respiratingr" class="form-control vital-input" min="8" max="40" placeholder="Resp" required>
                            <span class="vital-unit">BPM</span>
                        </div>
                    </div>
                </div>

                <div class="section-header">
                    <i class="fas fa-weight"></i> Body Measurements
                </div>

                <div class="form-row">
                    <!-- Weight & Height -->
                    <div class="form-group">
                        <label for="weight" class="form-label"><i class="fas fa-weight"></i> Weight</label>
                        <div class="input-group">
                            <input type="number" id="weight" name="weight" class="form-control" min="20" max="300" step="0.1" placeholder="Weight" required>
                            <span class="input-group-text">kg</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="height" class="form-label"><i class="fas fa-ruler-vertical"></i> Height</label>
                        <div class="input-group">
                            <input type="number" id="height" name="height" class="form-control" min="50" max="250" step="0.1" placeholder="Height" required>
                            <span class="input-group-text">cm</span>
                        </div>
                    </div>
                </div>

                <!-- BMI Calculation -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="bmi" class="form-label"><i class="fas fa-calculator"></i> BMI</label>
                        <input type="text" id="bmi" name="bmi" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="bmi_status" class="form-label"><i class="fas fa-info-circle"></i> BMI Status</label>
                        <input type="text" id="bmi_status" name="bmi_status" class="form-control" readonly>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-center mt-3">
                    <button type="submit" class="btn-cyber-submit">
                        <i class="fas fa-save me-2"></i>Submit Screening Results
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- JavaScript for BMI Calculation and Status -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const weightInput = document.getElementById("weight");
            const heightInput = document.getElementById("height");
            const bmiInput = document.getElementById("bmi");
            const bmiStatusInput = document.getElementById("bmi_status");

            function calculateBMI() {
                const weight = parseFloat(weightInput.value);
                const height = parseFloat(heightInput.value) / 100; // Convert cm to meters

                if (weight > 0 && height > 0) {
                    const bmi = (weight / (height * height)).toFixed(1);
                    bmiInput.value = bmi + " kg/m²";

                    // Determine BMI status
                    let status = "";
                    let statusClass = "";

                    if (bmi < 18.5) {
                        status = "Underweight";
                        statusClass = "bmi-warning";
                    } else if (bmi >= 18.5 && bmi < 24.9) {
                        status = "Normal";
                        statusClass = "bmi-normal";
                    } else if (bmi >= 25 && bmi < 29.9) {
                        status = "Overweight";
                        statusClass = "bmi-warning";
                    } else {
                        status = "Obese";
                        statusClass = "bmi-danger";
                    }

                    bmiStatusInput.value = status;
                    bmiStatusInput.className = `form-control ${statusClass}`;
                } else {
                    bmiInput.value = "";
                    bmiStatusInput.value = "";
                    bmiStatusInput.className = "form-control";
                }
            }

            weightInput.addEventListener("input", calculateBMI);
            heightInput.addEventListener("input", calculateBMI);

            // Set initial state of patient details collapse
            const patientDetails = document.getElementById('patientDetails');
            const bsCollapse = new bootstrap.Collapse(patientDetails, {
                toggle: false
            });
        });
    </script>
@endsection
