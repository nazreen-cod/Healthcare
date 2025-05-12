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
            padding: 15px 20px;
            position: relative;
            font-size: 1.1rem;
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

        .form-label {
            margin-bottom: 4px;
            font-weight: 500;
            color: var(--primary);
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 10px;
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

        /* Icons */
        .me-1 {
            margin-right: 0.2rem !important;
        }
    </style>

    <div class="main-container">
        <div class="form-wrapper animate-in">
            <h1 class="page-header">Pharmacist Registration</h1>

            <!-- Combined Alert Messages -->
            @if(session()->has("success") || session()->has("error"))
                <div class="alert {{ session()->has('success') ? 'alert-success' : 'alert-danger' }}">
                    <i class="fas {{ session()->has('success') ? 'fa-check-circle' : 'fa-exclamation-circle' }} me-1"></i>
                    {{ session()->get(session()->has('success') ? "success" : "error") }}
                </div>
            @endif

            <!-- Registration Form -->
            <div class="cyber-card">
                <h3 class="cyber-card-header">
                    <i class="fas fa-prescription-bottle-alt me-1"></i>Register Pharmacist
                </h3>
                <div class="cyber-card-body">
                    <form method="POST" action="{{ route('firebase.admin.store_pharmacist') }}">
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
                            </div>

                            <!-- Right Column -->
                            <div class="form-col">
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

                                <!-- Credentials in one row inside the right column -->
                                <div class="form-row">
                                    <div class="form-col">
                                        <div class="form-group">
                                            <label for="numberMMC" class="form-label">
                                                <i class="fas fa-id-card me-1"></i>MMC Reg.
                                            </label>
                                            <input type="text" id="numberMMC" name="numberMMC" class="form-control" maxlength="5" required>
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
                                            <input type="text" id="numberAPC" name="numberAPC" class="form-control" maxlength="5" required>
                                            @if ($errors->has('numberAPC'))
                                                <span class="text-danger">{{ $errors->first('numberAPC') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="btn-container">
                            <button type="submit" class="btn-cyber-primary">
                                <i class="fas fa-user-plus me-1"></i>Register Pharmacist
                            </button>

                            <a href="{{ route('firebase.admin.show_pharmacist') }}" class="btn-cyber-secondary">
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
                    this.style.borderColor = 'var(--primary)';
                    this.style.boxShadow = '0 0 0 3px rgba(0, 195, 255, 0.25)';
                });

                input.addEventListener('blur', function() {
                    this.style.borderColor = 'rgba(0, 195, 255, 0.2)';
                    this.style.boxShadow = 'none';
                });
            });
        });
    </script>
@endsection
