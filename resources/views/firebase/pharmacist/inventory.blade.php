
@extends('firebase.layoutpharmacist')

@section('title', 'Medication Inventory')

@section('content')
    <style>
        /* Inventory Page Styles */
        .inventory-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 15px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 187, 0, 0.2);
        }

        .page-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            color: #ffbb00;
            display: flex;
            align-items: center;
        }

        .page-title i {
            margin-right: 10px;
        }

        .inventory-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: rgba(16, 25, 36, 0.7);
            border-radius: 12px;
            border: 1px solid rgba(255, 187, 0, 0.15);
            padding: 15px;
            position: relative;
            transition: all 0.3s;
            overflow: hidden;
            text-align: center;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, #ffbb00, #ff9800);
        }

        .stat-icon {
            font-size: 1.5rem;
            color: #ffbb00;
            margin-bottom: 10px;
        }

        .stat-count {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 5px;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.8rem;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .inventory-tabs {
            display: flex;
            gap: 5px;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(255, 187, 0, 0.15);
        }

        .inventory-tab {
            padding: 10px 20px;
            background: rgba(16, 25, 36, 0.7);
            border: 1px solid rgba(255, 187, 0, 0.15);
            border-bottom: none;
            border-radius: 8px 8px 0 0;
            color: rgba(255, 255, 255, 0.7);
            cursor: pointer;
            transition: all 0.3s;
        }

        .inventory-tab.active {
            background: rgba(255, 187, 0, 0.1);
            color: #ffbb00;
            border-color: rgba(255, 187, 0, 0.3);
        }

        .inventory-tab:hover {
            color: white;
        }

        .inventory-tab i {
            margin-right: 8px;
        }

        .inventory-content {
            background: rgba(16, 25, 36, 0.7);
            border-radius: 12px;
            border: 1px solid rgba(255, 187, 0, 0.15);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .inventory-header {
            background: rgba(255, 187, 0, 0.1);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 187, 0, 0.15);
        }

        .inventory-header h2 {
            font-family: 'Orbitron', sans-serif;
            color: #ffbb00;
            font-size: 1.2rem;
            margin: 0;
        }

        .inventory-body {
            padding: 20px;
        }

        .inventory-actions {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-container {
            position: relative;
            flex-grow: 1;
        }

        .search-input {
            width: 100%;
            padding: 10px 40px 10px 15px;
            border: 1px solid rgba(255, 187, 0, 0.2);
            border-radius: 8px;
            background: rgba(16, 25, 36, 0.7);
            color: white;
        }

        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.5);
        }

        .btn-primary {
            background: linear-gradient(to right, #ffbb00, #ff9800);
            border: none;
            color: #000;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 187, 0, 0.3);
        }

        .inventory-table {
            width: 100%;
            border-collapse: collapse;
        }

        .inventory-table th {
            background: rgba(255, 187, 0, 0.1);
            color: rgba(255, 255, 255, 0.8);
            text-align: left;
            padding: 12px 15px;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .inventory-table td {
            padding: 12px 15px;
            border-top: 1px solid rgba(255, 187, 0, 0.1);
            color: rgba(255, 255, 255, 0.7);
        }

        .inventory-table tr:hover td {
            background: rgba(255, 187, 0, 0.05);
        }

        .stock-level {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .stock-normal {
            background: rgba(0, 200, 83, 0.15);
            color: #00c853;
        }

        .stock-low {
            background: rgba(255, 187, 0, 0.15);
            color: #ffbb00;
        }

        .stock-critical {
            background: rgba(255, 82, 82, 0.15);
            color: #ff5252;
        }

        .action-btn {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 5px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .action-btn:hover {
            transform: translateY(-2px);
        }

        .btn-edit {
            border-color: rgba(255, 187, 0, 0.3);
            color: #ffbb00;
        }

        .btn-delete {
            border-color: rgba(255, 82, 82, 0.3);
            color: #ff5252;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.3s ease;
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            background: rgba(16, 25, 36, 0.95);
            border: 1px solid rgba(255, 187, 0, 0.2);
            border-radius: 12px;
            width: 100%;
            max-width: 500px;
            animation: slideIn 0.3s ease;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid rgba(255, 187, 0, 0.2);
        }

        .modal-title {
            font-family: 'Orbitron', sans-serif;
            color: #ffbb00;
            font-size: 1.2rem;
            margin: 0;
        }

        .modal-close {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.5);
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.3s;
        }

        .modal-close:hover {
            color: #ff5252;
        }

        .modal-body {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            display: block;
            margin-bottom: 5px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 10px 15px;
            background: rgba(16, 25, 36, 0.7);
            border: 1px solid rgba(255, 187, 0, 0.2);
            border-radius: 8px;
            color: white;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: rgba(255, 187, 0, 0.5);
        }

        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='rgba(255, 255, 255, 0.5)' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 1em;
        }

        .modal-footer {
            padding: 15px 20px;
            border-top: 1px solid rgba(255, 187, 0, 0.2);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(-30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Responsive */
        @media (max-width: 992px) {
            .inventory-stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .inventory-tabs {
                flex-wrap: wrap;
            }

            .inventory-tab {
                flex-grow: 1;
                text-align: center;
            }

            .inventory-actions {
                flex-direction: column;
            }
        }

        @media (max-width: 576px) {
            .inventory-stats {
                grid-template-columns: 1fr;
            }

            .inventory-table {
                font-size: 0.8rem;
            }
        }
    </style>

    <div class="inventory-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title"><i class="fas fa-boxes"></i> Medication Inventory</h1>
            <button id="addMedicationBtn" class="btn-primary">
                <i class="fas fa-plus"></i> Add Medication
            </button>
        </div>

        <!-- Inventory Stats -->
        <div class="inventory-stats">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-pills"></i>
                </div>
                <div class="stat-count" id="totalMedications">{{ count($inventory) }}</div>
                <div class="stat-label">Total Medications</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-count" id="lowStockCount">{{ count($lowStockItems) }}</div>
                <div class="stat-label">Low Stock Items</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-box-open"></i>
                </div>
                <div class="stat-count" id="outOfStockCount">
                    {{ count(array_filter($inventory, function($item) { return isset($item['quantity']) && $item['quantity'] == 0; })) }}
                </div>
                <div class="stat-label">Out of Stock</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-count" id="expiringCount">
                    {{ count(array_filter($inventory, function($item) {
                        return isset($item['expiry_date']) && strtotime($item['expiry_date']) < strtotime('+30 days');
                    })) }}
                </div>
                <div class="stat-label">Expiring Soon</div>
            </div>
        </div>

        <!-- Inventory Tabs -->
        <div class="inventory-tabs">
            <div class="inventory-tab active" data-target="all-inventory">
                <i class="fas fa-list"></i> All Inventory
            </div>
            <div class="inventory-tab" data-target="low-stock">
                <i class="fas fa-exclamation-triangle"></i> Low Stock
            </div>
            <div class="inventory-tab" data-target="categories">
                <i class="fas fa-tags"></i> Categories
            </div>
        </div>

        <!-- All Inventory -->
        <div class="inventory-content tab-content" id="all-inventory">
            <div class="inventory-header">
                <h2><i class="fas fa-clipboard-list me-2"></i> All Medications</h2>
            </div>
            <div class="inventory-body">
                <div class="inventory-actions">
                    <div class="search-container">
                        <input type="text" id="searchInventory" class="search-input" placeholder="Search medications...">
                        <i class="fas fa-search search-icon"></i>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="inventory-table" id="inventoryTable">
                        <thead>
                        <tr>
                            <th>Medication Name</th>
                            <th>Category</th>
                            <th>Dosage</th>
                            <th>Quantity</th>
                            <th>Expiry Date</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($inventory as $item)
                            <tr data-id="{{ $item['id'] }}">
                                <td>{{ $item['medication_name'] }}</td>
                                <td>{{ $item['category'] ?? 'N/A' }}</td>
                                <td>{{ $item['dosage'] ?? 'N/A' }}</td>
                                <td>
                                    <span class="stock-level
                                        @if(isset($item['quantity']) && $item['quantity'] <= 0) stock-critical
                                        @elseif(isset($item['quantity']) && $item['quantity'] <= 10) stock-low
                                        @else stock-normal @endif">
                                        {{ $item['quantity'] ?? 0 }}
                                    </span>
                                </td>
                                <td>{{ isset($item['expiry_date']) ? date('M d, Y', strtotime($item['expiry_date'])) : 'N/A' }}</td>
                                <td>{{ isset($item['last_updated']) ? date('M d, Y', strtotime($item['last_updated'])) : 'N/A' }}</td>
                                <td>
                                    <button class="action-btn btn-edit edit-medication" data-id="{{ $item['id'] }}" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="action-btn btn-delete delete-medication" data-id="{{ $item['id'] }}" data-name="{{ $item['medication_name'] }}" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No medications in inventory</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Low Stock -->
        <div class="inventory-content tab-content" id="low-stock" style="display: none;">
            <div class="inventory-header">
                <h2><i class="fas fa-exclamation-triangle me-2"></i> Low Stock Alert</h2>
            </div>
            <div class="inventory-body">
                <div class="table-responsive">
                    <table class="inventory-table">
                        <thead>
                        <tr>
                            <th>Medication Name</th>
                            <th>Category</th>
                            <th>Current Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($lowStockItems as $item)
                            <tr>
                                <td>{{ $item['medication_name'] }}</td>
                                <td>{{ $item['category'] ?? 'N/A' }}</td>
                                <td>{{ $item['quantity'] ?? 0 }}</td>
                                <td>
                                    <span class="stock-level
                                        @if(isset($item['quantity']) && $item['quantity'] <= 3) stock-critical
                                        @else stock-low @endif">
                                        {{ isset($item['quantity']) && $item['quantity'] <= 3 ? 'Critical' : 'Low' }}
                                    </span>
                                </td>
                                <td>
                                    <button class="action-btn btn-edit edit-medication" data-id="{{ $item['id'] }}" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No low stock medications</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Categories -->
        <div class="inventory-content tab-content" id="categories" style="display: none;">
            <div class="inventory-header">
                <h2><i class="fas fa-tags me-2"></i> Medication Categories</h2>
            </div>
            <div class="inventory-body">
                <div class="table-responsive">
                    @php
                        $categories = [];
                        foreach ($inventory as $item) {
                            $category = $item['category'] ?? 'Uncategorized';
                            if (!isset($categories[$category])) {
                                $categories[$category] = [
                                    'count' => 0,
                                    'total_stock' => 0
                                ];
                            }
                            $categories[$category]['count']++;
                            $categories[$category]['total_stock'] += ($item['quantity'] ?? 0);
                        }
                        ksort($categories);
                    @endphp

                    <table class="inventory-table">
                        <thead>
                        <tr>
                            <th>Category</th>
                            <th>Medications</th>
                            <th>Total Stock</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($categories as $category => $data)
                            <tr>
                                <td>{{ $category }}</td>
                                <td>{{ $data['count'] }}</td>
                                <td>{{ $data['total_stock'] }}</td>
                                <td>
                                    <button class="action-btn btn-edit view-category" data-category="{{ $category }}" title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No categories found</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Medication Modal -->
        <div class="modal" id="addMedicationModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title"><i class="fas fa-plus-circle me-2"></i> Add Medication</h2>
                    <button class="modal-close" id="closeAddModal">&times;</button>
                </div>
                <form action="{{ route('firebase.pharmacist.inventory.add') }}" method="POST" id="addMedicationForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="medication_name" class="form-label">Medication Name</label>
                            <input type="text" id="medication_name" name="medication_name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" id="quantity" name="quantity" class="form-control" min="0" required>
                        </div>

                        <div class="form-group">
                            <label for="category" class="form-label">Category</label>
                            <select id="category" name="category" class="form-control form-select" required>
                                <option value="">Select Category</option>
                                <option value="Antibiotics">Antibiotics</option>
                                <option value="Analgesics">Analgesics</option>
                                <option value="Antiviral">Antiviral</option>
                                <option value="Antihistamines">Antihistamines</option>
                                <option value="Cardiovascular">Cardiovascular</option>
                                <option value="Gastrointestinal">Gastrointestinal</option>
                                <option value="Respiratory">Respiratory</option>
                                <option value="Vaccines">Vaccines</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="dosage" class="form-label">Dosage</label>
                            <input type="text" id="dosage" name="dosage" class="form-control" placeholder="e.g. 500mg">
                        </div>

                        <div class="form-group">
                            <label for="expiry_date" class="form-label">Expiry Date</label>
                            <input type="date" id="expiry_date" name="expiry_date" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" id="cancelAddBtn">Cancel</button>
                        <button type="submit" class="btn-primary">Add Medication</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Medication Modal -->
        <div class="modal" id="editMedicationModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title"><i class="fas fa-edit me-2"></i> Edit Medication</h2>
                    <button class="modal-close" id="closeEditModal">&times;</button>
                </div>
                <form action="{{ route('firebase.pharmacist.inventory.update') }}" method="POST" id="editMedicationForm">
                    @csrf
                    <input type="hidden" id="edit_id" name="id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_medication_name" class="form-label">Medication Name</label>
                            <input type="text" id="edit_medication_name" name="medication_name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_quantity" class="form-label">Quantity</label>
                            <input type="number" id="edit_quantity" name="quantity" class="form-control" min="0" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_category" class="form-label">Category</label>
                            <select id="edit_category" name="category" class="form-control form-select" required>
                                <option value="">Select Category</option>
                                <option value="Antibiotics">Antibiotics</option>
                                <option value="Analgesics">Analgesics</option>
                                <option value="Antiviral">Antiviral</option>
                                <option value="Antihistamines">Antihistamines</option>
                                <option value="Cardiovascular">Cardiovascular</option>
                                <option value="Gastrointestinal">Gastrointestinal</option>
                                <option value="Respiratory">Respiratory</option>
                                <option value="Vaccines">Vaccines</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="edit_dosage" class="form-label">Dosage</label>
                            <input type="text" id="edit_dosage" name="dosage" class="form-control" placeholder="e.g. 500mg">
                        </div>

                        <div class="form-group">
                            <label for="edit_expiry_date" class="form-label">Expiry Date</label>
                            <input type="date" id="edit_expiry_date" name="expiry_date" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea id="edit_description" name="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" id="cancelEditBtn">Cancel</button>
                        <button type="submit" class="btn-primary">Update Medication</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal" id="deleteConfirmModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title"><i class="fas fa-trash-alt me-2"></i> Delete Medication</h2>
                    <button class="modal-close" id="closeDeleteModal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong id="deleteMedicationName"></strong>? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button class="btn-secondary" id="cancelDeleteBtn">Cancel</button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-primary" style="background: linear-gradient(to right, #ff5252, #ff1744); color: white;">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab Switching
            const tabs = document.querySelectorAll('.inventory-tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const target = this.dataset.target;

                    // Hide all tab content
                    document.querySelectorAll('.tab-content').forEach(content => {
                        content.style.display = 'none';
                    });

                    // Show the selected tab content
                    document.getElementById(target).style.display = 'block';

                    // Update active tab
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            // Search Functionality
            const searchInput = document.getElementById('searchInventory');
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const tableRows = document.querySelectorAll('#inventoryTable tbody tr');

                tableRows.forEach(row => {
                    const medicationName = row.cells[0].textContent.toLowerCase();
                    const category = row.cells[1].textContent.toLowerCase();

                    if (medicationName.includes(searchTerm) || category.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            // Add Medication Modal
            const addMedicationBtn = document.getElementById('addMedicationBtn');
            const addMedicationModal = document.getElementById('addMedicationModal');
            const closeAddModal = document.getElementById('closeAddModal');
            const cancelAddBtn = document.getElementById('cancelAddBtn');

            addMedicationBtn.addEventListener('click', function() {
                addMedicationModal.classList.add('show');
            });

            closeAddModal.addEventListener('click', function() {
                addMedicationModal.classList.remove('show');
            });

            cancelAddBtn.addEventListener('click', function() {
                addMedicationModal.classList.remove('show');
            });

            // Edit Medication Modal
            const editButtons = document.querySelectorAll('.edit-medication');
            const editMedicationModal = document.getElementById('editMedicationModal');
            const closeEditModal = document.getElementById('closeEditModal');
            const cancelEditBtn = document.getElementById('cancelEditBtn');

            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const medicationId = this.dataset.id;
                    const row = document.querySelector(`tr[data-id="${medicationId}"]`);

                    if (row) {
                        document.getElementById('edit_id').value = medicationId;
                        document.getElementById('edit_medication_name').value = row.cells[0].textContent.trim();
                        document.getElementById('edit_category').value = row.cells[1].textContent.trim() !== 'N/A' ? row.cells[1].textContent.trim() : '';
                        document.getElementById('edit_dosage').value = row.cells[2].textContent.trim() !== 'N/A' ? row.cells[2].textContent.trim() : '';
                        document.getElementById('edit_quantity').value = parseInt(row.cells[3].textContent.trim());

                        // Handle expiry date (convert from "May 12, 2025" to "2025-05-12")
                        const expiryText = row.cells[4].textContent.trim();
                        if (expiryText !== 'N/A') {
                            const expiryDate = new Date(expiryText);
                            const year = expiryDate.getFullYear();
                            const month = String(expiryDate.getMonth() + 1).padStart(2, '0');
                            const day = String(expiryDate.getDate()).padStart(2, '0');
                            document.getElementById('edit_expiry_date').value = `${year}-${month}-${day}`;
                        } else {
                            document.getElementById('edit_expiry_date').value = '';
                        }

                        // We don't have description in the table, so we'll need to fetch it or leave it blank
                        document.getElementById('edit_description').value = '';

                        editMedicationModal.classList.add('show');
                    }
                });
            });

            closeEditModal.addEventListener('click', function() {
                editMedicationModal.classList.remove('show');
            });

            cancelEditBtn.addEventListener('click', function() {
                editMedicationModal.classList.remove('show');
            });

            // Delete Medication
            const deleteButtons = document.querySelectorAll('.delete-medication');
            const deleteConfirmModal = document.getElementById('deleteConfirmModal');
            const closeDeleteModal = document.getElementById('closeDeleteModal');
            const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
            const deleteForm = document.getElementById('deleteForm');
            const deleteMedicationName = document.getElementById('deleteMedicationName');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const medicationId = this.dataset.id;
                    const medicationName = this.dataset.name;

                    deleteMedicationName.textContent = medicationName;
                    deleteForm.action = "{{ route('firebase.pharmacist.inventory.delete', '') }}/" + medicationId;

                    deleteConfirmModal.classList.add('show');
                });
            });

            closeDeleteModal.addEventListener('click', function() {
                deleteConfirmModal.classList.remove('show');
            });

            cancelDeleteBtn.addEventListener('click', function() {
                deleteConfirmModal.classList.remove('show');
            });

            // View Category
            const viewCategoryButtons = document.querySelectorAll('.view-category');
            viewCategoryButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const category = this.dataset.category;

                    // Switch to all inventory tab
                    document.querySelector('.inventory-tab[data-target="all-inventory"]').click();

                    // Set search to the category
                    const searchInput = document.getElementById('searchInventory');
                    searchInput.value = category;

                    // Trigger the search
                    const event = new Event('input');
                    searchInput.dispatchEvent(event);
                });
            });

            // Optional: Close modals when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === addMedicationModal) {
                    addMedicationModal.classList.remove('show');
                }
                if (event.target === editMedicationModal) {
                    editMedicationModal.classList.remove('show');
                }
                if (event.target === deleteConfirmModal) {
                    deleteConfirmModal.classList.remove('show');
                }
            });
        });
    </script>
@endsection
