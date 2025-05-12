@extends('firebase.layoutnurse')

@section('title', 'Register Patient')

@section('content')
    <style>
        /* Compact Register Patient Styles */
        .register-container {
            padding: 15px 0;
        }

        .register-header {
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

        .register-header::after {
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

        /* Compact Form Section Header */
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

        /* Compact Submit Button */
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

        /* Compact Grid Layout */
        .form-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
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
        }

        @media (max-width: 575.98px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        /* Tabs for registration sections */
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

    <div class="container register-container">
        <h2 class="register-header mb-3"><i class="fas fa-user-plus me-2"></i>Register Patient</h2>

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

        <form method="POST" action="{{ route('firebase.nurse.store_Patient') }}" class="animate-in">
            @csrf
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
                </ul>

                <div class="tab-content">
                    <!-- Personal Information Tab -->
                    <div class="tab-pane fade show active" id="personal-info" role="tabpanel">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user"></i> Full Name
                                </label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="Enter full name" required value="{{ old('name') }}">
                            </div>

                            <div class="form-group">
                                <label for="id_no" class="form-label">
                                    <i class="fas fa-id-badge"></i> ID Number
                                </label>
                                <input type="text" id="id_no" name="id_no" class="form-control" placeholder="Enter ID number" value="{{ old('id_no') }}">
                            </div>

                            <div class="form-group">
                                <label for="gender" class="form-label">
                                    <i class="fas fa-venus-mars"></i> Gender
                                </label>
                                <select id="gender" name="gender" class="form-select" required>
                                    <option value="" disabled selected>Select gender</option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="dob" class="form-label">
                                    <i class="fas fa-calendar-alt"></i> Date of Birth
                                </label>
                                <input type="date" id="dob" name="dob" class="form-control" required value="{{ old('dob') }}">
                            </div>

                            <div class="form-group">
                                <label for="age" class="form-label">
                                    <i class="fas fa-birthday-cake"></i> Age
                                </label>
                                <input type="number" id="age" name="age" class="form-control" placeholder="Age" value="{{ old('age') }}">
                            </div>

                            <div class="form-group">
                                <label for="visit_no" class="form-label">
                                    <i class="fas fa-clipboard-list"></i> Visit Number
                                </label>
                                <input type="text" id="visit_no" name="visit_no" class="form-control" placeholder="Visit #" value="{{ old('visit_no') }}">
                            </div>

                            <div class="form-group">
                                <label for="contact" class="form-label">
                                    <i class="fas fa-phone"></i> Contact
                                </label>
                                <input type="text" id="contact" name="contact" class="form-control" placeholder="Phone number" value="{{ old('contact') }}">
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
                                <input type="text" id="location" name="location" class="form-control" placeholder="Enter address" value="{{ old('location') }}">
                            </div>

                            <div class="form-group">
                                <label for="nationality" class="form-label">
                                    <i class="fas fa-flag"></i> Nationality
                                </label>
                                <input type="text" id="nationality" name="nationality" class="form-control" placeholder="Enter nationality" value="{{ old('nationality') }}">
                            </div>

                            <div class="form-group">
                                <label for="race" class="form-label">
                                    <i class="fas fa-users"></i> Race/Ethnicity
                                </label>
                                <input type="text" id="race" name="race" class="form-control" placeholder="Enter race/ethnicity" value="{{ old('race') }}">
                            </div>

                            <div class="form-group">
                                <label for="blood_type" class="form-label">
                                    <i class="fas fa-tint"></i> Blood Type
                                </label>
                                <select id="blood_type" name="blood_type" class="form-select">
                                    <option value="" selected disabled>Select blood type</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="height" class="form-label">
                                    <i class="fas fa-ruler-vertical"></i> Height (cm)
                                </label>
                                <input type="number" id="height" name="height" class="form-control" placeholder="Height in cm" value="{{ old('height') }}">
                            </div>

                            <div class="form-group">
                                <label for="weight" class="form-label">
                                    <i class="fas fa-weight"></i> Weight (kg)
                                </label>
                                <input type="number" id="weight" name="weight" class="form-control" placeholder="Weight in kg" value="{{ old('weight') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Medical Information Tab -->
                    <div class="tab-pane fade" id="medical-info" role="tabpanel">
                        <div class="form-row">
                            <div class="form-group" style="grid-column: span 2;">
                                <label for="allergies" class="form-label">
                                    <i class="fas fa-allergies"></i> Allergies
                                </label>
                                <input type="text" id="allergies" name="allergies" class="form-control" placeholder="Enter allergies (if any)" value="{{ old('allergies') }}">
                            </div>

                            <div class="form-group" style="grid-column: span 2;">
                                <label for="medical_alerts" class="form-label">
                                    <i class="fas fa-exclamation-triangle"></i> Medical Alerts
                                </label>
                                <input type="text" id="medical_alerts" name="medical_alerts" class="form-control" placeholder="Enter medical alerts (if any)" value="{{ old('medical_alerts') }}">
                            </div>

                            <div class="form-group" style="grid-column: span 2;">
                                <label for="current_medications" class="form-label">
                                    <i class="fas fa-pills"></i> Current Medications
                                </label>
                                <input type="text" id="current_medications" name="current_medications" class="form-control" placeholder="List current medications" value="{{ old('current_medications') }}">
                            </div>

                            <div class="form-group" style="grid-column: span 2;">
                                <label for="chronic_conditions" class="form-label">
                                    <i class="fas fa-heartbeat"></i> Chronic Conditions
                                </label>
                                <input type="text" id="chronic_conditions" name="chronic_conditions" class="form-control" placeholder="List chronic conditions" value="{{ old('chronic_conditions') }}">
                            </div>

                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label for="medical_history" class="form-label">
                                    <i class="fas fa-file-medical"></i> Medical History
                                </label>
                                <textarea id="medical_history" name="medical_history" class="form-control" rows="2" placeholder="Brief medical history">{{ old('medical_history') }}</textarea>
                            </div>

                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label for="family_history" class="form-label">
                                    <i class="fas fa-users"></i> Family History
                                </label>
                                <textarea id="family_history" name="family_history" class="form-control" rows="2" placeholder="Relevant family medical history">{{ old('family_history') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <button type="submit" class="btn-cyber-submit">
                        <i class="fas fa-save me-2"></i>Register Patient
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Auto-calculate age from date of birth
        document.addEventListener('DOMContentLoaded', function() {
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
                    }
                });
            }

            // Tab navigation memory
            const triggerTabList = document.querySelectorAll('button[data-bs-toggle="tab"]');
            triggerTabList.forEach(tabEl => {
                tabEl.addEventListener('shown.bs.tab', function (event) {
                    localStorage.setItem('activeRegisterTab', event.target.getAttribute('data-bs-target'));
                });
            });

            // Restore active tab on page load
            const activeTab = localStorage.getItem('activeRegisterTab');
            if (activeTab) {
                const tab = document.querySelector(`button[data-bs-target="${activeTab}"]`);
                if (tab) {
                    const bsTab = new bootstrap.Tab(tab);
                    bsTab.show();
                }
            }
        });
    </script>
@endsection
