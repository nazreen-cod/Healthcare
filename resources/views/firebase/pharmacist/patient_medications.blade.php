
@extends('firebase.layoutpharmacist')

@section('styles')
    <style>
        /* Root variables */
        :root {
            --pharma-primary: #ffbb00;
            --pharma-secondary: #ff9800;
            --pharma-light: #ffdb71;
            --pharma-dark: #151c24;
        }

        /* All your existing CSS styles */
        .clinical-summary-card {
            background: rgba(0, 0, 0, 0.1);
            border-left: 5px solid var(--pharma-secondary);
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .clinical-summary-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .clinical-summary-title {
            color: var(--pharma-secondary);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .clinical-summary-content {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            line-height: 1.5;
            white-space: pre-line;
            margin-bottom: 15px;
            max-height: 200px;
            overflow-y: auto;
            padding: 8px;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 4px;
        }

        .source-badge {
            background: rgba(255, 152, 0, 0.2);
            color: var(--pharma-primary);
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .section-header {
            color: var(--pharma-primary);
            font-weight: 600;
            font-size: 1.2rem;
            margin-bottom: 1rem;
            display: inline-block;
            position: relative;
        }

        .section-header::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 50px;
            height: 2px;
            background-color: var(--pharma-primary);
        }

        /* Inventory Integration Styles */
        .inventory-warning {
            background-color: rgba(255, 187, 0, 0.1);
            border-left: 4px solid #ffbb00;
            padding: 15px;
            margin-bottom: 20px;
            color: #fff;
            border-radius: 4px;
        }

        .inventory-warning .title {
            color: #ffbb00;
            font-weight: 600;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .inventory-warning .title i {
            margin-right: 8px;
        }

        .inventory-warning ul {
            padding-left: 25px;
            margin-bottom: 0;
            color: rgba(255, 255, 255, 0.8);
        }

        .inventory-warning li {
            margin-bottom: 6px;
        }

        .medication-inventory-status {
            margin: 15px 0;
            padding: 10px;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 4px;
        }

        .inventory-status-title {
            display: flex;
            align-items: center;
            font-weight: 600;
            color: var(--pharma-primary);
            margin-bottom: 10px;
        }

        .inventory-status-title i {
            margin-right: 8px;
        }

        .inventory-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
            padding: 5px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .inventory-item:last-child {
            border-bottom: none;
        }

        .medication-name {
            flex-grow: 1;
            color: rgba(255, 255, 255, 0.8);
        }

        .inventory-stock {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            min-width: 80px;
            text-align: center;
        }

        .stock-available {
            background: rgba(0, 200, 83, 0.15);
            color: #00c853;
        }

        .stock-low {
            background: rgba(255, 187, 0, 0.15);
            color: #ffbb00;
        }

        .stock-none {
            background: rgba(255, 82, 82, 0.15);
            color: #ff5252;
        }

        /* Tooltip styles */
        .tooltip-trigger {
            cursor: pointer;
            position: relative;
        }

        .tooltip-content {
            position: absolute;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            padding: 8px 12px;
            background: rgba(0, 0, 0, 0.9);
            color: white;
            border-radius: 4px;
            font-size: 0.75rem;
            white-space: nowrap;
            z-index: 10;
            display: none;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }

        .tooltip-trigger:hover .tooltip-content {
            display: block;
        }

        .tooltip-content:after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: rgba(0, 0, 0, 0.9) transparent transparent transparent;
        }

        /* Custom Medication Selection Styles */
        .custom-med-section {
            background: rgba(16, 25, 36, 0.7);
            border-radius: 8px;
            border: 1px solid rgba(255, 187, 0, 0.15);
            margin-bottom: 25px;
            overflow: hidden;
        }

        .custom-med-header {
            background: rgba(255, 187, 0, 0.1);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 187, 0, 0.15);
        }

        .custom-med-title {
            color: var(--pharma-primary);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
            font-size: 1.1rem;
        }

        .custom-med-body {
            padding: 20px;
        }

        .custom-med-list {
            margin-top: 15px;
        }

        .selected-med-table {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 4px;
            overflow: hidden;
            margin-top: 15px;
        }

        .med-action-btn {
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-remove-med {
            background: rgba(255, 82, 82, 0.15);
            color: #ff5252;
        }

        .btn-remove-med:hover {
            background: rgba(255, 82, 82, 0.3);
            transform: scale(1.1);
        }

        .med-qty-input {
            width: 60px;
            text-align: center;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 187, 0, 0.2);
            color: white;
            border-radius: 4px;
            padding: 3px;
        }

        .med-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .med-search-input {
            flex-grow: 1;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 187, 0, 0.2);
            color: white;
            border-radius: 4px;
            padding: 8px 12px;
        }

        .med-category-select {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 187, 0, 0.2);
            color: white;
            border-radius: 4px;
            padding: 8px 12px;
            min-width: 150px;
        }

        .med-results {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 187, 0, 0.2);
            border-radius: 4px;
            max-height: 200px;
            overflow-y: auto;
            margin-bottom: 15px;
            display: none;
        }

        .med-result-item {
            padding: 10px 15px;
            border-bottom: 1px solid rgba(255, 187, 0, 0.1);
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .med-result-item:hover {
            background: rgba(255, 187, 0, 0.05);
        }

        .med-result-item:last-child {
            border-bottom: none;
        }

        .med-info {
            display: flex;
            flex-direction: column;
        }

        .med-result-name {
            color: white;
            font-weight: 500;
        }

        .med-result-details {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.8rem;
        }

        .med-result-stock {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            min-width: 80px;
            text-align: center;
            margin-left: 15px;
        }

        .no-meds-message {
            color: rgba(255, 255, 255, 0.6);
            text-align: center;
            padding: 15px;
        }

        .dispense-btn-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 15px;
        }
    </style>
