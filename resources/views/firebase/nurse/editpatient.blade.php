
@extends('firebase.layoutnurse')

@section('title', 'Edit Patient')

@section('content')
    <style>
        /* Compact Edit Patient Styles - Shared with register page */
        .edit-container {
            padding: 15px 0;
        }

        .edit-header {
            font-family: 'Orbitron', sans-serif;
            color: var(--success);
            margin-bottom: 1rem;
            position: relative;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
            text-shadow: 0 0 15px rgba(0, 255, 157, 0.3);
            font-size: 1.5rem;
        }

        .edit-header::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(to right, var(--success), transparent);
        }

        /* Compact Cyber Card */
        .cyber-card {
            background: rgba(16, 25, 36, 0.7);
            border-radius: 10px;
            border: 1px solid rgba(0, 255, 157, 0.2);
            padding: 20px;
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

        /* Patient Summary Banner */
        .patient-summary {
            background: rgba(0, 255, 157, 0.1);
            border: 1px solid rgba(0, 255, 157, 0.2);
            border-left: 4px solid var(--success);
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .patient-info-main {
            display: flex;
            align-items: center;
        }

        .patient-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(0, 255, 157, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.5rem;
            color: var(--success);
        }

        .patient-name-id h4 {
            color: var(--success);
            font-size: 1.1rem;
            margin: 0;
        }

        .patient-name-id p {
            color: #e1e1e1;
            font-size: 0.8rem;
            margin: 0;
        }

        .last-update {
            font-size: 0.8rem;
            color: #a0a0a0;
        }

        /* Form Section Header */
        .form-section-header {
            font-family: 'Orbitron', sans-serif;
            color: var(--success);
            margin: 10px 0;
            font-size: 1rem;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            padding-bottom: 5px;
            border-bottom: 1px solid rgba(0, 255, 157, 0.1);
        }

        .form-section-header i {
            margin-right: 8px;
            font-size: 0.9rem;
        }

        /* Compact Form Elements */
        .form-group {
            margin-bottom: 12px;
            position: relative;
        }

        .form-label {
            color: var(--success);
            font-weight: 500;
            margin-bottom: 3px;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
        }

        .form-label i {
            margin-right: 5px;
            width: 16px;
            text-align: center;
            font-size: 0.85rem;
        }

        .form-control, .form-select {
            background: rgba(16, 25, 36, 0.6);
            border: 1px solid rgba(0, 255, 157, 0.2);
            border-radius: 6px;
            color: white;
            padding: 8px 12px;
            font-size: 0.9rem;
            height: auto;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(16, 25, 36, 0.8);
            border-color: var(--success);
            box-shadow: 0 0 0 2px rgba(0, 255, 157, 0.25);
        }

        /* Updated field highlight */
        .field-updated {
            border-color: var(--success) !important;
            background-color: rgba(0, 255, 157, 0.1) !important;
        }

        /* Field change indicator */
        .field-changed-indicator {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--success);
            display: none;
            font-size: 0.8rem;
            z-index: 2;
        }

        .field-updated + .field-changed-indicator {
            display: block;
        }

        /* Original value tooltip */
        .original-value-tooltip {
            position: absolute;
            bottom: -22px;
            left: 0;
            background: rgba(0, 0, 0, 0.8);
            color: #a0a0a0;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            opacity: 0;
            transition: opacity 0.2s;
            pointer-events: none;
            white-space: nowrap;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            z-index: 5;
            border-left: 2px solid var(--success);
        }

        .form-group:hover .original-value-tooltip {
            opacity: 1;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-cyber-submit {
            background: linear-gradient(45deg, var(--secondary), var(--success));
            border: none;
            color: white;
            border-radius: 6px;
            padding: 8px 20px;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-size: 0.9rem;
            font-family: 'Orbitron', sans-serif;
        }

        .btn-cyber-submit:hover {
            box-shadow: 0 0 15px rgba(0, 255, 157, 0.5);
            transform: translateY(-2px);
        }

        .btn-cyber-cancel {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 6px;
            padding: 8px 20px;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .btn-cyber-cancel:hover {
            border-color: rgba(255, 255, 255, 0.4);
            background: rgba(255, 255, 255, 0.1);
        }

        /* Compact Grid Layout */
        .form-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        /* Edit History Section */
        .edit-history {
            margin-top: 20px;
            font-size: 0.85rem;
        }

        .history-item {
            padding: 8px;
            border-left: 2px solid rgba(0, 255, 157, 0.3);
            margin-bottom: 8px;
            background: rgba(0, 255, 157, 0.05);
        }

        .history-time {
            color: var(--success);
            font-size: 0.75rem;
        }

        .history-user {
            font-weight: 500;
            color: #e1e1e1;
        }

        .history-changes {
            color: #a0a0a0;
        }

        /* Compact Alerts */
        .alert {
            border: none;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }

        .alert-success {
            background: linear-gradient(45deg, rgba(0, 255, 157, 0.1), rgba(0, 255, 157, 0.2));
            color: var(--success);
            border-left: 4px solid var(--success);
        }

        .alert-danger {
            background: linear-gradient(45deg, rgba(255, 75, 75, 0.1), rgba(255, 75, 75, 0.2));
            color: #ff4b4b;
            border-left: 4px solid #ff4b4b;
        }

        /* Changes summary panel */
        .changes-summary {
            background: rgba(0, 255, 157, 0.05);
            border: 1px solid rgba(0, 255, 157, 0.2);
            border-radius: 6px;
            padding: 10px;
            margin-top: 15px;
        }

        .change-count {
            display: inline-block;
            background: rgba(0, 255, 157, 0.2);
            color: var(--success);
            font-weight: bold;
            padding: 0 8px;
            border-radius: 10px;
            font-size: 0.85rem;
            margin-left: 5px;
        }

        .change-item {
            display: flex;
            align-items: center;
            padding: 5px 8px;
            border-bottom: 1px solid rgba(0, 255, 157, 0.1);
            font-size: 0.85rem;
        }

        .change-item:last-child {
            border-bottom: none;
        }

        .change-field {
            flex: 1;
            color: var(--success);
        }

        .change-old {
            color: #a0a0a0;
            text-decoration: line-through;
            margin-right: 8px;
        }

        .change-arrow {
            margin: 0 5px;
            color: var(--secondary);
        }

        .change-new {
            color: white;
            font-weight: 500;
        }

        /* Responsive adjustments */
        @media (max-width: 1199.98px) {
            .form-row {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 991.98px) {
            .form-row {
                grid-template-columns: repeat(2, 1fr);
            }

            .patient-summary {
                flex-direction: column;
                align-items: flex-start;
            }

            .last-update {
                margin-top: 10px;
            }
        }

        @media (max-width: 575.98px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .patient-info-main {
                flex-direction: column;
                text-align: center;
            }

            .patient-avatar {
                margin: 0 auto 10px;
            }
        }

        /* Tabs for edit sections */
        .nav-tabs {
            border-bottom: 1px solid rgba(0, 255, 157, 0.2);
            margin-bottom: 15px;
        }

        .nav-tabs .nav-link {
            color: rgba(255, 255, 255, 0.7);
            border: none;
            padding: 8px 15px;
            border-radius: 0;
            margin-right: 5px;
            font-size: 0.9rem;
            position: relative;
        }

        .nav-tabs .nav-link.active {
            background: transparent;
            color: var(--success);
            font-weight: 500;
        }

        .nav-tabs .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--success);
        }

        .nav-tabs .nav-link:hover {
            border-color: transparent;
            background: rgba(0, 255, 157, 0.05);
        }

        .nav-tabs .nav-link i {
            margin-right: 5px;
        }

        .tab-content {
            padding-top: 5px;
        }
    </style>

    <div class="container edit-container">
        <h2 class="edit-header mb-3"><i class="fas fa-user-edit me-2"></i>Edit Patient</h2>

        @if(session()->has("success"))
            <div class="alert alert-success animate-in">
                <i class="fas fa-check-circle me-2"></i>{{ session()->get("success") }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger animate-in">
                <i class="fas fa-exclamation-circle me-2"></i>
                @foreach ($errors->all() as $error)
                    {{ $error }}{{ !$loop->last ? ', ' : '' }}
                @endforeach
            </div>
        @endif

        <!-- Patient Summary Banner -->
        <div class="patient-summary">
            <div class="patient-info-main">
                <div class="patient-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="patient-name-id">
                    <h4>{{ $patientObj->name ?? 'Patient Name' }}</h4>
                    <p>ID: {{ $patientObj->id_no ?? 'ID Number' }} | DOB: {{ isset($patientObj->dob) ? date('d M Y', strtotime($patientObj->dob)) : 'Not available' }}</p>
                </div>
            </div>
            <div class="last-update">
                <i class="fas fa-history me-1"></i> Last updated: {{ isset($patientObj->updated_at) ? date('d M Y H:i', strtotime($patientObj->updated_at)) : 'Not available' }}
            </div>
        </div>

        <form method="POST" action="{{ route('firebase.nurse.update_patient', $patientObj->id ?? '') }}" class="animate-in">
            @csrf
            @method('PUT')

            <div class="cyber-card">
                <!-- Navigation tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#personal-info" type="button" role="tab">
                            <i class="fas fa-id-card"></i>Personal
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#demographics" type="button" role="tab">
                            <i class="fas fa-map-marker-alt"></i>Demographics
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#medical-info" type="button" role="tab">
                            <i class="fas fa-heartbeat"></i>Medical
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#changes-tab" type="button" role="tab">
                            <i class="fas fa-exchange-alt"></i>Changes <span id="change-counter" class="change-count" style="display: none;">0</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Personal Information Tab -->
                    <div class="tab-pane fade show active" id="personal-info" role="tabpanel">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user"></i> Full Name
                                </label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="Enter full name" required
                                       value="{{ old('name', $patientObj->name ?? '') }}" data-original="{{ $patientObj->name ?? '' }}">
                                <div class="field-changed-indicator"><i class="fas fa-check-circle"></i></div>
                                <div class="original-value-tooltip"><i class="fas fa-history"></i> Original: {{ $patientObj->name ?? 'Not set' }}</div>
                            </div>

                            <div class="form-group">
                                <label for="id_no" class="form-label">
                                    <i class="fas fa-id-badge"></i> ID Number
                                </label>
                                <input type="text" id="id_no" name="id_no" class="form-control" placeholder="Enter ID number"
                                       value="{{ old('id_no', $patientObj->id_no ?? '') }}" data-original="{{ $patientObj->id_no ?? '' }}">
                                <div class="field-changed-indicator"><i class="fas fa-check-circle"></i></div>
                                <div class="original-value-tooltip"><i class="fas fa-history"></i> Original: {{ $patientObj->id_no ?? 'Not set' }}</div>
                            </div>

                            <div class="form-group">
                                <label for="gender" class="form-label">
                                    <i class="fas fa-venus-mars"></i> Gender
                                </label>
                                <select id="gender" name="gender" class="form-select" required data-original="{{ $patientObj->gender ?? '' }}">
                                    <option value="" disabled>Select gender</option>
                                    <option value="Male" {{ (old('gender', $patientObj->gender ?? '') == 'Male') ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ (old('gender', $patientObj->gender ?? '') == 'Female') ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ (old('gender', $patientObj->gender ?? '') == 'Other') ? 'selected' : '' }}>Other</option>
                                </select>
                                <div class="field-changed-indicator"><i class="fas fa-check-circle"></i></div>
                                <div class="original-value-tooltip"><i class="fas fa-history"></i> Original: {{ $patientObj->gender ?? 'Not set' }}</div>
                            </div>

                            <div class="form-group">
                                <label for="dob" class="form-label">
                                    <i class="fas fa-calendar-alt"></i> Date of Birth
                                </label>
                                <input type="date" id="dob" name="dob" class="form-control" required
                                       value="{{ old('dob', isset($patientObj->dob) ? date('Y-m-d', strtotime($patientObj->dob)) : '') }}"
                                       data-original="{{ isset($patientObj->dob) ? date('Y-m-d', strtotime($patientObj->dob)) : '' }}">
                                <div class="field-changed-indicator"><i class="fas fa-check-circle"></i></div>
                                <div class="original-value-tooltip"><i class="fas fa-history"></i> Original: {{ isset($patientObj->dob) ? date('Y-m-d', strtotime($patientObj->dob)) : 'Not set' }}</div>
                            </div>

                            <div class="form-group">
                                <label for="age" class="form-label">
                                    <i class="fas fa-birthday-cake"></i> Age
                                </label>
                                <input type="number" id="age" name="age" class="form-control" placeholder="Age"
                                       value="{{ old('age', $patientObj->age ?? '') }}" data-original="{{ $patientObj->age ?? '' }}">
                                <div class="field-changed-indicator"><i class="fas fa-check-circle"></i></div>
                                <div class="original-value-tooltip"><i class="fas fa-history"></i> Original: {{ $patientObj->age ?? 'Not set' }}</div>
                            </div>

                            <div class="form-group">
                                <label for="visit_no" class="form-label">
                                    <i class="fas fa-clipboard-list"></i> Visit Number
                                </label>
                                <input type="text" id="visit_no" name="visit_no" class="form-control" placeholder="Visit #"
                                       value="{{ old('visit_no', $patientObj->visit_no ?? '') }}" data-original="{{ $patientObj->visit_no ?? '' }}">
                                <div class="field-changed-indicator"><i class="fas fa-check-circle"></i></div>
                                <div class="original-value-tooltip"><i class="fas fa-history"></i> Original: {{ $patientObj->visit_no ?? 'Not set' }}</div>
                            </div>

                            <div class="form-group">
                                <label for="contact" class="form-label">
                                    <i class="fas fa-phone"></i> Contact
                                </label>
                                <input type="text" id="contact" name="contact" class="form-control" placeholder="Phone number"
                                       value="{{ old('contact', $patientObj->contact ?? '') }}" data-original="{{ $patientObj->contact ?? '' }}">
                                <div class="field-changed-indicator"><i class="fas fa-check-circle"></i></div>
                                <div class="original-value-tooltip"><i class="fas fa-history"></i> Original: {{ $patientObj->contact ?? 'Not set' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Demographics Tab -->
                    <div class="tab-pane fade" id="demographics" role="tabpanel">
                        <div class="form-row">
                            <div class="form-group col-md-8">
                                <label for="location" class="form-label">
                                    <i class="fas fa-home"></i> Address
                                </label>
                                <input type="text" id="location" name="location" class="form-control" placeholder="Enter address"
                                       value="{{ old('location', $patientObj->location ?? '') }}" data-original="{{ $patientObj->location ?? '' }}">
                                <div class="field-changed-indicator"><i class="fas fa-check-circle"></i></div>
                                <div class="original-value-tooltip"><i class="fas fa-history"></i> Original: {{ $patientObj->location ?? 'Not set' }}</div>
                            </div>

                            <div class="form-group">
                                <label for="nationality" class="form-label">
                                    <i class="fas fa-flag"></i> Nationality
                                </label>
                                <input type="text" id="nationality" name="nationality" class="form-control" placeholder="Enter nationality"
                                       value="{{ old('nationality', $patientObj->nationality ?? '') }}" data-original="{{ $patientObj->nationality ?? '' }}">
                                <div class="field-changed-indicator"><i class="fas fa-check-circle"></i></div>
                                <div class="original-value-tooltip"><i class="fas fa-history"></i> Original: {{ $patientObj->nationality ?? 'Not set' }}</div>
                            </div>

                            <div class="form-group">
                                <label for="race" class="form-label">
                                    <i class="fas fa-users"></i> Race/Ethnicity
                                </label>
                                <input type="text" id="race" name="race" class="form-control" placeholder="Enter race/ethnicity"
                                       value="{{ old('race', $patientObj->race ?? '') }}" data-original="{{ $patientObj->race ?? '' }}">
                                <div class="field-changed-indicator"><i class="fas fa-check-circle"></i></div>
                                <div class="original-value-tooltip"><i class="fas fa-history"></i> Original: {{ $patientObj->race ?? 'Not set' }}</div>
                            </div>

                            <div class="form-group">
                                <label for="blood_type" class="form-label">
                                    <i class="fas fa-tint"></i> Blood Type
                                </label>
                                <select id="blood_type" name="blood_type" class="form-select" data-original="{{ $patientObj->blood_type ?? '' }}">
                                    <option value="" disabled>Select blood type</option>
                                    @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $blood)
                                        <option value="{{ $blood }}" {{ (old('blood_type', $patientObj->blood_type ?? '') == $blood) ? 'selected' : '' }}>{{ $blood }}</option>
                                    @endforeach
                                </select>
                                <div class="field-changed-indicator"><i class="fas fa-check-circle"></i></div>
                                <div class="original-value-tooltip"><i class="fas fa-history"></i> Original: {{ $patientObj->blood_type ?? 'Not set' }}</div>
                            </div>

                            <div class="form-group">
                                <label for="height" class="form-label">
                                    <i class="fas fa-ruler-vertical"></i> Height (cm)
                                </label>
                                <input type="number" id="height" name="height" class="form-control" placeholder="Height in cm"
                                       value="{{ old('height', $patientObj->height ?? '') }}" data-original="{{ $patientObj->height ?? '' }}">
                                <div class="field-changed-indicator"><i class="fas fa-check-circle"></i></div>
                                <div class="original-value-tooltip"><i class="fas fa-history"></i> Original: {{ $patientObj->height ?? 'Not set' }}</div>
                            </div>

                            <div class="form-group">
                                <label for="weight" class="form-label">
                                    <i class="fas fa-weight"></i> Weight (kg)
                                </label>
                                <input type="number" id="weight" name="weight" class="form-control" placeholder="Weight in kg"
                                       value="{{ old('weight', $patientObj->weight ?? '') }}" data-original="{{ $patientObj->weight ?? '' }}">
                                <div class="field-changed-indicator"><i class="fas fa-check-circle"></i></div>
                                <div class="original-value-tooltip"><i class="fas fa-history"></i> Original: {{ $patientObj->weight ?? 'Not set' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Medical Information Tab -->
                    <div class="tab-pane fade" id="medical-info" role="tabpanel">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="allergies" class="form-label">
                                    <i class="fas fa-allergies"></i> Allergies
                                </label>
                                <input type="text" id="allergies" name="allergies" class="form-control" placeholder="Enter allergies (if any)"
                                       value="{{ old('allergies', $patientObj->allergies ?? '') }}" data-original="{{ $patientObj->allergies ?? '' }}">
                                <div class="field-changed-indicator"><i class="fas fa-check-circle"></i></div>
                                <div class="original-value-tooltip"><i class="fas fa-history"></i> Original: {{ $patientObj->allergies ?? 'Not set' }}</div>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="medical_alerts" class="form-label">
                                    <i class="fas fa-exclamation-triangle"></i> Medical Alerts
                                </label>
                                <input type="text" id="medical_alerts" name="medical_alerts" class="form-control" placeholder="Enter medical alerts (if any)"
                                       value="{{ old('medical_alerts', $patientObj->medical_alerts ?? '') }}" data-original="{{ $patientObj->medical_alerts ?? '' }}">
                                <div class="field-changed-indicator"><i class="fas fa-check-circle"></i></div>
                                <div class="original-value-tooltip"><i class="fas fa-history"></i> Original: {{ $patientObj->medical_alerts ?? 'Not set' }}</div>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="medical_history" class="form-label">
                                    <i class="fas fa-file-medical"></i> Medical History
                                </label>
                                <textarea id="medical_history" name="medical_history" class="form-control" rows="2" placeholder="Brief medical history"
                                          data-original="{{ $patientObj->medical_history ?? '' }}">{{ old('medical_history', $patientObj->medical_history ?? '') }}</textarea>
                                <div class="field-changed-indicator"><i class="fas fa-check-circle"></i></div>
                                <div class="original-value-tooltip"><i class="fas fa-history"></i> Original: {{ $patientObj->medical_history ?? 'Not set' }}</div>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="family_history" class="form-label">
                                    <i class="fas fa-users"></i> Family History
                                </label>
                                <textarea id="family_history" name="family_history" class="form-control" rows="2" placeholder="Relevant family medical history"
                                          data-original="{{ $patientObj->family_history ?? '' }}">{{ old('family_history', $patientObj->family_history ?? '') }}</textarea>
                                <div class="field-changed-indicator"><i class="fas fa-check-circle"></i></div>
                                <div class="original-value-tooltip"><i class="fas fa-history"></i> Original: {{ $patientObj->family_history ?? 'Not set' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Changes Tab -->
                    <div class="tab-pane fade" id="changes-tab" role="tabpanel">
                        <div class="form-section-header">
                            <i class="fas fa-exchange-alt"></i> Changes Summary
                        </div>
                        <div id="changesContainer" class="p-2">
                            <p class="text-muted small">No changes detected yet. Modify fields to see changes here.</p>
                        </div>
                    </div>
                </div>

                <div class="action-buttons">
                    <button type="submit" class="btn-cyber-submit">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                    <a href="{{ route('firebase.nurse.search') }}" class="btn-cyber-cancel">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>

                <!-- Edit History -->
                @if(isset($editsArray) && count($editsArray) > 0)
                    <div class="edit-history mt-4">
                        <div class="form-section-header">
                            <i class="fas fa-history"></i> Edit History
                        </div>
                        @foreach($editsArray as $edit)
                            <div class="history-item">
                                <div class="history-time">{{ date('d M Y H:i', strtotime($edit->updated_at)) }}</div>
                                <div class="history-user">{{ $edit->updated_by ?? 'System' }}</div>
                                <div class="history-changes">{{ $edit->changes ?? 'Updated patient information' }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </form>
    </div>

    <script>
        // Form change tracking and handling
        document.addEventListener('DOMContentLoaded', function() {
            const changesContainer = document.getElementById('changesContainer');
            const changeCounter = document.getElementById('change-counter');
            const formInputs = document.querySelectorAll('input, select, textarea');
            const changedFields = new Map();

            // Get field label from input element
            function getFieldLabel(input) {
                // Try to get label text from label element
                const labelElement = input.closest('.form-group').querySelector('.form-label');
                if (labelElement) {
                    // Get label text without the icon
                    return labelElement.textContent.trim();
                }

                // Fallback to input name with formatting
                return input.name
                    .replace(/_/g, ' ')
                    .replace(/\b\w/g, l => l.toUpperCase());
            }

            // Format value for display
            function formatValue(value, inputType) {
                if (value === null || value === undefined || value === '') {
                    return '<em>Empty</em>';
                }

                if (inputType === 'date') {
                    // Format date in a readable format
                    try {
                        const date = new Date(value);
                        return date.toLocaleDateString();
                    } catch (e) {
                        return value;
                    }
                }

                return value;
            }

            // Update the changes summary
            function updateChangesSummary() {
                if (changedFields.size === 0) {
                    changesContainer.innerHTML = `<p class="text-muted small">No changes detected yet. Modify fields to see changes here.</p>`;
                    changeCounter.style.display = 'none';
                    return;
                }

                changeCounter.style.display = 'inline-block';
                changeCounter.textContent = changedFields.size;

                let html = '';
                changedFields.forEach((change, fieldName) => {
                    html += `
                <div class="change-item">
                    <div class="change-field">${change.label}</div>
                    <div class="ms-auto d-flex align-items-center">
                        <span class="change-old">${formatValue(change.oldValue, change.type)}</span>
                        <span class="change-arrow"><i class="fas fa-long-arrow-alt-right"></i></span>
                        <span class="change-new">${formatValue(change.newValue, change.type)}</span>
                    </div>
                </div>`;
                });

                changesContainer.innerHTML = html;
            }

            // Initialize field change tracking
            formInputs.forEach(input => {
                // Skip buttons
                if (input.type === 'button' || input.type === 'submit') return;

                const originalValue = input.dataset.original || '';

                // Add a changed indicator icon to the input
                if (!input.nextElementSibling || !input.nextElementSibling.classList.contains('field-changed-indicator')) {
                    const indicator = document.createElement('div');
                    indicator.className = 'field-changed-indicator';
                    indicator.innerHTML = '<i class="fas fa-check-circle"></i>';
                    input.insertAdjacentElement('afterend', indicator);
                }

                // Track changes
                input.addEventListener('input', function() {
                    const fieldName = this.name;
                    const currentValue = this.value;

                    if (currentValue !== originalValue) {
                        this.classList.add('field-updated');

                        changedFields.set(fieldName, {
                            label: getFieldLabel(this),
                            oldValue: originalValue,
                            newValue: currentValue,
                            type: this.type
                        });
                    } else {
                        this.classList.remove('field-updated');
                        changedFields.delete(fieldName);
                    }

                    updateChangesSummary();
                });

                // Also check on change event for select elements
                if (input.tagName === 'SELECT') {
                    input.addEventListener('change', function() {
                        const fieldName = this.name;
                        const currentValue = this.value;

                        if (currentValue !== originalValue) {
                            this.classList.add('field-updated');

                            changedFields.set(fieldName, {
                                label: getFieldLabel(this),
                                oldValue: originalValue,
                                newValue: currentValue,
                                type: 'select'
                            });
                        } else {
                            this.classList.remove('field-updated');
                            changedFields.delete(fieldName);
                        }

                        updateChangesSummary();
                    });
                }
            });

            // Auto-calculate age from date of birth
            const dobInput = document.getElementById('dob');
            const ageInput = document.getElementById('age');

            function calculateAge(dob) {
                const birthDate = new Date(dob);
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const monthDiff = today.getMonth() - birthDate.getMonth();

                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }

                return age;
            }

            if (dobInput && ageInput) {
                dobInput.addEventListener('change', function() {
                    if (this.value) {
                        ageInput.value = calculateAge(this.value);
                        // Trigger input event to mark age as updated if different
                        ageInput.dispatchEvent(new Event('input'));
                    }
                });

                // Calculate age on page load if DOB is available
                if (dobInput.value) {
                    ageInput.value = calculateAge(dobInput.value);
                }
            }

            // Tab navigation memory
            const triggerTabList = document.querySelectorAll('button[data-bs-toggle="tab"]');
            triggerTabList.forEach(tabEl => {
                tabEl.addEventListener('shown.bs.tab', function (event) {
                    localStorage.setItem('activeEditTab', event.target.getAttribute('data-bs-target'));
                });
            });

            // Restore active tab on page load
            const activeTab = localStorage.getItem('activeEditTab');
            if (activeTab) {
                const tab = document.querySelector(`button[data-bs-target="${activeTab}"]`);
                if (tab) {
                    const bsTab = new bootstrap.Tab(tab);
                    bsTab.show();
                }
            }

            // Auto-activate Changes tab when changes exist
            document.querySelector('form').addEventListener('input', function() {
                if (changedFields.size > 0) {
                    // Highlight the changes tab with a pulse effect
                    const changesTab = document.querySelector('button[data-bs-target="#changes-tab"]');
                    if (changesTab && !changesTab.classList.contains('active')) {
                        changesTab.classList.add('text-success');
                    }
                }
            });
        });
    </script>
@endsection
