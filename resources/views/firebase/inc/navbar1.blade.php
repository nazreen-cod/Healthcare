<style>
    /* Cyber Secondary Navbar Styles */
    .cyber-navbar-secondary {
        background: rgba(16, 25, 36, 0.9);
        border-bottom: 1px solid rgba(0, 195, 255, 0.2);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        padding: 15px 0;
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .cyber-navbar-secondary .navbar-brand {
        font-family: 'Orbitron', sans-serif;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary, #00c3ff) !important;
        text-transform: uppercase;
        letter-spacing: 2px;
        text-shadow: 0 0 10px rgba(0, 195, 255, 0.3);
        display: flex;
        align-items: center;
        transition: all 0.3s;
    }

    .cyber-navbar-secondary .navbar-brand:hover {
        transform: translateY(-2px);
        text-shadow: 0 0 15px rgba(0, 195, 255, 0.5);
    }

    .brand-icon {
        margin-right: 10px;
        font-size: 1.8rem;
        color: var(--primary, #00c3ff);
    }

    /* Animated Navbar Links */
    .cyber-nav-link-secondary {
        color: #e1e1e1 !important;
        font-weight: 500;
        padding: 8px 16px !important;
        margin: 0 5px;
        position: relative;
        z-index: 1;
        border-radius: 8px;
        transition: all 0.3s ease;
        font-size: 1rem;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
    }

    .cyber-nav-link-secondary::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        width: 0;
        height: 2px;
        background: linear-gradient(to right, var(--primary, #00c3ff), var(--secondary, #0077ff));
        transition: all 0.3s ease;
        transform: translateX(-50%);
        z-index: -1;
    }

    .cyber-nav-link-secondary:hover::before,
    .cyber-nav-link-secondary.active::before {
        width: 80%;
    }

    .cyber-nav-link-secondary:hover,
    .cyber-nav-link-secondary.active {
        color: var(--primary, #00c3ff) !important;
        background: rgba(0, 119, 255, 0.1);
    }

    .cyber-nav-link-secondary i {
        margin-right: 6px;
    }

    /* Logout Button */
    .btn-cyber-logout {
        background: linear-gradient(45deg, rgba(255, 75, 75, 0.8), rgba(255, 58, 58, 0.8));
        border: 1px solid rgba(255, 75, 75, 0.2);
        color: white;
        border-radius: 8px;
        padding: 8px 20px;
        font-weight: 500;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        z-index: 1;
        display: flex;
        align-items: center;
    }

    .btn-cyber-logout::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, rgba(255, 58, 58, 0.8), rgba(255, 0, 85, 0.8));
        transition: all 0.4s;
        z-index: -1;
    }

    .btn-cyber-logout:hover::before {
        left: 0;
    }

    .btn-cyber-logout:hover {
        box-shadow: 0 0 15px rgba(255, 75, 75, 0.5);
        transform: translateY(-2px);
        color: white;
    }

    .btn-cyber-logout i {
        margin-right: 6px;
        font-size: 1rem;
    }

    /* Toggler Button */
    .cyber-toggler-secondary {
        border: none;
        background: transparent;
        padding: 5px;
    }

    .cyber-toggler-icon {
        position: relative;
        display: inline-block;
        width: 30px;
        height: 24px;
    }

    .cyber-toggler-icon span {
        position: absolute;
        height: 3px;
        width: 100%;
        background: var(--primary, #00c3ff);
        border-radius: 2px;
        left: 0;
        transition: all 0.3s ease;
    }

    .cyber-toggler-icon span:nth-child(1) {
        top: 0;
    }

    .cyber-toggler-icon span:nth-child(2) {
        top: 10px;
        width: 80%;
    }

    .cyber-toggler-icon span:nth-child(3) {
        top: 20px;
    }

    .cyber-toggler-secondary:hover .cyber-toggler-icon span:nth-child(2) {
        width: 100%;
    }

    /* Mobile Responsive */
    @media (max-width: 991.98px) {
        .navbar-collapse {
            background: rgba(16, 25, 36, 0.95);
            border-radius: 12px;
            padding: 20px;
            margin-top: 15px;
            border: 1px solid rgba(0, 195, 255, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .cyber-nav-link-secondary {
            padding: 12px 15px !important;
            margin: 5px 0;
        }

        .btn-cyber-logout {
            margin-top: 10px;
            width: 100%;
            justify-content: center;
        }
    }
</style>

<nav class="navbar navbar-expand-lg cyber-navbar-secondary">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand" href="{{ url('/') }}">
            <i class="fas fa-heartbeat brand-icon"></i>
            Smart Medical Health
        </a>

        <!-- Toggler Button -->
        <button class="navbar-toggler cyber-toggler-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <div class="cyber-toggler-icon">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </button>

        <!-- Collapsible Menu -->
        <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link cyber-nav-link-secondary {{ Request::is('/') ? 'active' : '' }}" aria-current="page" href="{{ url('/') }}">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                @auth
                    <li class="nav-item">
                        <a class="nav-link cyber-nav-link-secondary" href="{{ url('/dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                @endauth
            </ul>

            @auth
                <!-- Logout Button -->
                <form action="{{ route('firebase.logout') }}" method="POST" class="d-flex ms-2">
                    @csrf
                    <button type="submit" class="btn-cyber-logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            @endauth
        </div>
    </div>
</nav>

<!-- Add Font Awesome if not already included -->
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

<!-- Add Google Fonts for Orbitron if not already included -->
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&display=swap" rel="stylesheet">