@endsection

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="cyber-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="page-header mb-0">Patient Medications</h2>
                        <a href="{{ route('firebase.pharmacist.search') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-2"></i>Back to Search
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success mb-4">
                            <i class="fas fa-check-circle me-2"></i>{!! session('success') !!}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger mb-4">
                            <i class="fas fa-exclamation-circle me-2"></i>{!! session('error') !!}
                        </div>
                    @endif

                    <!-- Inventory Warning Notice -->
                    <div class="inventory-warning">
                        <div class="title">
                            <i class="fas fa-info-circle"></i> Medication Dispensing & Inventory
                        </div>
                        <p>When you mark medications as "Dispensed", the system will automatically:</p>
                        <ul>
                            <li>Deduct medications from the pharmacy inventory</li>
                            <li>Record the transaction in medication pickup history</li>
                            <li>Update the medication status for this patient</li>
                        </ul>
                    </div>

                    <!-- Patient information -->
                    <div class="patient-info mb-4 p-3 rounded">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="fw-bold text-primary">Patient Name:</div>
                                <div class="text-white">{{ $patient['name'] ?? 'Unknown' }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="fw-bold text-primary">ID Number:</div>
                                <div class="text-white">{{ $patient['id_no'] ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="fw-bold text-primary">Gender:</div>
                                <div class="text-white">{{ $patient['gender'] ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="fw-bold text-primary">Age:</div>
                                <div class="text-white">{{ $patient['age'] ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Custom Medication Selection -->
                    <div class="custom-med-section">
                        <div class="custom-med-header">
                            <h5 class="custom-med-title">
                                <i class="fas fa-pills"></i> Custom Medication Dispensing
                            </h5>
                        </div>
                        <div class="custom-med-body">
                            <p class="text-white opacity-75 mb-3">
                                Select medications directly from inventory to dispense to this patient.
                            </p>

                            <div class="med-selector">
                                <input type="text" id="medicationSearch" class="med-search-input" placeholder="Search medications...">
                                <select id="medicationCategory" class="med-category-select">
                                    <option value="">All Categories</option>
                                    <option value="Antibiotics">Antibiotics</option>
                                    <option value="Analgesics">Analgesics</option>
                                    <option value="Antiviral">Antiviral</option>
                                    <option value="Antihistamines">Antihistamines</option>
                                    <option value="Cardiovascular">Cardiovascular</option>
                                    <option value="Gastrointestinal">Gastrointestinal</option>
                                    <option value="Respiratory">Respiratory</option>
                                    <option value="Vaccines">Vaccines</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="med-results" id="medicationResults">
                                <!-- Results will be populated dynamically -->
                            </div>

                            <div class="custom-med-list">
                                <h6 class="text-white mb-3" id="selectedMedsTitle" style="display: none;">Selected Medications</h6>
                                <div class="selected-med-table">
                                    <table class="table" id="selectedMedicationsTable" style="color: rgba(255, 255, 255, 0.8); margin-bottom: 0;">
                                        <thead>
                                        <tr>
                                            <th>Medication</th>
                                            <th>Category</th>
                                            <th>Dosage</th>
                                            <th class="text-center">Available</th>
                                            <th class="text-center">Quantity</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody id="selectedMedicationsList">
                                        <tr id="noSelectedMedsRow">
                                            <td colspan="6" class="text-center">No medications selected</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="dispense-btn-container" id="dispenseCustomContainer" style="display: none;">
                                    <button id="dispenseCustomBtn" class="btn btn-primary" style="background: linear-gradient(to right, var(--pharma-primary), var(--pharma-secondary)); color: #000; border: none;">
                                        <i class="fas fa-prescription-bottle-alt me-2"></i>Dispense Selected Medications
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h4 class="section-header mb-3">Prescriptions & Medication Status</h4>

                    <!-- Clinical Summary Card -->
                    @if(isset($patient['clinical_summary']) && !empty($patient['clinical_summary']))
                        <div class="clinical-summary-card mb-4">
                            <div class="clinical-summary-header">
                                <div class="clinical-summary-title">
                                    <i class="fas fa-file-medical" style="color: var(--pharma-secondary);"></i>
                                    From Clinical Summary
                                </div>
                                <span class="source-badge">
                                    <i class="fas fa-clipboard-list me-1"></i>
                                    Medical Record
                                </span>
                            </div>

                            <div class="clinical-summary-content">
                                {{ $patient['clinical_summary'] }}
                            </div>

                            <!-- Inventory Status for Clinical Summary -->
                            @if(isset($clinicalSummaryMedications) && count($clinicalSummaryMedications) > 0)
                                <div class="medication-inventory-status">
                                    <div class="inventory-status-title">
                                        <i class="fas fa-boxes"></i> Inventory Status for Medications
                                    </div>
                                    @foreach($clinicalSummaryMedications as $med)
                                        <div class="inventory-item">
                                            <div class="medication-name">{{ $med['name'] }}</div>
                                            <div class="tooltip-trigger">
                                                @if(!isset($inventoryStatuses[strtolower($med['name'])]))
                                                    <span class="inventory-stock stock-none">Not Found</span>
                                                    <div class="tooltip-content">
                                                        Medication not found in inventory
                                                    </div>
                                                @elseif($inventoryStatuses[strtolower($med['name'])]['available'] <= 0)
                                                    <span class="inventory-stock stock-none">Out of Stock</span>
                                                    <div class="tooltip-content">
                                                        Medication is out of stock
                                                    </div>
                                                @elseif($inventoryStatuses[strtolower($med['name'])]['available'] < 5)
                                                    <span class="inventory-stock stock-low">Low Stock ({{ $inventoryStatuses[strtolower($med['name'])]['available'] }})</span>
                                                    <div class="tooltip-content">
                                                        Low inventory: {{ $inventoryStatuses[strtolower($med['name'])]['available'] }} units available
                                                    </div>
                                                @else
                                                    <span class="inventory-stock stock-available">Available ({{ $inventoryStatuses[strtolower($med['name'])]['available'] }})</span>
                                                    <div class="tooltip-content">
                                                        {{ $inventoryStatuses[strtolower($med['name'])]['available'] }} units in stock
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="pickup-form">
                                <form action="{{ route('firebase.pharmacist.mark-medication-pickup', ['prescriptionId' => 'clinical_summary_'.$patientId]) }}" method="POST" class="d-flex justify-content-between align-items-center flex-wrap">
                                    @csrf
                                    <input type="hidden" name="patient_id" value="{{ $patientId }}">
                                    <input type="hidden" name="from_clinical_summary" value="1">

                                    <div class="d-flex align-items-center me-3">
                                        <label class="me-3 fw-bold" style="color: var(--pharma-primary);">Mark medications from clinical summary:</label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="dispensed_cs" value="dispensed" {{ isset($patient['clinical_summary_status']) && $patient['clinical_summary_status'] === 'dispensed' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="dispensed_cs">Dispensed</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="pending_cs" value="pending" {{ !isset($patient['clinical_summary_status']) || $patient['clinical_summary_status'] !== 'dispensed' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="pending_cs">Pending</label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn save-btn medication-dispense-btn" data-source="clinical_summary">
                                        <i class="fas fa-save me-2"></i>Update Status
                                    </button>
                                </form>
                            </div>

                            <!-- Clinical Summary Medications Pickup Details -->
                            @if(isset($patient['clinical_summary_status']) && $patient['clinical_summary_status'] === 'dispensed')
                                <div class="pickup-details">
                                    <div><i class="fas fa-calendar-check me-2"></i><strong>Dispensed Date:</strong>
                                        {{ isset($patient['clinical_summary_updated_at']) ? \Carbon\Carbon::parse($patient['clinical_summary_updated_at'])->format('M d, Y h:i A') : 'Unknown' }}
                                    </div>
                                    <div><i class="fas fa-user-md me-2"></i><strong>Dispensed By:</strong>
                                        {{ $patient['clinical_summary_updated_by'] ?? 'Unknown' }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if(count($prescriptions) > 0)
                        @foreach($prescriptions as $prescriptionId => $prescription)
                            <div class="prescription-card mb-4 border rounded p-3 {{isset($prescription['status']) && $prescription['status'] === 'dispensed' ? 'border-success' : 'border-warning'}}">
                                <!-- Your existing prescription card content -->
                                <div class="prescription-header">
                                    <div class="prescription-title">
                                        <i class="fas fa-file-prescription" style="color: var(--pharma-primary);"></i>
                                        Prescription
                                        <span class="ms-2 badge {{ isset($prescription['status']) && $prescription['status'] === 'dispensed' ? 'bg-success' : 'bg-warning' }}">
                                        {{ isset($prescription['status']) ? ucfirst($prescription['status']) : 'Pending' }}
                                    </span>
                                    </div>
                                    <div class="prescription-date">
                                        {{ isset($prescription['created_at']) ? \Carbon\Carbon::parse($prescription['created_at'])->format('M d, Y') : 'Unknown date' }}
                                    </div>
                                </div>

                                @if(isset($prescription['notes']) && !empty($prescription['notes']))
                                    <div class="doctor-notes mb-3">
                                        <div class="doctor-notes-title">
                                            <i class="fas fa-comment-medical me-2"></i>Doctor's Notes:
                                        </div>
                                        <div class="doctor-notes-content">
                                            {{ $prescription['notes'] }}
                                        </div>
                                    </div>
                                @endif

                                <div class="medications-list">
                                    <div class="table-responsive">
                                        <table class="table table-medications table-bordered">
                                            <thead>
                                            <tr>
                                                <th>Medication Name</th>
                                                <th>Dosage</th>
                                                <th>Quantity</th>
                                                <th>Instructions</th>
                                                <th>Inventory</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($prescription['medications']) && is_array($prescription['medications']))
                                                @foreach($prescription['medications'] as $med)
                                                    <tr>
                                                        <td>{{ $med['name'] ?? 'N/A' }}</td>
                                                        <td>{{ $med['dosage'] ?? 'N/A' }}</td>
                                                        <td>{{ $med['quantity'] ?? 'N/A' }}</td>
                                                        <td>{{ $med['instructions'] ?? 'N/A' }}</td>
                                                        <td class="text-center">
                                                            @php
                                                                $medName = strtolower($med['name'] ?? '');
                                                                $quantity = isset($med['quantity']) ? (int)$med['quantity'] : 1;
                                                                $availableStock = isset($inventoryStatuses[$medName]) ? $inventoryStatuses[$medName]['available'] : 0;
                                                            @endphp

                                                            @if(!isset($inventoryStatuses[$medName]))
                                                                <span class="badge bg-danger">Not Found</span>
                                                            @elseif($availableStock <= 0)
                                                                <span class="badge bg-danger">Out of Stock</span>
                                                            @elseif($availableStock < $quantity)
                                                                <span class="badge bg-warning text-dark">Low ({{ $availableStock }}/{{ $quantity }})</span>
                                                            @else
                                                                <span class="badge bg-success">In Stock</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5" class="text-center">No medications listed</td>
                                                </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Inventory Status Check Results -->
                                @if(isset($prescription['inventory_check']))
                                    <div class="alert {{ $prescription['inventory_check']['all_available'] ? 'alert-success' : 'alert-warning' }} my-3">
                                        <i class="fas {{ $prescription['inventory_check']['all_available'] ? 'fa-check-circle' : 'fa-exclamation-triangle' }} me-2"></i>
                                        <strong>Inventory Check:</strong>
                                        @if($prescription['inventory_check']['all_available'])
                                            All medications are available in inventory.
                                        @else
                                            Some medications have inventory issues.
                                        @endif
                                    </div>
                                @endif

                                <!-- Pickup Status Form -->
                                <div class="pickup-form">
                                    <form action="{{ route('firebase.pharmacist.mark-medication-pickup', ['prescriptionId' => $prescriptionId]) }}" method="POST" class="d-flex justify-content-between align-items-center flex-wrap">
                                        @csrf
                                        <div class="d-flex align-items-center me-3">
                                            <label class="me-3 fw-bold" style="color: var(--pharma-primary);">Mark medication status:</label>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="status" id="dispensed{{ $prescriptionId }}" value="dispensed" {{ isset($prescription['status']) && $prescription['status'] === 'dispensed' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="dispensed{{ $prescriptionId }}">Dispensed</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="status" id="pending{{ $prescriptionId }}" value="pending" {{ !isset($prescription['status']) || $prescription['status'] !== 'dispensed' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="pending{{ $prescriptionId }}">Pending</label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn save-btn medication-dispense-btn" data-prescription-id="{{ $prescriptionId }}">
                                            <i class="fas fa-save me-2"></i>Update Status
                                        </button>
                                    </form>
                                </div>

                                <!-- Pickup Details (if dispensed) -->
                                @if(isset($prescription['status']) && $prescription['status'] === 'dispensed')
                                    <div class="pickup-details">
                                        <div><i class="fas fa-calendar-check me-2"></i><strong>Dispensed Date:</strong>
                                            {{ isset($prescription['dispensed_at']) ? \Carbon\Carbon::parse($prescription['dispensed_at'])->format('M d, Y h:i A') : 'Unknown' }}
                                        </div>
                                        <div><i class="fas fa-user-md me-2"></i><strong>Dispensed By:</strong>
                                            {{ $prescription['dispensed_by'] ?? 'Unknown' }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @elseif(!isset($patient['clinical_summary']) || empty($patient['clinical_summary']))
                        <div class="no-prescriptions">
                            <i class="fas fa-file-medical text-muted d-block mb-3" style="font-size: 2rem;"></i>
                            <p class="mb-0">No prescriptions found for this patient.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="dispenseConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: rgba(16, 25, 36, 0.95); border: 1px solid rgba(255, 187, 0, 0.2);">
                <div class="modal-header" style="border-bottom-color: rgba(255, 187, 0, 0.2);">
                    <h5 class="modal-title" style="color: var(--pharma-primary);">
                        <i class="fas fa-boxes me-2"></i>Confirm Medication Dispensing
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3" style="color: rgba(255, 255, 255, 0.9);">
                        <p>You are about to mark these medications as <strong style="color: var(--pharma-primary);">Dispensed</strong>.</p>
                        <p>This will automatically deduct the following quantities from your inventory:</p>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm" style="color: rgba(255, 255, 255, 0.8); border-color: rgba(255, 255, 255, 0.1);">
                            <thead>
                            <tr style="border-bottom: 1px solid rgba(255, 187, 0, 0.2);">
                                <th>Medication</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Available</th>
                                <th class="text-center">Status</th>
                            </tr>
                            </thead>
                            <tbody id="modalMedicationList">
                            <!-- Will be filled dynamically -->
                            </tbody>
                        </table>
                    </div>

                    <div id="inventoryWarning" class="alert alert-warning mt-3" style="display: none;">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span id="warningText">Some medications are not available in sufficient quantity.</span>
                    </div>
                </div>
                <div class="modal-footer" style="border-top-color: rgba(255, 187, 0, 0.2);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            style="background: rgba(255, 255, 255, 0.1); border: none;">
                        Cancel
                    </button>
                    <button type="button" id="confirmDispenseBtn" class="btn btn-primary"
                            style="background: linear-gradient(to right, var(--pharma-primary), var(--pharma-secondary)); color: #000; border: none;">
                        Confirm Dispensing
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Medication Selection Modal -->
    <div class="modal fade" id="customMedicationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="background: rgba(16, 25, 36, 0.95); border: 1px solid rgba(255, 187, 0, 0.2);">
                <div class="modal-header" style="border-bottom-color: rgba(255, 187, 0, 0.2);">
                    <h5 class="modal-title" style="color: var(--pharma-primary);">
                        <i class="fas fa-pills me-2"></i>Select Medication from Inventory
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm" style="color: rgba(255, 255, 255, 0.8); border-color: rgba(255, 255, 255, 0.1);">
                            <thead>
                            <tr style="border-bottom: 1px solid rgba(255, 187, 0, 0.2);">
                                <th>Medication</th>
                                <th>Category</th>
                                <th>Dosage</th>
                                <th>Available</th>
                                <th class="text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody id="inventoryMedicationList">
                            <!-- Will be filled dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden form for custom medication dispensing -->
    <form id="customDispenseForm" action="{{ route('firebase.pharmacist.custom-dispense') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="patient_id" value="{{ $patientId }}">
        <input type="hidden" name="medications" id="customMedications">
        <input type="hidden" name="notes" id="customNotes">
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get all dispensing buttons
            const dispenseBtns = document.querySelectorAll('.medication-dispense-btn');
            const confirmDispenseBtn = document.getElementById('confirmDispenseBtn');
            const dispenseModal = new bootstrap.Modal(document.getElementById('dispenseConfirmModal'));
            let currentForm = null;

            // Function to check inventory status for prescriptions
            function checkInventoryForPrescription(prescriptionId) {
                // Find the prescription card
                const prescriptionCard = document.querySelector(`.prescription-card form[action*="${prescriptionId}"]`).closest('.prescription-card');
                const medicationRows = prescriptionCard.querySelectorAll('table tbody tr');
                const medicationsToCheck = [];

                // Extract medication data from the table
                medicationRows.forEach(row => {
                    if (row.cells.length >= 5) { // Make sure we have cells with data
                        const name = row.cells[0].textContent.trim();
                        const quantity = parseInt(row.cells[2].textContent.trim()) || 1;
                        const inventoryStatus = row.cells[4].querySelector('.badge').textContent.trim();

                        medicationsToCheck.push({
                            name: name,
                            quantity: quantity,
                            status: inventoryStatus
                        });
                    }
                });

                return medicationsToCheck;
            }

            // Function to check inventory for clinical summary
            function checkInventoryForClinicalSummary() {
                const medicationsToCheck = [];
                const inventoryItems = document.querySelectorAll('.clinical-summary-card .inventory-item');

                inventoryItems.forEach(item => {
                    const name = item.querySelector('.medication-name').textContent.trim();
                    const status = item.querySelector('.inventory-stock').textContent.trim();

                    medicationsToCheck.push({
                        name: name,
                        quantity: 1, // Default to 1 for clinical summary
                        status: status
                    });
                });

                return medicationsToCheck;
            }

            // Display medications in modal
            function displayMedicationsInModal(medications) {
                const medicationList = document.getElementById('modalMedicationList');
                const warningDiv = document.getElementById('inventoryWarning');
                const warningText = document.getElementById('warningText');

                medicationList.innerHTML = '';
                let hasWarning = false;
                let warningMessages = [];

                medications.forEach(med => {
                    const row = document.createElement('tr');

                    // Determine status class
                    let statusClass, statusText;
                    if (med.status.includes('Not Found') || med.status.includes('Out of Stock')) {
                        statusClass = 'text-danger';
                        statusText = med.status;
                        hasWarning = true;
                        warningMessages.push(`${med.name} is ${med.status.toLowerCase()}`);
                    } else if (med.status.includes('Low')) {
                        // Extract available quantity (if available)
                        let availableQty = med.status.match(/\((\d+)\/(\d+)\)/);
                        if (availableQty && parseInt(availableQty[1]) < parseInt(availableQty[2])) {
                            statusClass = 'text-warning';
                            statusText = 'Low Stock';
                            hasWarning = true;
                            warningMessages.push(`${med.name} has insufficient quantity (${availableQty[1]}/${availableQty[2]})`);
                        } else {
                            statusClass = 'text-success';
                            statusText = 'Available';
                        }
                    } else {
                        statusClass = 'text-success';
                        statusText = 'Available';
                    }

                    // Extract available quantity
                    let availableQty = '0';
                    if (med.status.includes('Available')) {
                        const match = med.status.match(/\((\d+)\)/);
                        availableQty = match ? match[1] : 'N/A';
                    } else if (med.status.includes('Low')) {
                        const match = med.status.match(/\((\d+)\/(\d+)\)/);
                        availableQty = match ? match[1] : 'N/A';
                    }

                    row.innerHTML = `
                <td>${med.name}</td>
                <td class="text-center">${med.quantity}</td>
                <td class="text-center">${availableQty}</td>
                <td class="text-center ${statusClass}">${statusText}</td>
            `;
                    medicationList.appendChild(row);
                });

                // Show/hide warning
                if (hasWarning) {
                    warningDiv.style.display = 'block';
                    warningText.textContent = warningMessages.join('. ');
                    confirmDispenseBtn.classList.add('btn-warning');
                    confirmDispenseBtn.classList.remove('btn-primary');
                    confirmDispenseBtn.textContent = 'Dispense Anyway';
                } else {
                    warningDiv.style.display = 'none';
                    confirmDispenseBtn.classList.remove('btn-warning');
                    confirmDispenseBtn.classList.add('btn-primary');
                    confirmDispenseBtn.textContent = 'Confirm Dispensing';
                }
            }

            // Handle dispense button clicks
            dispenseBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    // Only capture dispense button clicks when "Dispensed" is selected
                    const form = this.closest('form');
                    const dispensedRadio = form.querySelector('input[value="dispensed"]');

                    if (!dispensedRadio || !dispensedRadio.checked) {
                        return; // Let the form submit normally if "Pending" is selected
                    }

                    e.preventDefault(); // Prevent form submission
                    currentForm = form; // Store the form for later submission

                    // Check if this is a clinical summary or regular prescription
                    let medications;
                    if (this.dataset.source === 'clinical_summary') {
                        medications = checkInventoryForClinicalSummary();
                    } else if (this.dataset.prescriptionId) {
                        medications = checkInventoryForPrescription(this.dataset.prescriptionId);
                    } else {
                        // Default fallback - let the form submit
                        form.submit();
                        return;
                    }

                    // Display medications in the modal
                    displayMedicationsInModal(medications);

                    // Show the modal
                    dispenseModal.show();
                });
            });

            // Handle confirm button click
            confirmDispenseBtn.addEventListener('click', function() {
                if (currentForm) {
                    currentForm.submit();
                }
                dispenseModal.hide();
            });

            // ======== CUSTOM MEDICATION SELECTION FUNCTIONALITY ========

            // Initialize variables
            const medicationSearch = document.getElementById('medicationSearch');
            const medicationCategory = document.getElementById('medicationCategory');
            const medicationResults = document.getElementById('medicationResults');
            const selectedMedicationsList = document.getElementById('selectedMedicationsList');
            const noSelectedMedsRow = document.getElementById('noSelectedMedsRow');
            const selectedMedsTitle = document.getElementById('selectedMedsTitle');
            const dispenseCustomBtn = document.getElementById('dispenseCustomBtn');
            const dispenseCustomContainer = document.getElementById('dispenseCustomContainer');
            const customDispenseForm = document.getElementById('customDispenseForm');
            const customMedicationsInput = document.getElementById('customMedications');

            // Store selected medications
            let selectedMedications = [];

            // Initialize custom medication modal
            const customMedicationModal = new bootstrap.Modal(document.getElementById('customMedicationModal'));

            // CRITICAL FIX: Use allInventoryItems instead of fullInventory
            // Load all inventory data from the controller
            let inventoryData = @json($allInventoryItems ?? []);

            // Debug info to help diagnose the issue
            console.log("Loaded inventory items:", inventoryData);

            // Add button that was missing
            const customMedHeader = document.querySelector('.custom-med-header');
            if (customMedHeader && !document.getElementById('addFromInventoryBtn')) {
                const addFromInventoryBtn = document.createElement('button');
                addFromInventoryBtn.id = 'addFromInventoryBtn';
                addFromInventoryBtn.className = 'btn btn-sm';
                addFromInventoryBtn.style = 'background: linear-gradient(to right, var(--pharma-primary), var(--pharma-secondary)); color: #000; border: none;';
                addFromInventoryBtn.innerHTML = '<i class="fas fa-box-open me-1"></i> Select from Inventory';
                customMedHeader.appendChild(addFromInventoryBtn);

                // Event listener for the Add from Inventory button
                addFromInventoryBtn.addEventListener('click', function() {
                    populateInventoryModal();
                    customMedicationModal.show();
                });
            }

            // Function to populate the inventory modal
            function populateInventoryModal() {
                const inventoryMedicationList = document.getElementById('inventoryMedicationList');
                inventoryMedicationList.innerHTML = '';

                if (!inventoryData || inventoryData.length === 0) {
                    inventoryMedicationList.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center">No medications found in inventory</td>
                </tr>
            `;
                    return;
                }

                // Sort by name
                let sortedInventory = [...inventoryData];
                sortedInventory.sort((a, b) => {
                    const nameA = (a.medication_name || '').toLowerCase();
                    const nameB = (b.medication_name || '').toLowerCase();
                    return nameA.localeCompare(nameB);
                });

                sortedInventory.forEach(med => {
                    if (!med) return; // Skip undefined items

                    const isSelected = selectedMedications.some(m => m.id === med.id);
                    const row = document.createElement('tr');

                    // Get quantity or default to 0
                    const quantity = parseInt(med.quantity) || 0;

                    // Determine stock status
                    let stockClass, stockText;
                    if (quantity <= 0) {
                        stockClass = 'text-danger';
                        stockText = 'Out of Stock';
                    } else if (quantity < 5) {
                        stockClass = 'text-warning';
                        stockText = `${quantity} units`;
                    } else {
                        stockClass = 'text-success';
                        stockText = `${quantity} units`;
                    }

                    row.innerHTML = `
                <td>${med.medication_name || 'Unknown'}</td>
                <td>${med.category || 'N/A'}</td>
                <td>${med.dosage || 'N/A'}</td>
                <td class="${stockClass}">${stockText}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm add-med-btn" ${isSelected || quantity <= 0 ? 'disabled' : ''} data-id="${med.id}"
                        style="background: ${isSelected ? 'rgba(0, 200, 83, 0.15)' : 'linear-gradient(to right, var(--pharma-primary), var(--pharma-secondary))'};
                        color: ${isSelected ? '#00c853' : '#000'}; border: none;">
                        ${isSelected ? '<i class="fas fa-check"></i> Added' : '<i class="fas fa-plus"></i> Add'}
                    </button>
                </td>
            `;

                    inventoryMedicationList.appendChild(row);

                    // Add click event for add button
                    if (!isSelected && quantity > 0) {
                        const addBtn = row.querySelector('.add-med-btn');
                        addBtn.addEventListener('click', function() {
                            const medId = this.dataset.id;
                            const medication = inventoryData.find(m => m.id === medId);

                            if (!selectedMedications.some(m => m.id === medId)) {
                                addToSelectedMedications(medication);

                                // Update button appearance
                                this.innerHTML = '<i class="fas fa-check"></i> Added';
                                this.disabled = true;
                                this.style.background = 'rgba(0, 200, 83, 0.15)';
                                this.style.color = '#00c853';
                            }
                        });
                    }
                });
            }

            // Function to filter medications based on search and category
            function filterMedications() {
                const searchTerm = medicationSearch.value.toLowerCase();
                const category = medicationCategory.value;

                // Filter medications
                let filteredMeds = [];
                if (inventoryData && inventoryData.length > 0) {
                    filteredMeds = inventoryData.filter(med => {
                        if (!med || !med.medication_name) return false;

                        const nameMatch = med.medication_name.toLowerCase().includes(searchTerm);
                        const categoryMatch = category === '' || (med.category && med.category.toLowerCase() === category.toLowerCase());
                        return nameMatch && categoryMatch;
                    });
                }

                // Display results
                if (searchTerm || category) {
                    displayMedicationResults(filteredMeds);
                } else {
                    medicationResults.style.display = 'none';
                }
            }

            // Display medication search results
            function displayMedicationResults(medications) {
                medicationResults.innerHTML = '';
                medicationResults.style.display = 'block';

                if (!medications || medications.length === 0) {
                    medicationResults.innerHTML = `
                <div class="no-meds-message">
                    <i class="fas fa-search me-2"></i> No medications found matching your criteria
                </div>
            `;
                    return;
                }

                medications.forEach(med => {
                    const resultItem = document.createElement('div');
                    resultItem.className = 'med-result-item';

                    // Check if already selected
                    const isSelected = selectedMedications.some(selected => selected.id === med.id);

                    // Get quantity or default to 0
                    const quantity = parseInt(med.quantity) || 0;

                    // Determine stock status
                    let stockClass, stockText;
                    if (quantity <= 0) {
                        stockClass = 'stock-none';
                        stockText = 'Out of Stock';
                    } else if (quantity < 5) {
                        stockClass = 'stock-low';
                        stockText = `Low Stock (${quantity})`;
                    } else {
                        stockClass = 'stock-available';
                        stockText = `Available (${quantity})`;
                    }

                    resultItem.innerHTML = `
                <div class="med-info">
                    <div class="med-result-name">${med.medication_name || 'Unknown'}</div>
                    <div class="med-result-details">${med.dosage || 'No dosage'} • ${med.category || 'Uncategorized'}</div>
                </div>
                <div class="d-flex align-items-center">
                    <span class="med-result-stock ${stockClass}">${stockText}</span>
                    <button type="button" class="btn btn-sm ms-3 select-med-btn" ${isSelected || quantity <= 0 ? 'disabled' : ''} data-id="${med.id}"
                        style="background: linear-gradient(to right, var(--pharma-primary), var(--pharma-secondary)); color: #000; border: none;">
                        ${isSelected ? '<i class="fas fa-check"></i>' : '<i class="fas fa-plus"></i>'}
                    </button>
                </div>
            `;

                    medicationResults.appendChild(resultItem);

                    // Add event listener to the select button if not disabled
                    if (!isSelected && quantity > 0) {
                        const selectBtn = resultItem.querySelector('.select-med-btn');
                        selectBtn.addEventListener('click', function() {
                            const medId = this.dataset.id;
                            const medication = medications.find(m => m.id === medId);

                            // Add to selected medications if not already there
                            if (!selectedMedications.some(m => m.id === medId)) {
                                addToSelectedMedications(medication);
                            }

                            // Close results
                            medicationResults.style.display = 'none';
                            medicationSearch.value = '';
                        });
                    }
                });
            }

            // Add medication to the selected list
            function addToSelectedMedications(medication) {
                // Add to array with initial quantity of 1
                const medWithQty = {
                    ...medication,
                    dispense_quantity: 1
                };

                selectedMedications.push(medWithQty);
                updateSelectedMedicationsTable();
            }

            // Update the selected medications table
            function updateSelectedMedicationsTable() {
                // Show/hide no medications row
                if (selectedMedications.length > 0) {
                    noSelectedMedsRow.style.display = 'none';
                    selectedMedsTitle.style.display = 'block';
                    dispenseCustomContainer.style.display = 'flex';
                } else {
                    noSelectedMedsRow.style.display = '';
                    selectedMedsTitle.style.display = 'none';
                    dispenseCustomContainer.style.display = 'none';
                }

                // Remove existing medication rows (except the no-meds row)
                const existingRows = selectedMedicationsList.querySelectorAll('tr:not(#noSelectedMedsRow)');
                existingRows.forEach(row => row.remove());

                // Add selected medications
                selectedMedications.forEach((med, index) => {
                    const row = document.createElement('tr');

                    // Get quantity or default to 0
                    const quantity = parseInt(med.quantity) || 0;

                    // Determine stock status for quantity
                    let quantityClass = '';
                    if (med.dispense_quantity > quantity) {
                        quantityClass = 'text-danger';
                    }

                    row.innerHTML = `
                <td>${med.medication_name || 'Unknown'}</td>
                <td>${med.category || 'N/A'}</td>
                <td>${med.dosage || 'N/A'}</td>
                <td class="text-center">${quantity}</td>
                <td class="text-center">
                    <input type="number" class="med-qty-input ${quantityClass}" value="${med.dispense_quantity}"
                           min="1" max="${quantity}" data-index="${index}">
                </td>
                <td class="text-center">
                    <button class="med-action-btn btn-remove-med" data-index="${index}" title="Remove">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            `;

                    selectedMedicationsList.appendChild(row);

                    // Add event listeners
                    const qtyInput = row.querySelector('.med-qty-input');
                    qtyInput.addEventListener('change', function() {
                        const index = parseInt(this.dataset.index);
                        const newQty = parseInt(this.value) || 1;

                        // Ensure quantity is at least 1
                        if (newQty < 1) {
                            this.value = 1;
                            selectedMedications[index].dispense_quantity = 1;
                        }
                        // Warn if exceeding available stock
                        else if (newQty > (selectedMedications[index].quantity || 0)) {
                            this.classList.add('text-danger');
                            selectedMedications[index].dispense_quantity = newQty;
                        }
                        // Normal case
                        else {
                            this.classList.remove('text-danger');
                            selectedMedications[index].dispense_quantity = newQty;
                        }
                    });

                    const removeBtn = row.querySelector('.btn-remove-med');
                    removeBtn.addEventListener('click', function() {
                        const index = parseInt(this.dataset.index);
                        selectedMedications.splice(index, 1);
                        updateSelectedMedicationsTable();
                    });
                });
            }

            // Event listeners for medication search
            if (medicationSearch) {
                medicationSearch.addEventListener('input', filterMedications);
            }

            if (medicationCategory) {
                medicationCategory.addEventListener('change', filterMedications);
            }

            // Hide results when clicking outside
            document.addEventListener('click', function(e) {
                if (medicationResults && !medicationResults.contains(e.target) && e.target !== medicationSearch) {
                    medicationResults.style.display = 'none';
                }
            });

            // Update category options dynamically
            function updateCategoryOptions() {
                if (!medicationCategory || !inventoryData) return;

                // Extract unique categories
                const categories = new Set();
                inventoryData.forEach(med => {
                    if (med && med.category) {
                        categories.add(med.category);
                    }
                });

                // Clear existing options (except the first one)
                while (medicationCategory.options.length > 1) {
                    medicationCategory.remove(1);
                }

                // Add new options
                const sortedCategories = Array.from(categories).sort();
                sortedCategories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category;
                    option.textContent = category;
                    medicationCategory.appendChild(option);
                });
            }

            // Call to update categories
            updateCategoryOptions();

            // Handle the custom dispense button
            if (dispenseCustomBtn) {
                dispenseCustomBtn.addEventListener('click', function() {
                    // Validate if there are selected medications
                    if (selectedMedications.length === 0) {
                        alert('Please select at least one medication to dispense.');
                        return;
                    }

                    // Check for quantity issues
                    const qtyIssues = selectedMedications.filter(med => med.dispense_quantity > (med.quantity || 0));
                    if (qtyIssues.length > 0) {
                        if (!confirm(`Warning: Some medications exceed available inventory. Are you sure you want to continue?\n\n${qtyIssues.map(med => `${med.medication_name}: ${med.dispense_quantity} requested, ${med.quantity} available`).join('\n')}`)) {
                            return;
                        }
                    }

                    // Format medications for submission
                    const formattedMeds = selectedMedications.map(med => ({
                        id: med.id,
                        name: med.medication_name || 'Unknown',
                        quantity: med.dispense_quantity,
                        dosage: med.dosage || '',
                        category: med.category || '',
                        expiry_date: med.expiry_date || '',
                        instructions: ''
                    }));

                    // Set the form input values
                    customMedicationsInput.value = JSON.stringify(formattedMeds);
                    document.getElementById('customNotes').value = 'Custom dispensation by pharmacist';

                    // Submit the form
                    customDispenseForm.submit();
                });
            }
        });
    </script>
@endsection
