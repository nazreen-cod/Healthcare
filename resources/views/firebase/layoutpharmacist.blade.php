<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Pharmacist Panel - Smart Medical Health')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Inline CSS for Cyber Medical Theme -->
    <style>
        :root {
            --primary: #ff9800;
            --secondary: #ffbb00;
            --success: #ffcc00;
            --dark: #101924;
            --darker: #0a1017;
            --light-text: #fff8e1;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--darker);
            margin: 0;
            padding: 0;
            color: var(--light-text);
            overflow-x: hidden;
            position: relative;
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

        /* Sidebar Styles */
        .sidebar {
            background: rgba(10, 16, 23, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255, 187, 0, 0.2);
            color: var(--light-text);
            width: 280px;
            min-height: 100vh;
            padding: 25px 0;
            position: fixed;
            z-index: 100;
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 0 25px 25px;
            border-bottom: 1px solid rgba(255, 187, 0, 0.1);
            margin-bottom: 25px;
        }

        .sidebar h3 {
            font-family: 'Orbitron', sans-serif;
            color: var(--success);
            font-size: 1.5rem;
            text-align: center;
            margin-bottom: 5px;
            letter-spacing: 1px;
            text-transform: uppercase;
            text-shadow: 0 0 15px rgba(255, 187, 0, 0.3);
        }

        .sidebar-role {
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 0;
        }

        .sidebar .nav-heading {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 20px 25px 10px;
            color: rgba(255, 255, 255, 0.4);
        }

        .sidebar .nav-link {
            color: var(--light-text);
            padding: 12px 25px;
            margin: 4px 10px;
            border-radius: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            font-size: 1.1rem;
            color: var(--success);
            transition: all 0.3s;
            min-width: 20px;
            text-align: center;
        }

        .sidebar .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, rgba(255, 187, 0, 0.1), rgba(255, 152, 0, 0.05));
            z-index: -1;
            transition: all 0.3s;
            opacity: 0;
        }

        .sidebar .nav-link:hover {
            color: #fff;
        }

        .sidebar .nav-link:hover::before {
            opacity: 1;
        }

        .sidebar .nav-link:hover i {
            transform: translateX(3px);
        }

        .sidebar .nav-link.active {
            color: #fff;
            background: linear-gradient(90deg, rgba(255, 187, 0, 0.15), rgba(255, 152, 0, 0.1));
            border-left: 3px solid var(--success);
        }

        .sidebar .nav-link.active i {
            color: var(--success);
        }

        .logout-btn {
            width: 100%;
            text-align: left;
            display: flex;
            align-items: center;
            background: transparent;
            border: none;
            color: var(--light-text);
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            margin: 4px 10px;
        }

        .logout-btn i {
            margin-right: 10px;
            color: #ff5252;
        }

        .logout-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 82, 82, 0.1);
            opacity: 0;
            transition: all 0.3s;
            z-index: -1;
        }

        .logout-btn:hover {
            color: #ff5252;
        }

        .logout-btn:hover::before {
            opacity: 1;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            padding: 30px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        /* Dashboard Cards */
        .cyber-card {
            background: rgba(16, 25, 36, 0.7);
            border-radius: 12px;
            border: 1px solid rgba(255, 187, 0, 0.2);
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .cyber-card:hover {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
            border-color: rgba(255, 187, 0, 0.4);
        }

        .cyber-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, var(--success), var(--primary));
        }

        /* Header Styles */
        .page-header {
            font-family: 'Orbitron', sans-serif;
            color: var(--success);
            margin-bottom: 1.5rem;
            position: relative;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
            text-shadow: 0 0 15px rgba(255, 187, 0, 0.3);
        }

        .page-header::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 80px;
            height: 3px;
            background: linear-gradient(to right, var(--success), transparent);
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-in {
            animation: fadeIn 0.5s ease forwards;
        }

        /* Tables */
        .cyber-table {
            width: 100%;
            background: rgba(16, 25, 36, 0.7);
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid rgba(255, 187, 0, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }

        .cyber-table th {
            background: rgba(16, 25, 36, 0.9);
            color: var(--success);
            padding: 15px;
            font-family: 'Orbitron', sans-serif;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(255, 187, 0, 0.2);
        }

        .cyber-table td {
            padding: 12px 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: var(--light-text);
        }

        .cyber-table tr:hover td {
            background: rgba(255, 187, 0, 0.05);
        }

        /* Buttons */
        .btn-cyber {
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            border: none;
            color: var(--dark);
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 8px;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 1px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .btn-cyber::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, var(--secondary), var(--primary));
            transition: all 0.4s;
            z-index: -1;
        }

        .btn-cyber:hover::before {
            left: 0;
        }

        .btn-cyber:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        }

        /* Form Controls */
        .form-control {
            background: rgba(16, 25, 36, 0.7);
            border: 1px solid rgba(255, 187, 0, 0.2);
            color: var(--light-text);
            border-radius: 8px;
            padding: 12px 15px;
        }

        .form-control:focus {
            background: rgba(16, 25, 36, 0.9);
            border-color: var(--secondary);
            box-shadow: 0 0 0 0.25rem rgba(255, 187, 0, 0.25);
            color: var(--light-text);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
                padding: 20px 0;
            }

            .sidebar h3, .sidebar-role, .nav-link span, .sidebar-footer {
                display: none;
            }

            .sidebar .nav-link {
                padding: 15px 0;
                justify-content: center;
            }

            .sidebar .nav-link i {
                margin-right: 0;
                font-size: 1.2rem;
            }

            .logout-btn span {
                display: none;
            }

            .logout-btn {
                justify-content: center;
                padding: 15px 0;
            }

            .logout-btn i {
                margin-right: 0;
            }

            .main-content {
                margin-left: 70px;
            }
        }

        @media (max-width: 576px) {
            .main-content {
                padding: 20px 15px;
            }
        }
    </style>

    @yield('styles')
</head>
<body>
<!-- Grid Lines Background -->
<div class="grid-lines"></div>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>Pharmacist</h3>
            <p class="sidebar-role">Pharmacist Panel</p>
        </div>

        <div class="sidebar-nav">
            <p class="nav-heading">Main Navigation</p>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('firebase.pharmacist.dashboard') ? 'active' : '' }}" href="{{ route('firebase.pharmacist.dashboard') }}">
                        <i class="fas fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('firebase.pharmacist.search') ? 'active' : '' }}" href="{{ route('firebase.pharmacist.search') }}">
                        <i class="fas fa-search"></i>
                        <span>Search Patient</span>
                    </a>
            </ul>
        </div>

        <div class="mt-auto">
            <p class="nav-heading">Account</p>
            <ul class="nav flex-column">

                <li class="nav-item">
                    <form action="{{ route('firebase.pharmacist.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        @yield('content') <!-- Dynamic content -->
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Optional: Custom JS for animations -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add animation classes to elements
        const elements = document.querySelectorAll('.cyber-card, .page-header');
        elements.forEach(function(element, index) {
            setTimeout(() => {
                element.classList.add('animate-in');
            }, 100 * index);
        });
    });
</script>

@yield('scripts')
</body>
</html>
