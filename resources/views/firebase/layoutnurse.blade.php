
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Smart Medical Health')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">

    <!-- Inline CSS for Futuristic Design -->
    <style>
        :root {
            --primary: #00ff9d;
            --secondary: #00cc7d;
            --accent: #7bffbe;
            --dark: #151c24;
            --light: #f0fff7;
            --panel: rgba(16, 25, 36, 0.85);
            --glow: 0 0 10px rgba(0, 255, 157, 0.7);
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-image: url('{{ asset('images/hospital.jpeg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            margin: 0;
            padding: 0;
            color: var(--light);
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(0,20,0,0.7), rgba(0,40,20,0.8));
            z-index: -1;
        }

        /* Digital grid overlay */
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                linear-gradient(rgba(0, 255, 157, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 255, 157, 0.03) 1px, transparent 1px);
            background-size: 20px 20px;
            z-index: -1;
            pointer-events: none;
        }

        .sidebar {
            background: var(--panel);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-right: 1px solid rgba(0, 255, 157, 0.2);
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.3);
            color: white;
            width: 280px;
            min-height: 100vh;
            padding: 25px 15px;
            position: fixed;
            transition: all 0.3s ease;
            z-index: 100;
        }

        .sidebar::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 2px;
            height: 100%;
            background: linear-gradient(to bottom, transparent, var(--primary), transparent);
        }

        .sidebar h3 {
            font-family: 'Orbitron', sans-serif;
            text-align: center;
            margin-bottom: 30px;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 2px;
            position: relative;
            padding-bottom: 10px;
            text-shadow: var(--glow);
        }

        .sidebar h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50%;
            height: 2px;
            background: linear-gradient(to right, transparent, var(--primary), transparent);
        }

        .nav-item {
            margin-bottom: 10px;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.2s;
        }

        .nav-item:hover {
            transform: translateX(5px);
        }

        .sidebar .nav-link {
            color: white;
            font-weight: 500;
            padding: 12px 15px;
            border-left: 3px solid transparent;
            border-radius: 4px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            font-size: 18px;
            color: var(--primary);
        }

        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: linear-gradient(90deg, rgba(0, 255, 157, 0.15), transparent);
            border-left: 3px solid var(--primary);
            box-shadow: var(--glow);
            color: var(--primary);
        }

        .main-content {
            margin-left: 280px;
            padding: 30px;
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        /* Card styling for content areas */
        .card {
            background: rgba(21, 32, 43, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 255, 157, 0.2);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            transition: all 0.3s;
            margin-bottom: 20px;
        }

        .card:hover {
            box-shadow: 0 8px 32px rgba(0, 255, 157, 0.3);
            transform: translateY(-5px);
        }

        .card-header {
            background: linear-gradient(90deg, var(--dark), rgba(16, 25, 36, 0.7));
            border-bottom: 1px solid rgba(0, 255, 157, 0.2);
            color: var(--primary);
            font-family: 'Orbitron', sans-serif;
            font-size: 1.2rem;
            letter-spacing: 1px;
        }

        /* Form elements */
        .form-control {
            background-color: rgba(16, 25, 36, 0.7);
            border: 1px solid rgba(0, 255, 157, 0.2);
            color: white;
            transition: all 0.3s;
        }

        .form-control:focus {
            background-color: rgba(16, 25, 36, 0.9);
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(0, 255, 157, 0.25);
            color: white;
        }

        .form-label {
            color: var(--light);
            font-weight: 500;
        }

        /* Button styling */
        .btn-cyber {
            background: linear-gradient(45deg, var(--secondary), var(--primary));
            border: none;
            color: var(--dark);
            font-weight: 600;
            border-radius: 6px;
            padding: 10px 20px;
            position: relative;
            z-index: 1;
            overflow: hidden;
            transition: all 0.3s;
        }

        .btn-cyber::before {
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

        .btn-cyber:hover::before {
            left: 0;
        }

        .btn-cyber:hover {
            box-shadow: 0 0 15px rgba(0, 255, 157, 0.5);
            transform: translateY(-2px);
            color: var(--dark);
        }

        /* Custom button for logout */
        .btn-logout {
            background: transparent;
            border: 1px solid rgba(0, 255, 157, 0.3);
            color: white;
            width: 100%;
            text-align: left;
            padding: 12px 15px;
            border-radius: 4px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }

        .btn-logout i {
            margin-right: 10px;
            color: var(--primary);
        }

        .btn-logout:hover {
            background: rgba(0, 255, 157, 0.1);
            color: var(--primary);
            transform: translateX(5px);
        }

        /* Animated elements */
        @keyframes pulse {
            0% { opacity: 0.7; }
            50% { opacity: 1; }
            100% { opacity: 0.7; }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        /* Responsive design for mobile */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                padding: 20px 10px;
            }

            .sidebar h3, .sidebar .nav-link span {
                display: none;
            }

            .sidebar .nav-link i {
                margin-right: 0;
                font-size: 22px;
            }

            .main-content {
                margin-left: 70px;
            }
        }
    </style>
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar">
        <h3>Nurse Panel</h3>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('firebase.nurse.dashboard') ? 'active' : '' }}" href="{{ route('firebase.nurse.dashboard') }}">
                    <i class="fas fa-tachometer-alt pulse"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('firebase.nurse.register_patient') ? 'active' : '' }}" href="{{ route('firebase.nurse.register_patient') }}">
                    <i class="fas fa-user-plus"></i>
                    <span>Register Patient</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('firebase.nurse.search') ? 'active' : '' }}" href="{{ route('firebase.nurse.search') }}">
                    <i class="fas fa-search"></i>
                    <span>Search Patient</span>
                </a>
            </li>
            <li class="nav-item mt-auto">
                <form action="{{ route('firebase.nurse.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        @yield('content') <!-- Dynamic content -->
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Add some interactive elements
    document.addEventListener('DOMContentLoaded', function() {
        // Add hover effect to cards
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.boxShadow = '0 8px 32px rgba(0, 255, 157, 0.4)';
            });
            card.addEventListener('mouseleave', function() {
                this.style.boxShadow = '0 8px 32px rgba(0, 0, 0, 0.3)';
            });
        });
    });
</script>
</body>
</html>
