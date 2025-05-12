<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Smart Medical Health - Advanced Healthcare Platform">
    <meta name="author" content="Smart Medical Health Team">
    <meta name="theme-color" content="#0c1222">

    <title>Smart Medical Health</title>

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

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <!-- Global CSS Variables and Styles -->
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
        }

        /* Base Styles */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--dark);
            color: #f5f5f5;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            min-height: 100vh;
            position: relative;
        }

        /* Medical tech background effect */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                linear-gradient(to bottom, rgba(12, 18, 34, 0.85), rgba(12, 18, 34, 0.92)),
                url('{{ asset('images/hospital.jpeg') }}');
            background-size: cover;
            background-position: center;
            z-index: -2;
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
            animation: gridMove 20s linear infinite;
        }

        @keyframes gridMove {
            0% { transform: translateY(0) }
            100% { transform: translateY(20px) }
        }

        /* Typography */
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Orbitron', sans-serif;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1rem;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            position: relative;
            display: inline-block;
            margin-bottom: 1.5rem;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 60%;
            height: 3px;
            background: linear-gradient(to right, var(--primary), transparent);
        }

        h2 {
            font-size: 2rem;
            margin-top: 2rem;
        }

        p {
            line-height: 1.7;
            margin-bottom: 1rem;
        }

        a {
            color: var(--primary);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        a:hover {
            color: var(--secondary);
            text-decoration: none;
        }

        /* Main content area */
        .main-content {
            min-height: calc(100vh - 120px);
            padding: 30px 0;
            position: relative;
            z-index: 1;
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

        /* Form controls */
        .form-control {
            background: rgba(16, 25, 36, 0.6);
            border: 1px solid rgba(0, 195, 255, 0.2);
            border-radius: 8px;
            color: white;
            padding: 12px 15px;
            transition: all 0.3s;
        }

        .form-control:focus {
            background: rgba(16, 25, 36, 0.8);
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 195, 255, 0.25);
            color: white;
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

        /* Hero section for landing pages */
        .hero-section {
            padding: 120px 0 80px;
            text-align: center;
            position: relative;
        }

        .hero-section h1 {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            text-shadow: 0 0 15px rgba(0, 195, 255, 0.5);
            animation: glow 2s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from { text-shadow: 0 0 5px rgba(0, 195, 255, 0.5); }
            to { text-shadow: 0 0 20px rgba(0, 195, 255, 0.8); }
        }

        .hero-section p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 2rem;
            color: #d0d0d0;
        }

        /* Feature boxes */
        .feature-box {
            border-radius: 12px;
            padding: 25px;
            margin: 15px 0;
            background: rgba(16, 25, 36, 0.7);
            border: 1px solid rgba(0, 195, 255, 0.15);
            transition: all 0.3s ease;
        }

        .feature-box:hover {
            transform: translateY(-5px);
            background: rgba(16, 25, 36, 0.9);
            border-color: rgba(0, 195, 255, 0.3);
            box-shadow: 0 10px 25px rgba(0, 119, 255, 0.2);
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--primary);
            text-shadow: 0 0 10px rgba(0, 195, 255, 0.5);
        }

        /* Custom scrollbar */
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

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .hero-section {
                padding: 80px 0 50px;
            }

            .hero-section h1 {
                font-size: 2rem;
            }

            .hero-section p {
                font-size: 1rem;
            }

            h1 {
                font-size: 1.8rem;
            }

            h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
<!-- Include the Navbar -->
@include('firebase.inc.navbar1')

<!-- Main Content -->
<div class="main-content">
    <div class="container">
        @yield('content') <!-- Dynamic content -->
    </div>
</div>

<!-- Footer -->
<footer class="py-4" style="background: rgba(12, 18, 34, 0.8); border-top: 1px solid rgba(0, 195, 255, 0.2);">
    <div class="container text-center">
        <p class="mb-1">
            <span class="text-primary">&copy; {{ date('Y') }} Smart Medical Health</span>
        </p>
        <p class="mb-0" style="font-size: 0.85rem; opacity: 0.7;">
            Advanced Medical Technology for Better Healthcare
        </p>
    </div>
</footer>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add active class to current navigation item
        const currentLocation = window.location.pathname;
        const navLinks = document.querySelectorAll('.nav-link');

        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href && currentLocation.includes(href) && href !== '/') {
                link.classList.add('active');
                link.style.color = 'var(--primary)';
                link.style.borderBottom = '2px solid var(--primary)';
            }
        });

        // Animate elements when they come into view
        const animateOnScroll = function() {
            const elements = document.querySelectorAll('.card, .feature-box, .btn-cyber');

            elements.forEach(element => {
                const elementPosition = element.getBoundingClientRect().top;
                const screenPosition = window.innerHeight;

                if (elementPosition < screenPosition) {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }
            });
        }

        // Set initial styles
        document.querySelectorAll('.card, .feature-box, .btn-cyber').forEach(element => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(20px)';
            element.style.transition = 'all 0.5s ease';
        });

        // Run animation on load and scroll
        animateOnScroll();
        window.addEventListener('scroll', animateOnScroll);
    });
</script>
</body>
</html>
