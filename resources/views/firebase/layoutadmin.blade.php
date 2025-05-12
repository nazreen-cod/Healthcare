
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
            --primary: #ff4b4b;
            --secondary: #d32f2f;
            --accent: #ff7b7b;
            --dark: #151c24;
            --light: #fff5f5;
            --success: #00ff9d;
            --panel: rgba(16, 25, 36, 0.85);
            --glow: 0 0 10px rgba(255, 75, 75, 0.7);
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
            text-align: center;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(20,0,0,0.7), rgba(40,0,0,0.8));
            z-index: -1;
        }

        .sidebar {
            background: var(--panel);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255, 75, 75, 0.2);
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.3);
            color: white;
            width: 280px;
            min-height: 100vh;
            padding: 25px 15px;
            position: fixed;
            transition: all 0.3s ease;
            z-index: 100;
            text-align: left; /* Keep sidebar items left-aligned */
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
            font-weight: 400;
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
        }

        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: linear-gradient(90deg, rgba(255,75,75,0.15), transparent);
            border-left: 3px solid var(--primary);
            box-shadow: var(--glow);
            color: var(--primary);
        }

        .main-content {
            margin-left: 280px;
            padding: 30px;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .main-content > * {
            max-width: 1200px;
            width: 100%;
        }

        /* Card styling for content areas */
        .card {
            background: rgba(21, 32, 43, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 75, 75, 0.2);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            transition: all 0.3s;
            text-align: center;
            margin: 0 auto;
        }

        .card:hover {
            box-shadow: 0 8px 32px rgba(255, 75, 75, 0.3);
            transform: translateY(-5px);
        }

        .card-header {
            background: linear-gradient(90deg, var(--dark), rgba(16, 25, 36, 0.7));
            border-bottom: 1px solid rgba(255, 75, 75, 0.2);
            color: var(--primary);
            font-family: 'Orbitron', sans-serif;
            font-size: 1.2rem;
            letter-spacing: 1px;
            text-align: center;
        }

        .card-body {
            text-align: center;
        }

        /* Form elements */
        .form-group {
            text-align: left; /* Keep forms left-aligned for readability */
        }

        .form-group label {
            text-align: left;
            display: block;
        }

        /* Table styles */
        .table {
            text-align: center;
        }

        .table th {
            text-align: center;
        }

        /* Custom button styling */
        .btn-cyber {
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            border: none;
            color: white;
            border-radius: 6px;
            position: relative;
            z-index: 1;
            overflow: hidden;
            transition: all 0.3s;
            margin: 0 auto;
            display: inline-block;
        }

        .btn-cyber::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, var(--secondary), var(--accent));
            transition: all 0.4s;
            z-index: -1;
        }

        .btn-cyber:hover::before {
            left: 0;
        }

        /* Section headers */
        h1, h2, h3, h4, h5, h6 {
            text-align: center;
            margin-left: auto;
            margin-right: auto;
        }

        /* Containers and rows */
        .container, .row {
            text-align: center;
        }

        /* Make columns center their content */
        .col, .col-md-6, .col-lg-4, [class^="col-"] {
            display: flex;
            flex-direction: column;
            align-items: center;
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

        /* Center icons */
        .icon-container {
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
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
        <h3>Admin Panel</h3>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('firebase.admin.dashboard') ? 'active' : '' }}" href="{{ route('firebase.admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt pulse"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('firebase.admin.reg_doctor') ? 'active' : '' }}" href="{{ route('firebase.admin.reg_doctor') }}">
                    <i class="fas fa-user-md"></i>
                    <span>Register Doctor</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('firebase.admin.reg_nurse') ? 'active' : '' }}" href="{{ route('firebase.admin.reg_nurse') }}">
                    <i class="fas fa-user-nurse"></i>
                    <span>Register Nurse</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('firebase.admin.reg_pharmacist') ? 'active' : '' }}" href="{{ route('firebase.admin.reg_pharmacist') }}">
                    <i class="fas fa-prescription-bottle-alt"></i>
                    <span>Register Pharmacist</span>
                </a>
            </li>

            <li class="nav-item mt-5">
                <form action="{{ route('firebase.admin.logout') }}" method="POST" class="text-center">
                    @csrf
                    <button type="submit" class="btn-cyber nav-link w-100 text-center">
                        <i class="fas fa-power-off"></i>
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
    // Add some interactive elements for the futuristic feel
    document.addEventListener('DOMContentLoaded', function() {
        // Add hover effect to cards
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.boxShadow = '0 8px 32px rgba(255, 75, 75, 0.4)';
            });
            card.addEventListener('mouseleave', function() {
                this.style.boxShadow = '0 8px 32px rgba(0, 0, 0, 0.3)';
            });
        });

        // Center content elements
        const contentElements = document.querySelectorAll('.main-content > div');
        contentElements.forEach(element => {
            if (!element.classList.contains('container') && !element.classList.contains('row')) {
                element.style.marginLeft = 'auto';
                element.style.marginRight = 'auto';
                element.style.textAlign = 'center';
            }
        });
    });
</script>
</body>
</html>
