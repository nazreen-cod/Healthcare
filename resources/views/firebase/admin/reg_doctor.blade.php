@extends('firebase.layoutadmin')

@section('content')
    <style>
        .main-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 85vh;
            padding: 20px 0;
        }

        .form-wrapper {
            max-width: 550px;
            width: 100%;
            position: relative;
            z-index: 10;
        }

        /* Futuristic Header */
        .page-header {
            margin-bottom: 30px;
            font-family: 'Orbitron', sans-serif;
            text-align: center;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 3px;
            position: relative;
            padding-bottom: 15px;
            text-shadow: 0 0 10px rgba(0, 195, 255, 0.5);
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
            padding: 20px;
            position: relative;
        }

        .cyber-card-body {
            padding: 25px;
        }

        /* Form Controls */
        .form-control {
            background: rgba(16, 25, 36, 0.6);
            border: 1px solid rgba(0, 195, 255, 0.2);
            border-radius: 8px;
            color: white;
            padding: 12px 15px;
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
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--primary);
        }

        /* Buttons */
        .btn-cyber-primary {
            background: linear-gradient(45deg, var(--secondary), var(--primary));
            border: none;
            color: white;
            border-radius: 8px;
            padding: 12px 20px;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .btn-cyber-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, var(--primary), var(--accent));
            transition: all 0.4s;
            z-index: -1;
        }

        .btn-cyber-primary:hover::before {
            left: 0;
        }

        .btn-cyber-primary:hover {
            box-shadow: 0 0 15px rgba(0, 195, 255, 0.5);
            transform: translateY(-2px);
        }

        .btn-cyber-secondary {
            background: linear-gradient(45deg, var(--dark), #2a3b4d);
            border: 1px solid rgba(0, 195, 255, 0.3);
            color: var(--primary);
        }

        .btn-cyber-secondary::before {
            background: linear-gradient(45deg, #2a3b4d, var(--dark));
        }

        /* Alert messages */
        .alert {
            border: none;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
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
            font-size: 0.85rem;
            margin-top: 5px;
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

        /* Compact Layout Styles */
        .compact-form .form-group {
            margin-bottom: 15px; /* Reduced from original */
        }

        .compact-form .form-label {
            margin-bottom: 4px; /* Reduced from original */
            font-size: 0.9rem;
        }

        .compact-form .form-control {
            padding: 8px 12px; /* Reduced from original */
            font-size: 0.95rem;
        }

        .compact-form .btn {
            padding: 10px 15px; /* Reduced from original */
        }

        .compact-form .text-danger {
            font-size: 0.8rem;
            margin-top: 3px;
        }

        .compact-form .me-2 {
            margin-right: 0.4rem !important; /* Reduced spacing */
        }

        /* Two-column layout */
        @media (min-width: 768px) {
            .compact-form .form-row {
                display: flex;
                margin-left: -8px;
                margin-right: -8px;
            }

            .compact-form .form-col {
                flex: 1;
                padding-left: 8px;
                padding-right: 8px;
            }
        }
    </style>

    <div class="main-container">
        <div class="form-wrapper animate-in">
            <h1 class="page-header">Doctor Registration</h1>

            <!-- Combined Alert Messages -->
            @if(session()->has("success") || session()->has("error"))
                <div class="alert {{ session()->has('success') ? 'alert-success' : 'alert-danger' }}">
                    <i class="fas {{ session()->has('success') ? 'fa-check-circle' : 'fa-exclamation-circle' }} me-2"></i>
                    {{ session()->get(session()->has('success') ? "success" : "error") }}
                </div>
            @endif

            <!-- Registration Form -->
            <div class="cyber-card">
                <h3 class="cyber-card-header">
                    <i class="fas fa-user-md me-2"></i>Register Doctor
                </h3>
                <div class="cyber-card-body">
                    <form method="POST" action="{{ route('firebase.admin.store_doctor') }}" class="compact-form">
                        @csrf

                        <div class="form-row">
                            <!-- Left Column -->
                            <div class="form-col">
                                <!-- Fullname -->
                                <div class="form-group">
                                    <label for="fullname" class="form-label">
                                        <i class="fas fa-user me-1"></i>Fullname
                                    </label>
                                    <input type="text" id="fullname" class="form-control" name="fullname" required>
                                    @if ($errors->has('fullname'))
                                        <span class="text-danger">{{ $errors->first('fullname') }}</span>
                                    @endif
                                </div>

                                <!-- Email -->
                                <div class="form-group">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-1"></i>Email
                                    </label>
                                    <input type="email" id="email" class="form-control" name="email" required>
                                    @if ($errors->has('email'))
                                        <span class="text-danger">{{ $errors->first('email') }}</span>
                                    @endif
                                </div>

                                <!-- Phone number -->
                                <div class="form-group">
                                    <label for="phone" class="form-label">
                                        <i class="fas fa-phone me-1"></i>Phone
                                    </label>
                                    <input type="tel" id="phone" class="form-control" name="phone" required>
                                    @if ($errors->has('phone'))
                                        <span class="text-danger">{{ $errors->first('phone') }}</span>
                                    @endif
                                </div>

                                <!-- Password -->
                                <div class="form-group">
                                    <label for="password" class="form-label">
                                        <i class="fas fa-lock me-1"></i>Password
                                    </label>
                                    <input type="password" id="password" class="form-control" name="password" required>
                                    @if ($errors->has('password'))
                                        <span class="text-danger">{{ $errors->first('password') }}</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="form-col">
                                <!-- Department -->
                                <div class="form-group">
                                    <label for="department" class="form-label">
                                        <i class="fas fa-hospital me-1"></i>Department
                                    </label>
                                    <select id="department" name="department" class="form-control" required>
                                        <option value="">Select Department</option>
                                        <option value="Emergency">Emergency</option>
                                        <option value="Intensive">Intensive Care Unit</option>
                                        <option value="Lab">Lab Staff</option>
                                        <option value="Pharmacy">Pharmacy Staff</option>
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
                                        <option value="">Select Designation</option>
                                        <option value="Consultation">Consultation</option>
                                        <option value="Specialist">Specialist</option>
                                        <option value="Medical">Medical Official</option>
                                    </select>
                                    @if ($errors->has('designation'))
                                        <span class="text-danger">{{ $errors->first('designation') }}</span>
                                    @endif
                                </div>

                                <!-- MMC and APC in one row -->
                                <div class="row">
                                    <div class="col-6 form-group">
                                        <label for="numberMMC" class="form-label">
                                            <i class="fas fa-id-card me-1"></i>MMC Reg.
                                        </label>
                                        <input type="text" id="numberMMC" name="numberMMC" class="form-control" maxlength="5" required>
                                        @if ($errors->has('numberMMC'))
                                            <span class="text-danger">{{ $errors->first('numberMMC') }}</span>
                                        @endif
                                    </div>

                                    <div class="col-6 form-group">
                                        <label for="numberAPC" class="form-label">
                                            <i class="fas fa-certificate me-1"></i>APC Valid.
                                        </label>
                                        <input type="text" id="numberAPC" name="numberAPC" class="form-control" maxlength="5" required>
                                        @if ($errors->has('numberAPC'))
                                            <span class="text-danger">{{ $errors->first('numberAPC') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <button type="submit" class="btn btn-cyber-primary">
                                <i class="fas fa-user-plus me-1"></i>Register Doctor
                            </button>

                            <a href="{{ route('firebase.admin.show_doctor') }}" class="btn btn-cyber-secondary">
                                <i class="fas fa-list me-1"></i>View All
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add focus effects
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('is-focused');
                });

                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('is-focused');
                });
            });
        });
    </script>
@endsection
