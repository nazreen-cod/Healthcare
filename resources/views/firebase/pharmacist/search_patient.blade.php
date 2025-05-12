
@extends('firebase.layoutpharmacist')

@section('styles')
    <style>
        /* Yellow Theme for Pharmacist Section */
        :root {
            --pharma-primary: #ffbb00;
            --pharma-secondary: #ff9800;
            --pharma-light: #ffdb71;
            --pharma-dark: #151c24;
        }

        /* Section Headers */
        .section-header {
            color: var(--pharma-primary);
            font-family: 'Orbitron', sans-serif;
            font-size: 1.2rem;
            margin: 1.5rem 0 1rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            position: relative;
            display: inline-block;
        }

        .section-header::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 50px;
            height: 2px;
            background: linear-gradient(to right, var(--pharma-primary), transparent);
        }

        /* Patient Data Display */
        .data-row {
            display: flex;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            padding: 6px 0;
        }

        .data-row:last-child {
            border-bottom: none;
        }

        .data-label {
            width: 140px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }

        .data-value {
            flex: 1;
            color: #fff;
            font-size: 0.9rem;
        }

        /* Custom Search Button */
        .btn-cyber-search {
            background: linear-gradient(45deg, var(--pharma-secondary), var(--pharma-primary));
            border: none;
            color: var(--pharma-dark);
            font-weight: 600;
            border-radius: 8px;
            padding: 12px 30px;
            font-size: 1rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            z-index: 1;
            box-shadow: 0 4px 15px rgba(255, 187, 0, 0.3);
        }

        .btn-cyber-search::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, var(--pharma-primary), var(--pharma-light));
            transition: all 0.4s;
            z-index: -1;
        }

        .btn-cyber-search:hover::before {
            left: 0;
        }

        .btn-cyber-search:hover {
            box-shadow: 0 0 20px rgba(255, 187, 0, 0.5);
            transform: translateY(-2px);
            color: var(--pharma-dark);
        }

        /* View Button Styles */
        .btn-action {
            background: rgba(255, 187, 0, 0.2);
            border: 1px solid rgba(255, 187, 0, 0.3);
            color: var(--pharma-primary);
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s;
            display: inline-block;
            text-decoration: none;
        }

        .btn-action:hover {
            background: rgba(255, 187, 0, 0.3);
            color: #fff;
            transform: translateY(-2px);
            text-decoration: none;
        }

        /* Alert Styles */
        .alert-warning {
            background: rgba(255, 187, 0, 0.1);
            border-left: 4px solid var(--pharma-primary);
            color: #ffecb3;
        }

        .animate-in {
            opacity: 0;
            transition: opacity 0.5s ease, transform 0.5s ease;
            transform: translateY(20px);
        }

        .animate-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Tabs for patient details */
        .nav-tabs {
            border-bottom: 1px solid rgba(255, 187, 0, 0.2);
            margin-bottom: 20px;
        }

        .nav-tabs .nav-link {
            color: rgba(255, 255, 255, 0.7);
            border: none;
            border-bottom: 3px solid transparent;
            border-radius: 0;
            padding: 8px 16px;
            margin-right: 10px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .nav-tabs .nav-link:hover {
            color: var(--pharma-primary);
            border-color: rgba(255, 187, 0, 0.3);
            background: rgba(255, 187, 0, 0.05);
        }

        .nav-tabs .nav-link.active {
            color: var(--pharma-primary);
            background: transparent;
            border-color: var(--pharma-primary);
            font-weight: 600;
        }

        .patient-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 1px solid rgba(255, 187, 0, 0.1);
            padding-bottom: 10px;
        }

        .patient-header i {
            color: var(--pharma-primary);
            font-size: 1.5rem;
            margin-right: 10px;
        }

        .patient-header h4 {
            color: #fff;
            margin: 0;
            font-weight: 600;
        }

        .patient-header .badge {
            margin-left: auto;
            background: rgba(255, 187, 0, 0.2);
            color: var(--pharma-primary);
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 6px;
        }

        .tab-content {
            padding: 10px;
        }

        /* Compact table */
        .cyber-table td, .cyber-table th {
            padding: 8px 12px;
            font-size: 0.9rem;
        }

        .patient-card {
            padding: 15px;
            margin-bottom: 15px;
        }

        .alert-section {
            background: rgba(255, 187, 0, 0.05);
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 15px;
            border-left: 3px solid var(--pharma-primary);
        }

        /* Action buttons at the bottom of patient details */
        .patient-actions {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 187, 0, 0.1);
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-med-pickup {
            background: linear-gradient(45deg, #4CAF50, #8BC34A);
            border: none;
            color: white;
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 8px rgba(76, 175, 80, 0.3);
        }

        .btn-med-pickup:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(76, 175, 80, 0.4);
            color: white;
            text-decoration: none;
        }

        .pickup-icon {
            display: inline-block;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
    </style>
@endsection

@section('content')
    <h1 class="page-header mb-3">Patient Search</h1>

    @if(session()->has("error"))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session()->get("error") }}
        </div>
    @endif

    @if(session()->has("success"))
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            {{ session()->get("success") }}
        </div>
    @endif

    <div class="cyber-card animate-in">
        <form method="GET" action="{{ route('firebase.pharmacist.searchPatient') }}">
            <div class="form-group mb-3">
                <label for="search" class="form-label">
                    <i class="fas fa-search me-2" style="color: var(--secondary);"></i>
                    Search by Patient Name or ID Number
                </label>
                <div class="d-flex">
                    <input type="text" id="search" name="search" class="form-control me-2"
                           placeholder="Enter patient name or identification number" required
                           value="{{ request('search') }}">
                    <button type="submit" class="btn-cyber-search">
                        <i class="fas fa-search me-2"></i>Search
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if(isset($patients) && count($patients) > 0)
        <div class="cyber-card animate-in mt-3">
            <h3 class="section-header mb-3">Search Results</h3>
            <div class="table-responsive">
                <table class="cyber-table">
                    <thead>
                    <tr>
                        <th>Patient Name</th>
                        <th>ID Number</th>
                        <th>Gender</th>
                        <th>Age</th>
                        <th>View</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($patients as $index => $patient)
                        <tr class="animate-in" style="animation-delay: {{ $loop->index * 100 }}ms">
                            <td>{{ $patient['name'] ?? 'N/A' }}</td>
                            <td>{{ $patient['id_no'] ?? 'N/A' }}</td>
                            <td>{{ $patient['gender'] ?? 'N/A' }}</td>
                            <td>{{ $patient['age'] ?? 'N/A' }}</td>
                            <td>
                                <button class="btn-action" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#patient-details-{{ $index }}"
                                        aria-expanded="false" aria-controls="patient-details-{{ $index }}">
                                    <i class="fas fa-eye me-1"></i> View
                                </button>
                            </td>
                        </tr>
                        <tr class="collapse" id="patient-details-{{ $index }}">
                            <td colspan="5" class="p-0">
                                <div class="p-3">
                                    <div class="patient-header">
                                        <i class="fas fa-user-circle"></i>
                                        <h4>{{ $patient['name'] ?? 'Unknown Patient' }}</h4>
                                        <span class="badge ms-2">ID: {{ $patient['id_no'] ?? 'N/A' }}</span>
                                    </div>

                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" data-bs-toggle="tab"
                                                    data-bs-target="#demographics-{{ $index }}" type="button">
                                                <i class="fas fa-id-card me-1"></i> Demographics
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" data-bs-toggle="tab"
                                                    data-bs-target="#history-{{ $index }}" type="button">
                                                <i class="fas fa-file-medical-alt me-1"></i> Medical History
                                            </button>
                                        </li>
                                    </ul>

                                    <div class="tab-content">
                                        <!-- Demographics Tab -->
                                        <div class="tab-pane fade show active" id="demographics-{{ $index }}">
                                            @if(!empty($patient['allergies']) || !empty($patient['medical_alerts']))
                                                <div class="alert-section mb-3">
                                                    @if(!empty($patient['allergies']))
                                                        <div class="data-row">
                                                            <div class="data-label">
                                                                <i class="fas fa-exclamation-triangle me-1" style="color: #ff9800;"></i> Allergies:
                                                            </div>
                                                            <div class="data-value" style="color: #ffcc80;">
                                                                {{ $patient['allergies'] }}
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if(!empty($patient['medical_alerts']))
                                                        <div class="data-row">
                                                            <div class="data-label">
                                                                <i class="fas fa-exclamation-circle me-1" style="color: #ff9800;"></i> Medical Alerts:
                                                            </div>
                                                            <div class="data-value" style="color: #ffcc80;">
                                                                {{ $patient['medical_alerts'] }}
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif

                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <div class="data-row">
                                                        <div class="data-label">Gender:</div>
                                                        <div class="data-value">{{ $patient['gender'] ?? 'N/A' }}</div>
                                                    </div>
                                                    <div class="data-row">
                                                        <div class="data-label">Age:</div>
                                                        <div class="data-value">{{ $patient['age'] ?? 'N/A' }}</div>
                                                    </div>
                                                    <div class="data-row">
                                                        <div class="data-label">Date of Birth:</div>
                                                        <div class="data-value">{{ $patient['dob'] ?? 'N/A' }}</div>
                                                    </div>
                                                    <div class="data-row">
                                                        <div class="data-label">Contact:</div>
                                                        <div class="data-value">{{ $patient['contact'] ?? 'N/A' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="data-row">
                                                        <div class="data-label">Height:</div>
                                                        <div class="data-value">{{ $patient['height'] ?? 'N/A' }}</div>
                                                    </div>
                                                    <div class="data-row">
                                                        <div class="data-label">Weight:</div>
                                                        <div class="data-value">{{ $patient['weight'] ?? 'N/A' }}</div>
                                                    </div>
                                                    <div class="data-row">
                                                        <div class="data-label">Blood Type:</div>
                                                        <div class="data-value">{{ $patient['blood_type'] ?? 'N/A' }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Medical History Tab -->
                                        <div class="tab-pane fade" id="history-{{ $index }}">
                                            <div class="data-row">
                                                <div class="data-label">Family History:</div>
                                                <div class="data-value">{{ $patient['family_history'] ?? 'No family history recorded' }}</div>
                                            </div>
                                            <div class="data-row">
                                                <div class="data-label">Medical History:</div>
                                                <div class="data-value">{{ $patient['medical_history'] ?? 'No medical history recorded' }}</div>
                                            </div>
                                            <div class="data-row">
                                                <div class="data-label">Clinical Summary:</div>
                                                <div class="data-value">{{ $patient['clinical_summary'] ?? 'No clinical summary recorded' }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Patient Action Buttons -->
                                    <div class="patient-actions">
                                        <a href="{{ route('firebase.pharmacist.patient.medications', ['patientId' => $patient['id']]) }}"
                                           class="btn-med-pickup">
                                            <span class="pickup-icon"><i class="fas fa-pills"></i></span>
                                            Mark Medication Pickup
                                        </a>


                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @elseif(isset($patients))
        <div class="alert alert-warning mt-4 animate-in">
            <i class="fas fa-exclamation-triangle me-2"></i>
            No patients found matching your search criteria.
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add animation classes to elements when they enter the viewport
            const animateElements = document.querySelectorAll('.animate-in');

            function checkIfInView() {
                animateElements.forEach(element => {
                    const elementTop = element.getBoundingClientRect().top;
                    const elementVisible = 20;

                    if (elementTop < window.innerHeight - elementVisible) {
                        element.classList.add('visible');
                    }
                });
            }

            // Initial check
            checkIfInView();

            // Check on scroll
            window.addEventListener('scroll', checkIfInView);
        });
    </script>
@endsection
