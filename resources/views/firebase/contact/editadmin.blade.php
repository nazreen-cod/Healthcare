@extends('firebase.app1')

@section('content')
    <style>
        :root {
            --neon-glow: 0 0 10px rgba(255, 187, 0, 0.5), 0 0 20px rgba(255, 187, 0, 0.3), 0 0 30px rgba(255, 187, 0, 0.1);
            --accent-color: #ffbb00;
            --accent-dark: #ff9800;
            --grid-lines: rgba(255, 255, 255, 0.05);
        }

        .container {
            padding: 2rem 0;
        }

        /* Futuristic background */
        .tech-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                linear-gradient(90deg, rgba(15, 23, 42, 0.7) 1px, transparent 1px) 0 0 / 40px 40px,
                linear-gradient(0deg, rgba(15, 23, 42, 0.7) 1px, transparent 1px) 0 0 / 40px 40px;
            z-index: -1;
            pointer-events: none;
            opacity: 0.4;
        }

        /* Card styles */
        .glass {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            border: 1px solid rgba(255, 187, 0, 0.15);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            position: relative;
            transform: translateY(20px);
            opacity: 0;
            animation: cardEntry 0.8s cubic-bezier(0.2, 1, 0.3, 1) forwards;
        }

        @keyframes cardEntry {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .glass::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, transparent, var(--accent-color), transparent);
            z-index: 2;
        }

        .card-header {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.9), rgba(15, 23, 42, 0.7));
            color: #fff;
            border-bottom: 1px solid rgba(255, 187, 0, 0.2);
            border-radius: 16px 16px 0 0;
            padding: 1.5rem;
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg,
            transparent,
            rgba(255, 187, 0, 0.7),
            transparent
            );
        }

        .card-header h4 {
            position: relative;
            display: inline-block;
            font-family: 'Orbitron', sans-serif;
            letter-spacing: 1px;
            margin: 0;
            color: var(--accent-color);
            text-transform: uppercase;
        }

        .card-header h4::before {
            content: '';
            position: absolute;
            left: -20px;
            top: 50%;
            transform: translateY(-50%);
            width: 12px;
            height: 12px;
            background: var(--accent-color);
            border-radius: 50%;
            box-shadow: var(--neon-glow);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 0.6;
                transform: translateY(-50%) scale(0.8);
            }
            50% {
                opacity: 1;
                transform: translateY(-50%) scale(1);
            }
            100% {
                opacity: 0.6;
                transform: translateY(-50%) scale(0.8);
            }
        }

        .card-body {
            padding: 2rem;
            position: relative;
        }

        /* Form elements */
        .form-label {
            color: var(--accent-color);
            font-family: 'Orbitron', sans-serif;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .form-label i {
            margin-right: 8px;
            font-size: 1rem;
        }

        .form-control {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(255, 187, 0, 0.2);
            color: #e2e8f0;
            border-radius: 8px;
            padding: 0.8rem 1rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            background: rgba(15, 23, 42, 0.7);
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(255, 187, 0, 0.15);
            color: #fff;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, #1e40af, #0c4a6e);
            border: 1px solid rgba(255, 187, 0, 0.3);
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            z-index: 1;
            font-family: 'Orbitron', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
            padding: 0.6rem 1.5rem;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
            z-index: -1;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(13, 110, 253, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #991b1b, #7f1d1d);
            border: 1px solid rgba(255, 187, 0, 0.3);
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            font-family: 'Orbitron', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.8rem;
        }

        .btn-danger::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }

        .btn-danger:hover::before {
            left: 100%;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(220, 53, 69, 0.4);
        }

        /* Password container */
        .password-container {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--accent-color);
            cursor: pointer;
            transition: all 0.2s;
            z-index: 5;
        }

        .password-toggle:hover {
            color: var(--accent-dark);
        }

        /* Circuit decoration */
        .circuit-decoration {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 150px;
            height: 150px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100' width='100' height='100'%3E%3Cpath d='M20,80 L80,80 L80,20' fill='none' stroke='rgba(255,187,0,0.1)' stroke-width='1'/%3E%3Ccircle cx='80' cy='20' r='5' fill='none' stroke='rgba(255,187,0,0.1)' stroke-width='1'/%3E%3Ccircle cx='20' cy='80' r='5' fill='none' stroke='rgba(255,187,0,0.1)' stroke-width='1'/%3E%3Cpath d='M30,70 L70,70 L70,30' fill='none' stroke='rgba(255,187,0,0.1)' stroke-width='1'/%3E%3Ccircle cx='70' cy='30' r='3' fill='none' stroke='rgba(255,187,0,0.1)' stroke-width='1'/%3E%3C/svg%3E");
            background-size: cover;
            opacity: 0.5;
            pointer-events: none;
            z-index: 0;
        }

        /* Scanner effect */
        .scan-line {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(to right, transparent, var(--accent-color), transparent);
            opacity: 0.5;
            z-index: 100;
            animation: scanline 8s linear infinite;
            pointer-events: none;
        }

        @keyframes scanline {
            0% {
                top: 0%;
            }
            100% {
                top: 100%;
            }
        }

        /* Admin info box */
        .admin-info {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(255, 187, 0, 0.2);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 2rem;
            color: rgba(255, 255, 255, 0.8);
        }

        .admin-info-title {
            color: var(--accent-color);
            font-size: 0.9rem;
            font-family: 'Orbitron', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .admin-info-title i {
            margin-right: 8px;
        }

        .admin-info-code {
            font-family: 'Courier New', monospace;
            background: rgba(0, 0, 0, 0.3);
            padding: 0.3rem 0.5rem;
            border-radius: 4px;
            font-size: 0.9rem;
            color: var(--accent-color);
            border-left: 3px solid var(--accent-color);
        }

        /* Security note */
        .security-note {
            margin-top: 2rem;
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.5);
            display: flex;
            align-items: center;
        }

        .security-note i {
            color: var(--accent-color);
            margin-right: 8px;
        }
    </style>

    <div class="tech-bg"></div>
    <div class="scan-line"></div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <!-- Glassmorphism Card for Edit Admin -->
                <div class="glass">
                    <div class="card-header">
                        <h4><i class="fas fa-user-shield me-2"></i>Modify Access Credentials</h4>
                        <a href="{{ url('addadmin') }}" class="btn btn-sm btn-danger">
                            <i class="fas fa-arrow-left me-1"></i> RETURN
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="circuit-decoration"></div>

                        <div class="admin-info">
                            <div class="admin-info-title">
                                <i class="fas fa-fingerprint"></i> Administrator ID
                            </div>
                            <div class="admin-info-code">{{ $key }}</div>
                        </div>

                        <form action="{{ url('update-admin/'.$key) }}" method="post">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label for="fullname" class="form-label">
                                    <i class="fas fa-id-card"></i> Identity Verification
                                </label>
                                <input type="text" class="form-control" id="fullname" name="fullname" value="{{ $editdata['fname'] }}">
                            </div>

                            <div class="mb-4">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope"></i> Communication Protocol
                                </label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ $editdata['email'] }}">
                            </div>

                            <div class="mb-4">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone-alt"></i> Emergency Contact
                                </label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="{{ $editdata['phone'] }}">
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="fas fa-key"></i> Access Encryption Key
                                </label>
                                <div class="password-container">
                                    <input type="password" class="form-control" id="password" name="password" value="{{ $editdata['password'] }}">
                                    <button type="button" class="password-toggle" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <div class="security-note">
                                    <i class="fas fa-shield-alt"></i> Last modified: {{ now()->format('Y-m-d H:i:s') }}
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-sync-alt me-2"></i> UPDATE CREDENTIALS
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password visibility toggle
            const togglePassword = document.getElementById('togglePassword');
            const password = document.getElementById('password');

            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';

                // Add tech glitch effect on toggle
                password.style.transition = 'all 0.2s';
                password.style.color = 'var(--accent-color)';

                setTimeout(() => {
                    password.style.color = '';
                }, 300);
            });

            // Animate form fields on load
            const formControls = document.querySelectorAll('.form-control');
            formControls.forEach((control, index) => {
                control.style.opacity = '0';
                control.style.transform = 'translateY(10px)';

                setTimeout(() => {
                    control.style.transition = 'all 0.3s ease';
                    control.style.opacity = '1';
                    control.style.transform = 'translateY(0)';
                }, 100 + (index * 100));
            });

            // Scan line pulse effect
            setInterval(() => {
                const scanLine = document.querySelector('.scan-line');
                scanLine.style.opacity = '0.7';

                setTimeout(() => {
                    scanLine.style.opacity = '0.3';
                }, 500);
            }, 8000);
        });
    </script>
@endsection
