@extends('firebase.app1')

@section('content')
    <style>
        /* Doctor Login Page Styles */
        .login-container {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 0;
        }

        .login-card {
            background: rgba(21, 32, 43, 0.8);
            border-radius: 12px;
            overflow: hidden;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 195, 255, 0.2);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            position: relative;
            width: 100%;
            max-width: 450px;
            margin: 0 auto;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, var(--primary), var(--secondary), var(--accent));
        }

        .login-header {
            background: linear-gradient(90deg, rgba(21, 32, 43, 0.9), rgba(16, 25, 36, 0.9));
            color: var(--primary);
            font-family: 'Orbitron', sans-serif;
            letter-spacing: 2px;
            border-bottom: 1px solid rgba(0, 195, 255, 0.2);
            padding: 20px;
            position: relative;
            text-align: center;
        }

        .login-header h4 {
            margin: 0;
            font-weight: 600;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .login-body {
            padding: 30px;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: var(--primary);
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .form-control {
            background: rgba(16, 25, 36, 0.6);
            border: 1px solid rgba(0, 195, 255, 0.2);
            border-radius: 8px;
            color: white;
            padding: 12px 15px;
            padding-left: 40px;
            transition: all 0.3s;
            width: 100%;
        }

        .form-control:focus {
            background: rgba(16, 25, 36, 0.8);
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 195, 255, 0.25);
            color: white;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        /* Fixed position for input icons */
        .position-relative {
            position: relative;
        }

        .input-icon {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: var(--primary);
            z-index: 10;
        }

        .password-wrapper {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--primary);
            z-index: 10;
        }

        /* Login Button */
        .btn-cyber-login {
            background: linear-gradient(45deg, var(--secondary), var(--primary));
            border: none;
            color: white;
            border-radius: 8px;
            padding: 12px 25px;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            z-index: 1;
            width: 100%;
            font-family: 'Orbitron', sans-serif;
            margin-top: 15px;
        }

        .btn-cyber-login::before {
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

        .btn-cyber-login:hover::before {
            left: 0;
        }

        .btn-cyber-login:hover {
            box-shadow: 0 0 15px rgba(0, 195, 255, 0.5);
            transform: translateY(-2px);
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

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-in {
            animation: fadeIn 0.5s ease forwards;
        }

        /* Medical caduceus design */
        .medical-symbol {
            position: absolute;
            width: 300px;
            height: 300px;
            background:
                radial-gradient(circle at center,
                rgba(0, 195, 255, 0.05) 0%,
                rgba(0, 195, 255, 0) 70%);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: -1;
            opacity: 0.5;
        }

        @media (max-width: 576px) {
            .login-body {
                padding: 20px;
            }
        }
    </style>

    <div class="login-container">
        <div class="login-card animate-in">
            <div class="login-header">
                <h4><i class="fas fa-user-md me-2"></i> Doctor Login</h4>
            </div>
            <div class="login-body">
                <!-- Display success message -->
                @if(session()->has('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Display error message -->
                @if(session()->has('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Display validation errors -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('firebase.doctor.logindoctor.submit') }}">
                    @csrf
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <div class="position-relative">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="password-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                            <i class="fas fa-eye-slash toggle-password"></i>
                        </div>
                    </div>

                    <div class="form-group mb-0 text-center">
                        <button type="submit" class="btn-cyber-login">
                            <i class="fas fa-sign-in-alt me-2"></i>Log in
                        </button>
                    </div>
                </form>

                <div class="medical-symbol"></div>
            </div>
        </div>
    </div>

    <script>
        // Add toggle password visibility
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
@endsection
