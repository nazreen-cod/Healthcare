<style>
    /* Cyber Navbar Styles */
    .cyber-navbar {
        background: rgba(16, 25, 36, 0.9);
        border-bottom: 1px solid rgba(0, 195, 255, 0.2);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        padding: 15px 0;
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .navbar-brand {
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

    .navbar-brand:hover {
        transform: translateY(-2px);
        text-shadow: 0 0 15px rgba(0, 195, 255, 0.5);
    }

    .brand-icon {
        margin-right: 10px;
        font-size: 1.8rem;
        color: var(--primary, #00c3ff);
    }

    /* Animated Underline for Navbar Links */
    .cyber-nav-link {
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

    .cyber-nav-link::before {
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

    .cyber-nav-link:hover::before,
    .cyber-nav-link.active::before {
        width: 80%;
    }

    .cyber-nav-link:hover,
    .cyber-nav-link.active {
        color: var(--primary, #00c3ff) !important;
        background: rgba(0, 119, 255, 0.1);
    }

    .cyber-nav-link i {
        margin-right: 6px;
    }

    /* Toggler Button */
    .cyber-toggler {
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

    .cyber-toggler:hover .cyber-toggler-icon span:nth-child(2) {
        width: 100%;
    }

    /* Role Badges */
    .role-badge {
        font-size: 0.65rem;
        padding: 2px 8px;
        border-radius: 50px;
        margin-left: 5px;
        display: inline-block;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .badge-admin {
        background: linear-gradient(135deg, rgba(255, 75, 75, 0.2), rgba(255, 75, 75, 0.1));
        color: #ff4b4b;
        border: 1px solid rgba(255, 75, 75, 0.3);
    }

    .badge-doctor {
        background: linear-gradient(135deg, rgba(0, 195, 255, 0.2), rgba(0, 195, 255, 0.1));
        color: #00c3ff;
        border: 1px solid rgba(0, 195, 255, 0.3);
    }

    .badge-nurse {
        background: linear-gradient(135deg, rgba(0, 255, 157, 0.2), rgba(0, 255, 157, 0.1));
        color: #00ff9d;
        border: 1px solid rgba(0, 255, 157, 0.3);
    }

    .badge-pharmacy {
        background: linear-gradient(135deg, rgba(255, 187, 0, 0.2), rgba(255, 187, 0, 0.1));
        color: #ffbb00;
        border: 1px solid rgba(255, 187, 0, 0.3);
    }

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

        .cyber-nav-link {
            padding: 12px 15px !important;
            margin: 5px 0;
        }

        .role-badge {
            margin-left: 10px;
        }
    }
</style>

<nav class="navbar navbar-expand-lg cyber-navbar">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand" href="{{url('/')}}">
            <i class="fas fa-heartbeat brand-icon"></i>
            Smart Medical Health
        </a>

        <!-- Toggler Button -->
        <button class="navbar-toggler cyber-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
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
                    <a class="nav-link cyber-nav-link {{ Request::is('/') ? 'active' : '' }}" aria-current="page" href="{{url('/')}}">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link cyber-nav-link {{ Request::is('admin/login') ? 'active' : '' }}" href="{{url('/admin/login')}}">
                        <i class="fas fa-user-shield"></i> Admin
                        <span class="role-badge badge-admin">System</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link cyber-nav-link {{ Request::is('doctor/login') ? 'active' : '' }}" href="{{url('/doctor/login')}}">
                        <i class="fas fa-user-md"></i> Doctor
                        <span class="role-badge badge-doctor">Medical</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link cyber-nav-link {{ Request::is('nurse/login') ? 'active' : '' }}" href="{{url('/nurse/login')}}">
                        <i class="fas fa-user-nurse"></i> Nurse
                        <span class="role-badge badge-nurse">Staff</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link cyber-nav-link {{ Request::is('pharmacist/login') ? 'active' : '' }}" href="{{url('/pharmacist/login')}}">
                        <i class="fas fa-prescription-bottle-alt"></i> Pharmacy
                        <span class="role-badge badge-pharmacy">Meds</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Add Font Awesome if not already included -->
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

<!-- Add Google Fonts for Orbitron if not already included -->
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&display=swap" rel="stylesheet">
