@extends('firebase.app1')

@section('content')
    <!-- Add custom styles for futuristic theme -->
    <style>

        .email-display, .phone-display {
            color: #000000;
            background-color: rgba(255, 255, 255, 0.85);
            padding: 6px 10px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            font-weight: 500;
            border: 1px solid rgba(255, 187, 0, 0.3);
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            text-decoration: none !important;
        }

        /* Fix the spacing between icon and text */
        .email-display i, .phone-display i {
            color: var(--accent-color) !important;
            margin-right: 8px; /* Increase the margin for more space */
            font-size: 0.9rem; /* Standardize icon size */
            display: flex; /* Ensure proper alignment */
            align-items: center;
        }

        /* Make sure text is black and properly spaced */
        .email-display span, .phone-display span {
            color: #000000 !important;
            line-height: 1.2;
        }
        :root {
            --neon-glow: 0 0 10px rgba(255, 187, 0, 0.5), 0 0 20px rgba(255, 187, 0, 0.3), 0 0 30px rgba(255, 187, 0, 0.1);
            --accent-color: #ffbb00;
            --accent-dark: #ff9800;
            --grid-lines: rgba(255, 255, 255, 0.05);
        }

        .glass {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            border: 1px solid rgba(255, 187, 0, 0.15);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .glass::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                linear-gradient(90deg, var(--grid-lines) 1px, transparent 1px) 0 0 / 20px 20px,
                linear-gradient(0deg, var(--grid-lines) 1px, transparent 1px) 0 0 / 20px 20px;
            z-index: -1;
        }

        .card-header {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.9), rgba(15, 23, 42, 0.7));
            color: #fff;
            border-bottom: 1px solid rgba(255, 187, 0, 0.2);
            border-radius: 16px 16px 0 0;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
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
        }

        .card-header h4::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg,
            transparent,
            var(--accent-color),
            transparent
            );
        }

        .btn-primary {
            background: linear-gradient(135deg, #1e40af, #0c4a6e);
            border: 1px solid rgba(255, 187, 0, 0.3);
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            z-index: 1;
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

        .btn-success {
            background: linear-gradient(135deg, #065f46, #064e3b);
            border: 1px solid rgba(255, 187, 0, 0.3);
            box-shadow: 0 4px 15px rgba(25, 135, 84, 0.3);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .btn-success::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }

        .btn-success:hover::before {
            left: 100%;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(25, 135, 84, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #991b1b, #7f1d1d);
            border: 1px solid rgba(255, 187, 0, 0.3);
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
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

        .table {
            color: #e2e8f0;
            border-color: rgba(255, 255, 255, 0.05);
        }

        .table thead th {
            background: rgba(15, 23, 42, 0.7);
            color: var(--accent-color);
            font-family: 'Orbitron', sans-serif;
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-color: rgba(255, 187, 0, 0.1);
            padding: 1rem 0.75rem;
            position: relative;
        }

        .table thead th::after {
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

        .table tbody td {
            border-color: rgba(255, 187, 0, 0.05);
            font-size: 0.9rem;
            padding: 0.75rem;
            vertical-align: middle;
            transition: all 0.3s;
        }

        .table tbody tr {
            transition: all 0.3s;
            position: relative;
        }

        .table tbody tr:hover {
            background: rgba(255, 187, 0, 0.03);
        }

        .table tbody tr:hover td {
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
        }

        .alert {
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .alert::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 4px;
        }

        .alert-success {
            background: rgba(6, 95, 70, 0.2);
            color: #d1e7dd;
        }

        .alert-success::before {
            background: linear-gradient(to bottom, #10b981, #059669);
        }

        .alert-danger {
            background: rgba(153, 27, 27, 0.2);
            color: #f8d7da;
        }

        .alert-danger::before {
            background: linear-gradient(to bottom, #ef4444, #dc2626);
        }

        /* Password security */
        .password-cell {
            position: relative;
        }

        .password-mask {
            filter: blur(4px);
            transition: all 0.3s;
            letter-spacing: 2px;
        }

        .password-actions {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0;
            transition: all 0.3s;
            display: flex;
        }

        .password-cell:hover .password-actions {
            opacity: 1;
        }

        .btn-reveal {
            color: var(--accent-color);
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(255, 187, 0, 0.2);
            border-radius: 4px;
            cursor: pointer;
            padding: 0.25rem 0.5rem;
            margin-right: 5px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-reveal:hover {
            color: var(--accent-dark);
            box-shadow: 0 0 10px rgba(255, 187, 0, 0.3);
            transform: translateY(-1px);
        }

        /* Avatar styles */
        .avatar {
            background: linear-gradient(135deg, var(--accent-color), var(--accent-dark));
            color: #212529;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 12px;
            position: relative;
            border: 1px solid rgba(255, 187, 0, 0.3);
            box-shadow: var(--neon-glow);
        }

        /* Empty state styling */
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: rgba(255, 255, 255, 0.5);
            background: rgba(15, 23, 42, 0.2);
            border-radius: 12px;
            border: 1px dashed rgba(255, 187, 0, 0.2);
            position: relative;
            overflow: hidden;
        }

        .empty-state::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                linear-gradient(90deg, var(--grid-lines) 1px, transparent 1px) 0 0 / 30px 30px,
                linear-gradient(0deg, var(--grid-lines) 1px, transparent 1px) 0 0 / 30px 30px;
            z-index: -1;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: rgba(255, 187, 0, 0.3);
            text-shadow: var(--neon-glow);
        }

        /* Futuristic scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.5);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, var(--accent-color), var(--accent-dark));
            border-radius: 4px;
        }

        /* Futuristic tooltip */
        .custom-tooltip {
            position: fixed;
            background: rgba(15, 23, 42, 0.9);
            color: var(--accent-color);
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 0.8rem;
            z-index: 9999;
            border: 1px solid rgba(255, 187, 0, 0.3);
            box-shadow: var(--neon-glow);
            backdrop-filter: blur(4px);
            letter-spacing: 1px;
            font-family: 'Orbitron', sans-serif;
            text-transform: uppercase;
        }

        /* Scan line effect */
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

        /* Action buttons hover effect */
        .btn i {
            margin-right: 5px;
            transition: transform 0.3s;
        }

        .btn:hover i {
            transform: scale(1.2);
        }
    </style>

    <div class="scan-line"></div>

    <div class="container py-4">
        <div class="row">
            <div class="col-md-12">
                @if (session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Futuristic Card for Admin List -->
                <div class="card glass">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>
                            <i class="fas fa-user-shield me-2"></i>
                            <span class="text-uppercase">System Administrators</span>
                        </h4>
                        <a href="{{ url('createadmin') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus-circle"></i> New Admin
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th width="5%">ID</th>
                                    <th width="20%">FULLNAME</th>
                                    <th width="20%">PASSWORD</th>
                                    <th width="20%">EMAIL</th>
                                    <th width="15%">PHONE NUMBER</th>
                                    <th width="10%">EDIT</th>
                                    <th width="10%">DELETE</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php $i=1; @endphp
                                @forelse($admin as $key => $item)
                                    <tr>
                                        <td>{{$i++}}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar me-2">
                                                    {{ substr($item["fname"], 0, 1) }}
                                                </div>
                                                <span>{{$item["fname"]}}</span>
                                            </div>
                                        </td>
                                        <td class="password-cell">
                                            <span class="password-mask">••••••••••••</span>
                                            <div class="password-actions">
                                                <button class="btn-reveal" onclick="togglePassword(this, '{{$item["password"]}}')">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn-reveal" onclick="copyToClipboard('{{$item["password"]}}')">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="mailto:{{$item["email"]}}" class="text-decoration-none text-light">
                                                <i class="fas fa-envelope me-1" style="color: var(--accent-color);"></i>
                                                {{$item["email"]}}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="tel:{{$item["phone"]}}" class="text-decoration-none text-light">
                                                <i class="fas fa-phone-alt me-1" style="color: var(--accent-color);"></i>
                                                {{$item["phone"]}}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{url("edit-admin/".$key)}}" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Edit Admin">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{url("delete-admin/".$key)}}" class="btn btn-sm btn-danger" onclick="return confirm('WARNING: You are about to delete admin access. Are you sure you want to proceed?')" data-bs-toggle="tooltip" title="Delete Admin">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="empty-state">
                                                <i class="fas fa-user-slash"></i>
                                                <p>No administrators found in the system</p>
                                                <p class="small text-muted mb-3">Create your first admin user to begin system management</p>
                                                <a href="{{ url('createadmin') }}" class="btn btn-sm btn-primary mt-2">
                                                    <i class="fas fa-plus-circle me-1"></i> Initialize Admin System
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to toggle password visibility
        function togglePassword(button, password) {
            const passwordCell = button.closest('.password-cell');
            const passwordSpan = passwordCell.querySelector('.password-mask');

            if (passwordSpan.classList.contains('password-mask')) {
                // Add cyberpunk-style reveal animation
                passwordSpan.style.transition = 'all 0.2s';
                passwordSpan.style.opacity = '0';

                setTimeout(() => {
                    passwordSpan.textContent = password;
                    passwordSpan.classList.remove('password-mask');
                    passwordSpan.style.opacity = '1';
                    passwordSpan.style.color = 'var(--accent-color)';
                    button.innerHTML = '<i class="fas fa-eye-slash"></i>';
                }, 200);

                // Auto-hide after 5 seconds with countdown
                let timeLeft = 5;
                const countdownInterval = setInterval(() => {
                    timeLeft--;
                    if (timeLeft <= 0) {
                        clearInterval(countdownInterval);

                        // Hide with animation
                        passwordSpan.style.opacity = '0';

                        setTimeout(() => {
                            passwordSpan.textContent = '••••••••••••';
                            passwordSpan.classList.add('password-mask');
                            passwordSpan.style.opacity = '1';
                            passwordSpan.style.color = '';
                            button.innerHTML = '<i class="fas fa-eye"></i>';
                        }, 200);
                    }
                }, 1000);
            } else {
                passwordSpan.style.opacity = '0';

                setTimeout(() => {
                    passwordSpan.textContent = '••••••••••••';
                    passwordSpan.classList.add('password-mask');
                    passwordSpan.style.opacity = '1';
                    passwordSpan.style.color = '';
                    button.innerHTML = '<i class="fas fa-eye"></i>';
                }, 200);
            }
        }

        // Function to copy password to clipboard with futuristic tooltip
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                // Show a cyberpunk tooltip
                const tooltip = document.createElement('div');
                tooltip.textContent = 'SECURED COPY COMPLETE';
                tooltip.className = 'custom-tooltip';

                // Position near mouse
                tooltip.style.left = (event.clientX + 10) + 'px';
                tooltip.style.top = (event.clientY + 10) + 'px';

                // Add to DOM with a scale-in animation
                tooltip.style.transform = 'scale(0.8)';
                tooltip.style.opacity = '0';
                document.body.appendChild(tooltip);

                // Trigger animation
                setTimeout(() => {
                    tooltip.style.transition = 'all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
                    tooltip.style.transform = 'scale(1)';
                    tooltip.style.opacity = '1';
                }, 10);

                // Add typing effect
                const audio = new Audio('data:audio/mp3;base64,SUQzBAAAAAAAI1RTU0UAAAAPAAADTGF2ZjU4Ljc2LjEwMAAAAAAAAAAAAAAA//tQwAAAAAAAAAAAAAAAAAAAAAAASW5mbwAAAA8AAAASAAAeMwAUFBQUFCgUFBQUFBQ8PDw8PDxQPDw8PDw8ZGRkZGRkfGRkZGRkZJiYmJiYmKyYmJiYmJjExMTExMTYxMTExMTE8PDw8PDw8PDw8PDw8P//////////////////////////AAAAAExhdmYAAAAAAAAAAAAAAAAAAAAAACQAAAAAAAAAADxu4EKnAAAAAAAAAAAAAAAAAAAA//tAwAAABLBTxx0EABicQc/cwgAlEFF21zDPBYG4g++TCiQQTJJJ/QSbf/nf//7vv/7BJJOAYLg+D4JBMEwfBIEB8HwQDLux0ACt324pWFf/+7IAYIAIAGfMG4UQAQmZCZN0ohAg4bMwr7hH5DJmJz3XikCCQaIIZHBgGH/8TcQczMzEDUERMGBQILoEQIFAUKnIzGIyGMB4OhUGyHAB7iTSSSSSSA+Z/8jvlbP/93VN//qQHgP/9AZDf/8gMR//0BqNGQGhkkkkkkkkDAYDO+YxLIbUGLkZiILI0akk6R07kjr//vcZziQX/+qLG5dz//1S7kFkigXL//XTCvg+CQTBWlA65IgFQ+D4PhkLyPg+CYTBMHwfB8EgmCYJgmD4Pg+D4Pg+CAZEguD4Pg+D4Ph8ODg+HwfBIPh8Y0EBA//+D4I//B8HwQHB8EBwfB8Hw+D4Pg+D4UBAQEBAQEBAQEBAQEBAQEBABAQEBAQEBAQEBAQEBAQEBAQEBH/+xA8P/ANBudGjdGjdAaPToOjoNORG9NyKe+b9F5gPgeDLrwfDwZv64Ph+CBePCLk8Pimn/64JBwfBAPh8EA8EA8HwfB8HwfBIPimQPgFQ//+g6j30v/t1XoNxVoHC+YAAAEzPWTEFNRTMuMTAwVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVf/7UMTWgAhIkQ54MQBT0ZHjz5jVdVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVV');
                audio.volume = 0.2;
                audio.play();

                // Remove after 2 seconds with fade out
                setTimeout(() => {
                    tooltip.style.transform = 'scale(1.1)';
                    tooltip.style.opacity = '0';

                    setTimeout(() => {
                        document.body.removeChild(tooltip);
                    }, 300);
                }, 2000);
            });
        }

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            // Find all email and phone cells and update their display
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length >= 5) {
                    // Email cell (4th column, index 3)
                    const emailCell = cells[3];
                    const emailLink = emailCell.querySelector('a');
                    if (emailLink) {
                        const email = emailLink.textContent.trim();
                        emailLink.className = 'email-display';
                        // Add non-breaking space after icon to ensure consistent spacing
                        emailLink.innerHTML = `<i class="fas fa-envelope" style="color: var(--accent-color);"></i>\u00A0<span style="color: #000000;">${email}</span>`;
                    }

                    // Phone cell (5th column, index 4)
                    const phoneCell = cells[4];
                    const phoneLink = phoneCell.querySelector('a');
                    if (phoneLink) {
                        const phone = phoneLink.textContent.trim();
                        phoneLink.className = 'phone-display';
                        // Add non-breaking space after icon to ensure consistent spacing
                        phoneLink.innerHTML = `<i class="fas fa-phone-alt" style="color: var(--accent-color);"></i>\u00A0<span style="color: #000000;">${phone}</span>`;
                    }
                }
            });
            // Add futuristic scan line animation
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
