@extends('firebase.app1')

@section('content')
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #0a1017;
            color: #e1e1e1;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ asset('images/hospital.jpeg') }}');
            background-size: cover;
            background-position: center;
            filter: brightness(0.2) contrast(1.2) saturate(0.8);
            z-index: -2;
        }

        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 30% 30%, rgba(255, 187, 0, 0.1), transparent),
            linear-gradient(to bottom right, rgba(255, 152, 0, 0.05), rgba(0, 0, 0, 0.9));
            z-index: -1;
        }

        /* Grid Lines Background */
        .grid-lines {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .grid-lines::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: linear-gradient(to right, rgba(255, 187, 0, 0.05) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(255, 187, 0, 0.05) 1px, transparent 1px);
            background-size: 30px 30px;
        }

        /* Login Styles */
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: rgba(16, 25, 36, 0.75);
            border-radius: 16px;
            border: 1px solid rgba(255, 187, 0, 0.2);
            padding: 40px;
            width: 100%;
            max-width: 450px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(to right, #ff9800, #ffbb00);
        }

        .login-body {
            position: relative;
            z-index: 1;
        }

        .login-card h4 {
            font-family: 'Orbitron', sans-serif;
            color: #fff;
            font-size: 1.8rem;
            margin-bottom: 25px;
            letter-spacing: 1px;
            text-transform: uppercase;
            text-align: center;
            background: linear-gradient(to right, #ffbb00, #ff9800);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0 0 10px rgba(255, 187, 0, 0.3);
        }

        /* Form Styles */
        .form-label {
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }

        .form-control {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 187, 0, 0.2);
            color: #fff;
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s;
        }

        .form-control:focus {
            background: rgba(0, 0, 0, 0.3);
            border-color: rgba(255, 187, 0, 0.5);
            box-shadow: 0 0 0 3px rgba(255, 187, 0, 0.2);
            color: #fff;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .form-group {
            margin-bottom: 25px;
        }

        .input-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 15px;
            color: rgba(255, 187, 0, 0.7);
            z-index: 10;
        }

        .position-relative .form-control {
            padding-left: 45px;
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper .form-control {
            padding-left: 45px;
            padding-right: 45px;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            right: 15px;
            color: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: all 0.3s;
            z-index: 15;
        }

        .toggle-password:hover {
            color: rgba(255, 187, 0, 0.8);
        }

        /* Button Styles */
        .btn-cyber-login {
            background: linear-gradient(45deg, #ff9800, #ffbb00);
            border: none;
            color: white;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            width: 100%;
            font-family: 'Orbitron', sans-serif;
            cursor: pointer;
        }

        .btn-cyber-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, #ffbb00, #ff9800);
            transition: all 0.4s;
            z-index: -1;
        }

        .btn-cyber-login:hover::before {
            left: 0;
        }

        .btn-cyber-login:hover {
            box-shadow: 0 0 20px rgba(255, 187, 0, 0.5);
            transform: translateY(-2px);
        }

        /* Alert Styles */
        .alert {
            border-radius: 8px;
            background: rgba(0, 0, 0, 0.3);
            border: none;
            position: relative;
            padding: 15px;
            margin-bottom: 25px;
        }

        .alert-success {
            border-left: 4px solid #00c853;
            color: #b9f6ca;
        }

        .alert-danger {
            border-left: 4px solid #ff1744;
            color: #ff8a80;
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-in {
            animation: fadeIn 0.5s ease forwards;
        }

        /* Medical symbol decoration */
        .medical-symbol {
            position: absolute;
            bottom: -40px;
            right: -40px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: radial-gradient(circle at center, rgba(255, 187, 0, 0.1) 0%, transparent 70%);
            z-index: 0;
        }

        .medical-symbol::before {
            content: '⚕';
            position: absolute;
            top: 25px;
            left: 25px;
            font-size: 40px;
            color: rgba(255, 187, 0, 0.1);
            transform: rotate(-15deg);
        }
    </style>

    <!-- Grid Lines Background -->
    <div class="grid-lines"></div>

    <div class="login-container">
        <div class="login-card animate-in">
            <div class="login-body">
                <h4>Pharmacist Login</h4>

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
                <form method="POST" action="{{ route('firebase.pharmacist.loginpharmacist.submit') }}">
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

    <!-- Add Font Awesome and Google Fonts if not already included -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>

    <!-- Password visibility toggle script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.querySelector('.toggle-password');
            const passwordInput = document.querySelector('#password');

            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                });
            }
        });
    </script>
@endsection
