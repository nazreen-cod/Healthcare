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

        .card-header h4::after {
            content: 'v1.0';
            position: absolute;
            top: -8px;
            right: -30px;
            font-size: 0.6rem;
            color: var(--accent-color);
            opacity: 0.7;
        }

        .card-body {
            padding: 2rem;
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

        /* Input group with icons */
        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--accent-color);
            z-index: 10;
        }

        .input-with-icon {
            padding-left: 40px;
        }

        /* Custom Buttons */
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

        /* Password strength indicator */
        .password-strength {
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            margin-top: 8px;
            border-radius: 2px;
            overflow: hidden;
            display: flex;
        }

        .strength-segment {
            flex: 1;
            height: 100%;
            margin: 0 1px;
            background: transparent;
            transition: all 0.3s;
        }

        .strength-text {
            font-size: 0.8rem;
            margin-top: 5px;
            color: rgba(255, 255, 255, 0.6);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Badge styles */
        .security-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.7rem;
            text-transform: uppercase;
            font-family: 'Orbitron', sans-serif;
            letter-spacing: 1px;
            background: rgba(255, 187, 0, 0.15);
            color: var(--accent-color);
            border: 1px solid rgba(255, 187, 0, 0.2);
        }

        .security-tip {
            background: rgba(15, 23, 42, 0.5);
            border: 1px dashed rgba(255, 187, 0, 0.3);
            border-radius: 8px;
            padding: 12px;
            margin-top: 2rem;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
            position: relative;
        }

        .security-tip i {
            color: var(--accent-color);
            margin-right: 8px;
        }

        /* Animated circuit lines */
        .circuit-lines {
            position: absolute;
            top: 0;
            right: 0;
            width: 150px;
            height: 150px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100' width='100' height='100'%3E%3Cpath d='M30,10 L70,10 L70,30 L90,30 L90,70 L70,70 L70,90 L30,90 L30,70 L10,70 L10,30 L30,30 Z' fill='none' stroke='rgba(255,187,0,0.15)' stroke-width='1'/%3E%3Cpath d='M30,10 L70,10 M70,30 L90,30 M90,70 L70,70 M70,90 L30,90 M30,70 L10,70 M10,30 L30,30' stroke='rgba(255,187,0,0.15)' stroke-width='1'/%3E%3C/svg%3E");
            background-size: cover;
            opacity: 0.5;
            pointer-events: none;
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

        /* Floating particle effect */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .particle {
            position: absolute;
            width: 2px;
            height: 2px;
            background-color: var(--accent-color);
            border-radius: 50%;
            opacity: 0.5;
            animation: float 15s infinite linear;
        }

        @keyframes float {
            0% {
                transform: translateY(0) translateX(0);
                opacity: 0;
            }
            10% {
                opacity: 0.8;
            }
            90% {
                opacity: 0.6;
            }
            100% {
                transform: translateY(-500px) translateX(100px);
                opacity: 0;
            }
        }

        /* Button with icon */
        .btn i {
            margin-right: 8px;
            transition: transform 0.3s;
        }

        .btn:hover i {
            transform: translateX(3px);
        }

        /* Show password toggle */
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

        /* Password container */
        .password-container {
            position: relative;
        }
    </style>

    <div class="tech-bg"></div>
    <div class="scan-line"></div>
    <div class="particles" id="particles"></div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <!-- Glassmorphism effect applied to the card -->
                <div class="card glass">
                    <div class="circuit-lines"></div>

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4><i class="fas fa-user-shield me-2"></i>Initialize Security Clearance</h4>
                        <a href="{{ url('addadmin') }}" class="btn btn-sm btn-danger">
                            <i class="fas fa-arrow-left"></i> RETURN
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="security-badge mb-4">
                            <i class="fas fa-shield-alt me-1"></i> Security Level: Alpha
                        </div>

                        <form action="{{ url('createadmin') }}" method="post">
                            @csrf
                            <div class="mb-4">
                                <label for="fullname" class="form-label">
                                    <i class="fas fa-id-card"></i> Identity Verification
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="fullname" name="fullname"
                                           placeholder="Enter administrator full name" required autocomplete="off">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope"></i> Communication Protocol
                                </label>
                                <div class="input-group">
                                    <input type="email" class="form-control" id="email" name="email"
                                           placeholder="Enter secure email address" required autocomplete="off">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone-alt"></i> Emergency Contact
                                </label>
                                <div class="input-group">
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                           placeholder="Enter communication number" required autocomplete="off">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="fas fa-key"></i> Access Encryption Key
                                </label>
                                <div class="password-container">
                                    <input type="password" class="form-control" id="password" name="password"
                                           placeholder="Create secure password" required autocomplete="off">
                                    <button type="button" class="password-toggle" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>

                                <div class="password-strength">
                                    <div class="strength-segment" id="segment1"></div>
                                    <div class="strength-segment" id="segment2"></div>
                                    <div class="strength-segment" id="segment3"></div>
                                    <div class="strength-segment" id="segment4"></div>
                                </div>

                                <div class="strength-text">
                                    <span id="strengthText">Password strength</span>
                                    <span id="strengthLevel"></span>
                                </div>
                            </div>

                            <div class="mb-4 text-center">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-save"></i> INITIALIZE ACCESS
                                </button>
                            </div>
                        </form>

                        <div class="security-tip">
                            <i class="fas fa-info-circle"></i>
                            <strong>SECURITY PROTOCOL:</strong> All administrators have full system access. Use strong passwords and protect access credentials.
                        </div>
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

                // Add futuristic glitch effect
                password.classList.add('glitch-effect');
                setTimeout(() => {
                    password.classList.remove('glitch-effect');
                }, 500);
            });

            // Password strength meter
            const strengthSegments = [
                document.getElementById('segment1'),
                document.getElementById('segment2'),
                document.getElementById('segment3'),
                document.getElementById('segment4')
            ];
            const strengthText = document.getElementById('strengthText');
            const strengthLevel = document.getElementById('strengthLevel');

            password.addEventListener('input', function() {
                const val = this.value;
                let strength = 0;

                // Check length
                if (val.length > 7) strength++;

                // Check for mixed case
                if (val.match(/[a-z]/) && val.match(/[A-Z]/)) strength++;

                // Check for numbers
                if (val.match(/\d/)) strength++;

                // Check for special characters
                if (val.match(/[^a-zA-Z\d]/)) strength++;

                // Update UI
                strengthSegments.forEach((segment, index) => {
                    if (index < strength) {
                        segment.style.background = getStrengthColor(strength);
                    } else {
                        segment.style.background = 'transparent';
                    }
                });

                // Update text
                if (val.length === 0) {
                    strengthText.textContent = 'Password strength';
                    strengthLevel.textContent = '';
                } else {
                    strengthText.textContent = 'Security level:';

                    if (strength < 2) {
                        strengthLevel.textContent = 'CRITICAL';
                        strengthLevel.style.color = '#dc2626';
                    } else if (strength < 3) {
                        strengthLevel.textContent = 'MODERATE';
                        strengthLevel.style.color = '#d97706';
                    } else if (strength < 4) {
                        strengthLevel.textContent = 'HIGH';
                        strengthLevel.style.color = '#65a30d';
                    } else {
                        strengthLevel.textContent = 'MAXIMUM';
                        strengthLevel.style.color = '#16a34a';
                    }
                }
            });

            function getStrengthColor(strength) {
                switch(strength) {
                    case 1: return '#dc2626'; // red
                    case 2: return '#d97706'; // orange
                    case 3: return '#65a30d'; // light green
                    case 4: return '#16a34a'; // green
                    default: return 'transparent';
                }
            }

            // Create floating particles
            const particlesContainer = document.getElementById('particles');
            for (let i = 0; i < 20; i++) {
                createParticle();
            }

            function createParticle() {
                const particle = document.createElement('div');
                particle.classList.add('particle');

                // Random position
                const posX = Math.random() * window.innerWidth;
                const posY = Math.random() * window.innerHeight + window.innerHeight; // Start below screen

                // Random size
                const size = Math.random() * 3;

                // Random animation duration
                const duration = Math.random() * 15 + 5;

                // Random animation delay
                const delay = Math.random() * 5;

                particle.style.left = `${posX}px`;
                particle.style.top = `${posY}px`;
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                particle.style.animationDuration = `${duration}s`;
                particle.style.animationDelay = `${delay}s`;

                particlesContainer.appendChild(particle);

                // Remove and recreate particle after animation completes
                setTimeout(() => {
                    particle.remove();
                    createParticle();
                }, (duration + delay) * 1000);
            }
        });
    </script>
@endsection
