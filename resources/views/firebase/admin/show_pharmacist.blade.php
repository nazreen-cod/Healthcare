@extends('firebase.layoutadmin')

@section('content')
    <style>
        /* Page Header */
        .page-header {
            margin-bottom: 30px;
            font-family: 'Orbitron', sans-serif;
            text-align: center;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 3px;
            position: relative;
            padding-bottom: 15px;
            text-shadow: 0 0 10px rgba(0, 195, 255, 0.5);
        }

        .page-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 2px;
            background: linear-gradient(to right, transparent, var(--primary), transparent);
        }

        /* Futuristic Container */
        .table-container {
            margin: 20px auto;
            padding: 0;
            max-width: 1200px;
            width: 100%;
            overflow: hidden;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Alert messages */
        .alert {
            border: none;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
            width: 100%;
            max-width: 1200px;
            text-align: center;
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

        /* Cyber Card */
        .cyber-card {
            background: rgba(21, 32, 43, 0.8);
            border-radius: 12px;
            overflow: hidden;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 195, 255, 0.2);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            position: relative;
            width: 100%;
            margin: 0 auto;
        }

        .cyber-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, var(--primary), var(--secondary), var(--accent));
        }

        .cyber-card-header {
            background: linear-gradient(90deg, rgba(21, 32, 43, 0.9), rgba(16, 25, 36, 0.9));
            color: var(--primary);
            font-family: 'Orbitron', sans-serif;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(0, 195, 255, 0.2);
            padding: 20px;
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cyber-card-body {
            padding: 0;
            overflow-x: auto;
            width: 100%;
        }

        /* Futuristic Table */
        .cyber-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            color: #fff;
        }

        .cyber-table th {
            background: rgba(0, 77, 128, 0.6);
            color: var(--primary);
            font-family: 'Orbitron', sans-serif;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 15px;
            position: sticky;
            top: 0;
            border-bottom: 1px solid rgba(0, 195, 255, 0.3);
            text-align: center;
        }

        .cyber-table td {
            padding: 12px 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.85);
            text-align: center;
        }

        .cyber-table tbody tr {
            background: rgba(21, 32, 43, 0.4);
            transition: all 0.3s;
        }

        .cyber-table tbody tr:nth-child(odd) {
            background: rgba(16, 25, 36, 0.6);
        }

        .cyber-table tbody tr:hover {
            background: rgba(0, 119, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .cyber-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Action Buttons */
        .btn-cyber-primary {
            background: linear-gradient(45deg, var(--secondary), var(--primary));
            border: none;
            color: white;
            border-radius: 6px;
            padding: 10px 15px;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            z-index: 1;
            display: inline-block;
            text-decoration: none;
        }

        .btn-cyber-primary::before {
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

        .btn-cyber-primary:hover::before {
            left: 0;
        }

        .btn-cyber-primary:hover {
            box-shadow: 0 0 15px rgba(0, 195, 255, 0.5);
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }

        .btn-cyber-edit {
            background: linear-gradient(45deg, #ff9500, #ff7800);
            font-size: 0.8rem;
            padding: 8px 12px;
        }

        .btn-cyber-edit::before {
            background: linear-gradient(45deg, #ff7800, #ff5500);
        }

        .btn-cyber-delete {
            background: linear-gradient(45deg, #ff3a3a, #ff1a1a);
            font-size: 0.8rem;
            padding: 8px 12px;
        }

        .btn-cyber-delete::before {
            background: linear-gradient(45deg, #ff1a1a, #e00000);
        }

        /* Empty state */
        .empty-state {
            padding: 40px 20px;
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
            font-style: italic;
            background: rgba(21, 32, 43, 0.4);
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-in {
            animation: fadeIn 0.5s ease forwards;
        }

        /* Custom scrollbar for table */
        .cyber-card-body::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .cyber-card-body::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }

        .cyber-card-body::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
        }

        .cyber-card-body::-webkit-scrollbar-thumb:hover {
            background: var(--secondary);
        }
    </style>

    <div class="container">
        <h1 class="page-header animate-in">Pharmacist Management</h1>

        <div class="table-container animate-in" style="animation-delay: 0.1s;">
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

            <div class="cyber-card">
                <div class="cyber-card-header">
                    <span><i class="fas fa-prescription-bottle-alt me-2"></i>Pharmacist List</span>
                    <a href="{{ route('firebase.admin.reg_pharmacist') }}" class="btn-cyber-primary">
                        <i class="fas fa-plus me-2"></i>Add Pharmacist
                    </a>
                </div>
                <div class="cyber-card-body">
                    <table class="cyber-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>FULLNAME</th>
                            <th>EMAIL</th>
                            <th>PHONE</th>
                            <th>MMC REG</th>
                            <th>APC VAL</th>
                            <th>ACTIONS</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $i=1; @endphp
                        @forelse($pharmacists as $key => $item)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $item["fname"] ?? 'N/A' }}</td>
                                <td>{{ $item["email"] ?? 'N/A' }}</td>
                                <td>{{ $item["phone"] ?? 'N/A' }}</td>
                                <td>{{ $item["numberMMC"] ?? 'N/A' }}</td>
                                <td>{{ $item["numberAPC"] ?? 'N/A' }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('firebase.admin.edit_pharmacist', ['id' => $key]) }}" class="btn-cyber-primary btn-cyber-edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('firebase.admin.delete_pharmacist', ['id' => $key]) }}" class="btn-cyber-primary btn-cyber-delete" onclick="return confirm('Are you sure you want to delete this pharmacist?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <i class="fas fa-prescription-bottle-alt fa-3x mb-3 d-block mx-auto" style="opacity: 0.3;"></i>
                                    No pharmacists have been registered yet
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add some interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            // Animation for table rows
            const tableRows = document.querySelectorAll('.cyber-table tbody tr');
            tableRows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(10px)';

                setTimeout(() => {
                    row.style.transition = 'all 0.3s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, 100 + (index * 50));
            });

            // Highlight row on hover
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    const cells = this.querySelectorAll('td');
                    cells.forEach(cell => {
                        cell.style.color = 'var(--primary)';
                    });
                });

                row.addEventListener('mouseleave', function() {
                    const cells = this.querySelectorAll('td');
                    cells.forEach(cell => {
                        cell.style.color = 'rgba(255, 255, 255, 0.85)';
                    });
                });
            });
        });
    </script>
@endsection
