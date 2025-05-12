
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Smart Medical Health - Doctor Portal">
    <meta name="author" content="Smart Medical Health Team">
    <meta name="theme-color" content="#0c1222">

    <title>@yield('title', 'Smart Medical Health - Doctor Portal')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Inline CSS for Futuristic Theme -->
    <style>
        :root {
            --primary: #00c3ff;
            --secondary: #0077ff;
            --accent: #7000ff;
            --success: #00ff9d;
            --warning: #ffbb00;
            --danger: #ff3a3a;
            --dark: #0c1222;
            --light: #e1f5fe;
            --sidebar-width: 280px;
            --header-height: 60px;
        }

        /* Base Styles */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--dark);
            background-image:
                linear-gradient(to bottom, rgba(12, 18, 34, 0.9), rgba(12, 18, 34, 0.95)),
                url('{{ asset('images/hospital.jpeg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: #f5f5f5;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            min-height: 100vh;
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
                linear-gradient(rgba(0, 119, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 119, 255, 0.03) 1px, transparent 1px);
            background-size: 20px 20px;
            z-index: -1;
            pointer-events: none;
        }

        /* Typography */
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Orbitron', sans-serif;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        a {
            color: var(--light);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        a:hover {
            color: var(--primary);
            text-decoration: none;
        }

        /* Futuristic Sidebar */
        .sidebar {
            background: rgba(16, 25, 36, 0.85);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            color: white;
            width: var(--sidebar-width);
            height: 100vh;
            padding: 0;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            transition: all 0.4s cubic-bezier(0, 0, 0.2, 1);
            border-right: 1px solid rgba(0, 195, 255, 0.2);
            box-shadow: 5px 0 25px rgba(0, 0, 0, 0.3);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--primary) rgba(16, 25, 36, 0.5);
        }

        .sidebar::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(16, 25, 36, 0.5);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background-color: var(--primary);
            border-radius: 10px;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            background: linear-gradient(90deg, rgba(16, 25, 36, 0.95), rgba(16, 30, 45, 0.95));
            border-bottom: 1px solid rgba(0, 195, 255, 0.2);
            position: relative;
        }

        .sidebar-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 10%;
            width: 80%;
            height: 1px;
            background: linear-gradient(to right, transparent, var(--primary), transparent);
        }

        .sidebar-header h3 {
            margin: 0;
            color: var(--primary);
            font-size: 1.5rem;
            text-shadow: 0 0 10px rgba(0, 195, 255, 0.5);
        }

        .sidebar .nav-item {
            margin: 5px 15px;
        }

        .sidebar .nav-link {
            color: #e1e1e1;
            padding: 12px 15px;
            border-radius: 8px;
            font-weight: 500;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            position: relative;
            z-index: 1;
            overflow: hidden;
            margin: 5px 0;
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
            color: var(--primary);
        }

        .sidebar .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(0, 119, 255, 0.1), rgba(0, 195, 255, 0.2));
            transition: all 0.4s;
            z-index: -1;
        }

        .sidebar .nav-link:hover::before {
            left: 0;
        }

        .sidebar .nav-link:hover {
            color: var(--primary);
            transform: translateX(3px);
        }

        .sidebar .nav-link.active {
            background: linear-gradient(45deg, rgba(0, 119, 255, 0.15), rgba(0, 195, 255, 0.3));
            color: var(--primary);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            font-weight: 600;
            border-left: 3px solid var(--primary);
        }

        .sidebar .nav-link.active::before {
            left: 0;
        }

        /* Logout Button */
        .sidebar form .btn-link {
            background: transparent;
            border: none;
            width: 100%;
            text-align: left;
            padding: 12px 15px;
            color: #e1e1e1;
            font-weight: 500;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            border-radius: 8px;
            position: relative;
            z-index: 1;
            overflow: hidden;
        }

        .sidebar form .btn-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(255, 58, 58, 0.1), rgba(255, 58, 58, 0.2));
            transition: all 0.4s;
            z-index: -1;
        }

        .sidebar form .btn-link:hover::before {
            left: 0;
        }

        .sidebar form .btn-link:hover {
            color: var(--danger);
            transform: translateX(3px);
        }

        .sidebar form .btn-link i {
            margin-right: 10px;
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
            color: var(--danger);
        }

        /* Main Content Area */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 30px;
            min-height: 100vh;
            transition: margin-left 0.4s cubic-bezier(0, 0, 0.2, 1);
        }

        /* Card styles */
        .card {
            background: rgba(21, 32, 43, 0.8);
            border-radius: 12px;
            overflow: hidden;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(0, 195, 255, 0.2);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            margin-bottom: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
        }

        .card-header {
            background: linear-gradient(90deg, rgba(21, 32, 43, 0.9), rgba(16, 25, 36, 0.9));
            color: var(--primary);
            font-family: 'Orbitron', sans-serif;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(0, 195, 255, 0.2);
            padding: 15px 20px;
        }

        .card-body {
            padding: 20px;
            color: #f0f0f0;
        }

        /* Button styles */
        .btn-cyber {
            background: linear-gradient(45deg, var(--secondary), var(--primary));
            border: none;
            color: white;
            border-radius: 6px;
            padding: 10px 20px;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
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
            background: linear-gradient(45deg, var(--primary), var(--accent));
            transition: all 0.4s;
            z-index: -1;
        }

        .btn-cyber:hover::before {
            left: 0;
        }

        .btn-cyber:hover {
            box-shadow: 0 0 15px rgba(0, 195, 255, 0.5);
            transform: translateY(-2px);
            color: white;
        }

        /* Footer */
        .footer {
            margin-left: var(--sidebar-width);
            background: rgba(21, 32, 43, 0.8);
            padding: 15px 0;
            text-align: center;
            border-top: 1px solid rgba(0, 195, 255, 0.2);
            transition: margin-left 0.4s cubic-bezier(0, 0, 0.2, 1);
        }

        /* Hamburger Menu for Mobile */
        .menu-toggle {
            position: fixed;
            top: 15px;
            left: 15px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(16, 25, 36, 0.8);
            border: 1px solid rgba(0, 195, 255, 0.3);
            color: var(--primary);
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1000;
            box-shadow: 0 0 10px rgba(0, 195, 255, 0.3);
        }

        /* Responsive Sidebar */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content, .footer {
                margin-left: 0;
            }

            .menu-toggle {
                display: flex;
            }

            .sidebar.active {
                transform: translateX(0);
            }
        }

        /* Custom scrollbar for the main content */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.2);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--secondary);
        }

        /* Alert styles */
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

        /* User profile in sidebar */
        .user-profile {
            display: flex;
            align-items: center;
            padding: 15px;
            margin: 10px 15px 20px;
            background: rgba(16, 25, 36, 0.5);
            border-radius: 10px;
            border: 1px solid rgba(0, 195, 255, 0.15);
        }

        .user-profile .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
            color: white;
            font-size: 16px;
        }

        .user-profile .user-info {
            flex: 1;
            overflow: hidden;
        }

        .user-profile .user-name {
            font-weight: 600;
            color: white;
            margin: 0;
            font-size: 0.9rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-profile .user-role {
            color: var(--primary);
            font-size: 0.8rem;
            margin: 0;
        }

        /* Section Title */
        .section-title {
            position: relative;
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: var(--primary);
            font-weight: 600;
            letter-spacing: 1px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: linear-gradient(to right, var(--primary), var(--accent));
            border-radius: 3px;
        }
    </style>
</head>
<body>
<!-- Mobile Menu Toggle Button -->
<div class="menu-toggle" id="menu-toggle">
    <i class="fas fa-bars"></i>
</div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h3><i class="fas fa-user-md me-2"></i>Doctor Portal</h3>
    </div>

    @if(session()->has('doctor_fullname'))
        <div class="user-profile">
            <div class="avatar">
                <i class="fas fa-user-md"></i>
            </div>
            <div class="user-info">
                <p class="user-name">{{ session('doctor_fullname') }}</p>
                <p class="user-role">Medical Practitioner</p>
            </div>
        </div>
    @endif

    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link {{ Request::routeIs('firebase.doctor.dashboard') ? 'active' : '' }}" href="{{ route('firebase.doctor.dashboard') }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ Request::routeIs('firebase.doctor.search') ? 'active' : '' }}" href="{{ route('firebase.doctor.search') }}">
                <i class="fas fa-search"></i> Search Patient
            </a>
        </li>

        <li class="nav-item">
            <form action="{{ route('firebase.doctor.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-link">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </li>
    </ul>
</div>

<!-- Main Content -->
<div class="main-content">
    @yield('content') <!-- Dynamic content -->
</div>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <span class="text-primary">&copy; {{ date('Y') }} Smart Medical Health</span>
    </div>
</footer>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile Menu Toggle
        const menuToggle = document.getElementById('menu-toggle');
        const sidebar = document.getElementById('sidebar');

        if (menuToggle && sidebar) {
            menuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');

                // Change icon based on sidebar state
                const icon = menuToggle.querySelector('i');
                if (sidebar.classList.contains('active')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickOnMenuToggle = menuToggle.contains(event.target);

            if (!isClickInsideSidebar && !isClickOnMenuToggle && window.innerWidth <= 992) {
                sidebar.classList.remove('active');

                const icon = menuToggle.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });

        // Add some interactive elements to the sidebar links
        const navLinks = document.querySelectorAll('.sidebar .nav-link');

        navLinks.forEach(link => {
            link.addEventListener('mouseenter', function() {
                this.style.transform = 'translateX(5px)';
            });

            link.addEventListener('mouseleave', function() {
                if (!this.classList.contains('active')) {
                    this.style.transform = 'translateX(0)';
                }
            });
        });
    });
</script>
</body>
</html>
