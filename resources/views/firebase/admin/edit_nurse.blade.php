@extends('firebase.layoutadmin')

@section('content')
    <style>
        .main-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 85vh;
            width: 100%;
            padding: 20px 0;
        }

        .form-wrapper {
            max-width: 650px;
            width: 100%;
            position: relative;
            z-index: 10;
        }

        /* Futuristic Header */
        .page-header {
            margin-bottom: 20px;
            font-family: 'Orbitron', sans-serif;
            text-align: center;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 3px;
            position: relative;
            padding-bottom: 15px;
            text-shadow: 0 0 10px rgba(0, 195, 255, 0.5);
            font-size: 1.6rem;
        }

        .page-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 2px;
            background: linear-gradient(to right, transparent, var(--primary), transparent);
        }

        /* Futuristic Card */
        .cyber-card {
            background: rgba(21, 32, 43, 0.8);
            border-radius: 12px;
            overflow: hidden;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 195, 255, 0.2);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            position: relative;
            margin-bottom: 20px;
        }

        .cyber-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, var(--primary), var(--secondary), var(--accent));
        }

        .cyber-card-header {
            background: linear-gradient(90deg, rgba(21, 32, 43, 0.9), rgba(16, 25, 36, 0.9));
            color: var(--primary);
            font-family: 'Orbitron', sans-serif;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(0, 195, 255, 0.2);
            padding: 15px 20px;
            position: relative;
            font-size: 1.1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cyber-card-body {
            padding: 20px;
        }

        /* Compact Form */
        .form-control {
            background: rgba(16, 25, 36, 0.6);
            border: 1px solid rgba(0, 195, 255, 0.2);
            border-radius: 6px;
            color: white;
            padding: 8px 12px;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            background: rgba(16, 25, 36, 0.8);
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 195, 255, 0.25);
            color: white;
        }

        select.form-control {
            background-image: linear-gradient(45deg, transparent 50%, var(--primary) 50%),
            linear-gradient(135deg, var(--primary) 50%, transparent 50%);
            background-position: calc(100% - 20px) calc(1em + 2px),
            calc(100% - 15px) calc(1em + 2px);
            background-size: 5px 5px, 5px 5px;
            background-repeat: no-repeat;
            -webkit-appearance: none;
            appearance: none;
        }

        .form-label {
            margin-bottom: 4px;
            font-weight: 500;
            color: var(--primary);
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 10px;
        }

        /* Password field styling */
        .password-wrapper {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--primary);
            font-size: 0.9rem;
        }

        /* Alert messages */
        .alert {
            border: none;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 15px;
            position: relative;
            overflow: hidden;
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

        /* Error messages */
        .text-danger {
            color: #ff4b4b !important;
            font-size: 0.75rem;
            margin-top: 3px;
            display: block;
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-in {
            animation: fadeIn 0.5s ease forwards;
        }

        /* Two-column layout */
        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-col {
            flex: 1;
        }

        /* Buttons */
        .btn-container {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .btn-cyber-primary {
            flex: 3;
            background: linear-gradient(45deg, var(--secondary), var(--primary));
            border: none;
            color: white;
            border-radius: 6px;
            padding: 10px 15px;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            z-index: 1;
            font-size: 0.9rem;
        }

        .btn-cyber-secondary {
            flex: 2;
            background: linear-gradient(45deg, var(--dark), #2a3b4d);
            border: 1px solid rgba(0, 195, 255, 0.3);
            color: var(--primary);
            border-radius: 6px;
            padding: 10px 15px;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: all 0.3s;
            font-size: 0.9rem;
            text-align: center;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Icons */
        .me-1 {
            margin-right: 0.2rem !important;
        }

        /* Audit log styles */
        .collapsible-section {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s ease, opacity 0.3s ease;
            opacity: 0;
            padding: 0 !important;
        }

        .collapsible-section.open {
            max-height: 2000px;
            opacity: 1;
            padding: 0 !important;
        }

        .audit-log-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            color: rgba(255, 255, 255, 0.85);
        }

        .audit-log-table th {
            background: linear-gradient(90deg, rgba(21, 32, 43, 0.9), rgba(16, 25, 36, 0.9));
            color: var(--primary);
            text-transform: uppercase;
            font-weight: 500;
            letter-spacing: 1px;
            padding: 10px 12px;
            font-family: 'Orbitron', sans-serif;
            font-size: 0.8rem;
            border-bottom: 1px solid rgba(0, 195, 255, 0.2);
        }

        .audit-log-table td {
            padding: 12px;
            vertical-align: middle;
            border-bottom: 1px solid rgba(0, 195, 255, 0.1);
        }

        .audit-log-table tbody tr:hover {
            background: rgba(0, 195, 255, 0.05);
        }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-update {
            background: rgba(0, 195, 255, 0.2);
            color: var(--primary);
        }

        .badge-create {
            background: rgba(50, 205, 50, 0.2);
            color: #32cd32;
        }

        .badge-delete {
            background: rgba(255, 75, 75, 0.2);
            color: #ff4b4b;
        }

        .toggle-audit-log {
            background: linear-gradient(45deg, var(--dark), #2a3b4d);
            border: 1px solid rgba(0, 195, 255, 0.3);
            color: var(--primary);
            border-radius: 6px;
            padding: 5px 15px;
            font-weight: 500;
            letter-spacing: 1px;
            transition: all 0.3s;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .toggle-audit-log:hover {
            background: rgba(0, 195, 255, 0.1);
            text-decoration: none;
        }

        .audit-details-row {
            display: none;
        }

        .audit-details-row.show {
            display: table-row;
        }

        .audit-details-content {
            background: rgba(0, 0, 0, 0.15);
            padding: 15px;
            border-radius: 0 0 6px 6px;
            margin: 0;
            border-left: 2px solid var(--primary);
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.2);
            font-size: 0.85rem;
        }

        .audit-field {
            font-weight: 500;
            color: var(--primary);
            min-width: 120px;
        }

        .audit-old-value {
            color: #ff4b4b;
            margin-right: 15px;
            text-decoration: line-through;
        }

        .audit-new-value {
            color: #32cd32;
        }

        .audit-details-item {
            display: flex;
            margin-bottom: 5px;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Make table full width on small screens */
        @media (max-width: 768px) {
            .audit-log-table {
                min-width: 600px;
            }
        }
    </style>

    <div class="main-container">
        <div class="form-wrapper animate-in">
            <h1 class="page-header">Update Nurse</h1>

            <!-- Combined Alert Messages -->
            @if(session()->has("success") || session()->has("error"))
                <div class="alert {{ session()->has('success') ? 'alert-success' : 'alert-danger' }}">
                    <i class="fas {{ session()->has('success') ? 'fa-check-circle' : 'fa-exclamation-circle' }} me-1"></i>
                    {{ session()->get(session()->has('success') ? "success" : "error") }}
                </div>
            @endif

            <!-- Update Form -->
            <div class="cyber-card">
                <h3 class="cyber-card-header">
                    <i class="fas fa-user-nurse me-1"></i>Edit Nurse Info
                </h3>
                <div class="cyber-card-body">
                    <form method="POST" action="{{ route('firebase.admin.update_nurse', ['id' => $key]) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-row">
                            <!-- Left Column -->
                            <div class="form-col">
                                <!-- Fullname -->
                                <div class="form-group">
                                    <label for="fullname" class="form-label">
                                        <i class="fas fa-user me-1"></i>Fullname
                                    </label>
                                    <input type="text" id="fullname" class="form-control" name="fullname" value="{{ $editdata['fname'] }}" required>
                                    @if ($errors->has('fname'))
                                        <span class="text-danger">{{ $errors->first('fname') }}</span>
                                    @endif
                                </div>

                                <!-- Email -->
                                <div class="form-group">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-1"></i>Email
                                    </label>
                                    <input type="email" id="email" class="form-control" name="email" value="{{ $editdata['email'] }}" required>
                                    @if ($errors->has('email'))
                                        <span class="text-danger">{{ $errors->first('email') }}</span>
                                    @endif
                                </div>

                                <!-- Phone number -->
                                <div class="form-group">
                                    <label for="phone" class="form-label">
                                        <i class="fas fa-phone me-1"></i>Phone
                                    </label>
                                    <input type="tel" id="phone" class="form-control" name="phone" value="{{ $editdata['phone'] }}" required>
                                    @if ($errors->has('phone'))
                                        <span class="text-danger">{{ $errors->first('phone') }}</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="form-col">
                                <!-- Password -->
                                <div class="form-group">
                                    <label for="password" class="form-label">
                                        <i class="fas fa-lock me-1"></i>Password
                                    </label>
                                    <div class="password-wrapper">
                                        <input type="password" id="password" class="form-control" name="password" value="{{ $editdata['password'] }}" required>
                                        <i class="fas fa-eye toggle-password"></i>
                                    </div>
                                    @if ($errors->has('password'))
                                        <span class="text-danger">{{ $errors->first('password') }}</span>
                                    @endif
                                </div>

                                <!-- Department -->
                                <div class="form-group">
                                    <label for="department" class="form-label">
                                        <i class="fas fa-hospital me-1"></i>Department
                                    </label>
                                    <select id="department" name="department" class="form-control" required>
                                        <option value="Emergency" {{ $editdata['department'] == 'Emergency' ? 'selected' : '' }}>Emergency</option>
                                        <option value="Intensive" {{ $editdata['department'] == 'Intensive' ? 'selected' : '' }}>Intensive Care Unit</option>
                                        <option value="Lab" {{ $editdata['department'] == 'Lab' ? 'selected' : '' }}>Lab Staff</option>
                                        <option value="Pharmacy" {{ $editdata['department'] == 'Pharmacy' ? 'selected' : '' }}>Pharmacy Staff</option>
                                    </select>
                                    @if ($errors->has('department'))
                                        <span class="text-danger">{{ $errors->first('department') }}</span>
                                    @endif
                                </div>

                                <!-- Designation -->
                                <div class="form-group">
                                    <label for="designation" class="form-label">
                                        <i class="fas fa-user-tag me-1"></i>Designation
                                    </label>
                                    <select id="designation" name="designation" class="form-control" required>
                                        <option value="Consultation" {{ $editdata['designation'] == 'Consultation' ? 'selected' : '' }}>Consultation</option>
                                        <option value="Specialist" {{ $editdata['designation'] == 'Specialist' ? 'selected' : '' }}>Specialist</option>
                                        <option value="Medical" {{ $editdata['designation'] == 'Medical' ? 'selected' : '' }}>Medical Official</option>
                                    </select>
                                    @if ($errors->has('designation'))
                                        <span class="text-danger">{{ $errors->first('designation') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Credentials in one row -->
                        <div class="form-row">
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="numberMMC" class="form-label">
                                        <i class="fas fa-id-card me-1"></i>MMC Reg.
                                    </label>
                                    <input type="text" id="numberMMC" class="form-control" name="numberMMC" value="{{ $editdata['numberMMC'] }}" maxlength="5" required>
                                    @if ($errors->has('numberMMC'))
                                        <span class="text-danger">{{ $errors->first('numberMMC') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-col">
                                <div class="form-group">
                                    <label for="numberAPC" class="form-label">
                                        <i class="fas fa-certificate me-1"></i>APC Valid.
                                    </label>
                                    <input type="text" id="numberAPC" class="form-control" name="numberAPC" value="{{ $editdata['numberAPC'] }}" maxlength="5" required>
                                    @if ($errors->has('numberAPC'))
                                        <span class="text-danger">{{ $errors->first('numberAPC') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="btn-container">
                            <button type="submit" class="btn-cyber-primary">
                                <i class="fas fa-save me-1"></i>Update Nurse
                            </button>

                            <a href="{{ route('firebase.admin.show_nurse') }}" class="btn-cyber-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Audit Log Section -->
            @if(isset($auditLogs) && count($auditLogs) > 0)
                <div class="cyber-card">
                    <div class="cyber-card-header">
                        <div>
                            <i class="fas fa-history me-1"></i>Edit History
                        </div>
                        <button type="button" id="toggleAuditLog" class="toggle-audit-log">
                            <span>Show History</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                    <div class="cyber-card-body p-0 collapsible-section" id="auditLogSection">
                        <div class="table-responsive">
                            <table class="audit-log-table">
                                <thead>
                                <tr>
                                    <th style="width: 25%">Date</th>
                                    <th style="width: 15%">Action</th>
                                    <th style="width: 25%">Changed By</th>
                                    <th style="width: 35%">Details</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($auditLogs as $log)
                                    <tr>
                                        <td>{{ date('M d, Y h:i A', strtotime($log['timestamp'])) }}</td>
                                        <td>
                                            @if($log['action'] === 'update')
                                                <span class="badge badge-update"><i class="fas fa-edit me-1"></i>Updated</span>
                                            @elseif($log['action'] === 'create')
                                                <span class="badge badge-create"><i class="fas fa-plus-circle me-1"></i>Created</span>
                                            @elseif($log['action'] === 'delete')
                                                <span class="badge badge-delete"><i class="fas fa-trash me-1"></i>Deleted</span>
                                            @endif
                                        </td>
                                        <td>{{ $log['admin_name'] }}</td>
                                        <td>
                                            <button type="button" class="toggle-audit-log toggle-details" data-target="details-{{ $loop->index }}">
                                                <span>View Changes</span>
                                                <i class="fas fa-chevron-down"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="audit-details-row" id="details-{{ $loop->index }}">
                                        <td colspan="4" style="padding: 0;">
                                            <div class="audit-details-content">
                                                @if($log['action'] === 'update' && isset($log['changes']))
                                                    <h6 style="color: var(--primary); margin-bottom: 10px; font-size: 0.9rem;">Fields Changed:</h6>
                                                    @foreach($log['changes'] as $field => $change)
                                                        @if(isset($change['changed']) && $change['changed'])
                                                            <div class="audit-details-item">
                                                                <span class="audit-field">{{ ucfirst($field) }}:</span>
                                                                <span class="audit-old-value">{{ $change['from'] ?? 'N/A' }}</span>
                                                                <i class="fas fa-arrow-right" style="color: var(--primary); margin: 0 5px;"></i>
                                                                <span class="audit-new-value">{{ $change['to'] ?? 'N/A' }}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @elseif($log['action'] === 'create' && isset($log['created_data']))
                                                    <h6 style="color: var(--primary); margin-bottom: 10px; font-size: 0.9rem;">Initial Data:</h6>
                                                    @foreach($log['created_data'] as $field => $value)
                                                        <div class="audit-details-item">
                                                            <span class="audit-field">{{ ucfirst($field) }}:</span>
                                                            <span style="color: white;">{{ $value }}</span>
                                                        </div>
                                                    @endforeach
                                                @elseif($log['action'] === 'delete' && isset($log['deleted_data']))
                                                    <h6 style="color: var(--primary); margin-bottom: 10px; font-size: 0.9rem;">Deleted Data:</h6>
                                                    @foreach($log['deleted_data'] as $field => $value)
                                                        <div class="audit-details-item">
                                                            <span class="audit-field">{{ ucfirst($field) }}:</span>
                                                            <span style="color: #ff4b4b;">{{ $value }}</span>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <p>No detailed information available for this action.</p>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add focus effects
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.style.borderColor = 'var(--primary)';
                    this.style.boxShadow = '0 0 0 3px rgba(0, 195, 255, 0.25)';
                });

                input.addEventListener('blur', function() {
                    this.style.borderColor = 'rgba(0, 195, 255, 0.2)';
                    this.style.boxShadow = 'none';
                });
            });

            // Toggle password visibility
            const togglePassword = document.querySelector('.toggle-password');
            const passwordInput = document.querySelector('#password');

            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    // Toggle the eye icon
                    this.classList.toggle('fa-eye-slash');
                    this.classList.toggle('fa-eye');
                });
            }

            // Toggle audit log section (history is hidden by default)
            const toggleAuditLogBtn = document.getElementById('toggleAuditLog');
            const auditLogSection = document.getElementById('auditLogSection');

            if (toggleAuditLogBtn && auditLogSection) {
                toggleAuditLogBtn.addEventListener('click', function() {
                    auditLogSection.classList.toggle('open');

                    // Update button text and icon
                    const icon = this.querySelector('i');
                    const text = this.querySelector('span');

                    if (auditLogSection.classList.contains('open')) {
                        text.textContent = 'Hide History';
                        icon.classList.remove('fa-chevron-down');
                        icon.classList.add('fa-chevron-up');
                    } else {
                        text.textContent = 'Show History';
                        icon.classList.remove('fa-chevron-up');
                        icon.classList.add('fa-chevron-down');
                    }
                });
            }

            // Toggle audit details
            const toggleDetailBtns = document.querySelectorAll('.toggle-details');
            toggleDetailBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const detailsRow = document.getElementById(targetId);

                    detailsRow.classList.toggle('show');

                    // Update button icon
                    const icon = this.querySelector('i');
                    const text = this.querySelector('span');

                    if (detailsRow.classList.contains('show')) {
                        text.textContent = 'Hide Changes';
                        icon.classList.remove('fa-chevron-down');
                        icon.classList.add('fa-chevron-up');
                    } else {
                        text.textContent = 'View Changes';
                        icon.classList.remove('fa-chevron-up');
                        icon.classList.add('fa-chevron-down');
                    }
                });
            });
        });
    </script>
@endsection
