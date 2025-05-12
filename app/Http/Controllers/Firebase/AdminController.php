<?php

namespace App\Http\Controllers\Firebase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    protected $database;
    protected $tablename;
    protected $auditLogTable = 'audit_logs';

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->tablename = 'admin'; // Ensure this matches your Firebase structure
        $this->tablenames = 'doctor'; // Ensure this matches your Firebase structure
        $this->tablenurse = 'nurse'; // Ensure this matches your Firebase structure
        $this->tablepharmacist = 'pharmacist'; // Ensure this matches your Firebase structure
    }


    // Display the admin login page
    public function index()
    {
        return view('firebase.admin.index');
    }

    public function dashboard()
    {
        try {
            // Get user name from session
            $fname = session('admin_name', 'Admin');

            // Initialize counters
            $doctorCount = 0;
            $nurseCount = 0;
            $pharmacistCount = 0;
            $patientCount = 0;
            $reportsCount = 0;
            $appointmentsCount = 0;
            $prescriptionsCount = 0;

            // Fetch data from all tables
            $doctors = $this->database->getReference('doctor')->getValue() ?: [];
            $nurses = $this->database->getReference('nurse')->getValue() ?: [];
            $pharmacists = $this->database->getReference('pharmacist')->getValue() ?: [];
            $patients = $this->database->getReference('merge_patient')->getValue() ?: [];
            $medicalReports = $this->database->getReference('full_patientdata')->getValue() ?: [];
            $appointments = $this->database->getReference('appointments')->getValue() ?: [];

            // Update counters
            $doctorCount = count($doctors);
            $nurseCount = count($nurses);
            $pharmacistCount = count($pharmacists);
            $patientCount = count($patients);
            $reportsCount = count($medicalReports);
            $appointmentsCount = count($appointments);

            // Count prescriptions
            foreach ($medicalReports as $report) {
                if (isset($report['prescriptions']) && is_array($report['prescriptions'])) {
                    $prescriptionsCount += count($report['prescriptions']);
                }
            }

            // Prepare recent staff data from all three tables
            $recentStaff = [];

            // Process doctors
            foreach ($doctors as $id => $doctor) {
                if (isset($doctor['fname'], $doctor['lname'])) {
                    $recentStaff[] = [
                        'id' => $id,
                        'name' => $doctor['fname'] . ' ' . $doctor['lname'],
                        'role' => 'doctor',
                        'timestamp' => isset($doctor['created_at']) ? strtotime($doctor['created_at']) : time()
                    ];
                }
            }

            // Process nurses
            foreach ($nurses as $id => $nurse) {
                if (isset($nurse['fname'], $nurse['lname'])) {
                    $recentStaff[] = [
                        'id' => $id,
                        'name' => $nurse['fname'] . ' ' . $nurse['lname'],
                        'role' => 'nurse',
                        'timestamp' => isset($nurse['created_at']) ? strtotime($nurse['created_at']) : time()
                    ];
                }
            }

            // Process pharmacists
            foreach ($pharmacists as $id => $pharmacist) {
                if (isset($pharmacist['fname'])) {
                    // For pharmacists, check if 'lname' exists, otherwise just use 'fname'
                    $name = isset($pharmacist['lname']) ?
                        $pharmacist['fname'] . ' ' . $pharmacist['lname'] :
                        $pharmacist['fname'];

                    $recentStaff[] = [
                        'id' => $id,
                        'name' => $name,
                        'role' => 'pharmacist',
                        'timestamp' => isset($pharmacist['created_at']) ? strtotime($pharmacist['created_at']) : time()
                    ];
                }
            }

            // Sort all staff by timestamp (most recent first)
            usort($recentStaff, function($a, $b) {
                return $b['timestamp'] - $a['timestamp'];
            });

            // Limit to 5 most recent staff members
            $recentStaff = array_slice($recentStaff, 0, 5);

            // Return view with all data
            return view('firebase.admin.dashboard', compact(
                'fname',
                'doctorCount',
                'nurseCount',
                'pharmacistCount',
                'patientCount',
                'reportsCount',
                'appointmentsCount',
                'prescriptionsCount',
                'recentStaff'
            ));
        } catch (\Exception $e) {
            \Log::error('Error loading admin dashboard: ' . $e->getMessage());

            // Even if there's an error, still try to show the dashboard with the admin name
            $fname = session('admin_name', 'Admin');
            return view('firebase.admin.dashboard', compact('fname'))->with('error', 'Error loading dashboard data: ' . $e->getMessage());
        }
    }


    public function logout()
    {
        session()->flush(); // Clear all session data
        return redirect(route('firebase.admin.loginadmin'))->with('success', 'Logged out successfully.');
    }

    // Handle admin login
    public function loginadmin(Request $request)
    {
        \Log::info('Admin login initiated', $request->all());

        // Validate input fields
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try {
            // Fetch all admin data from Firebase
            $admins = $this->database->getReference($this->tablename)->getValue();
            \Log::info('Admins fetched from Firebase', ['admin' => $admins]);
            \Log::info('checkpoint A');

            // Check if admins data is available and valid
            if (!$admins || !is_array($admins)) {
                return redirect()->back()->with('error', 'Invalid email or password.');
            }
            \Log::info('checkpoint B');

            foreach ($admins as $adminId => $adminData) {
                \Log::info('Checking admin record:', ['adminId' => $adminId, 'adminData' => $adminData]);

                \Log::info('checkpoint C');
                // Validate structure and match email
                if (
                    isset($adminData['email'], $adminData['password']) &&
                    $adminData['email'] === $request->input('email') &&
                    \Illuminate\Support\Facades\Hash::check($request->input('password'), $adminData['password'])
                ) {
                    \Log::info('Admin authenticated successfully', ['admin_id' => $adminId]);

                    // Store session data for admin
                    session([
                        'admin_id' => $adminId,
                        'admin_email' => $adminData['email'],
                        'admin_name' => $adminData['fname'],
                    ]);
                    \Log::info('checkpoint D');

                    return redirect(route('firebase.admin.dashboard'))->with('success', 'Welcomeback!');
                }
            }
            \Log::info('checkpoint E');
            // If no matching admin found
            return redirect()->back()->with('error', 'Invalid email or password.');
        } catch (\Exception $e) {
            \Log::info('checkpoint F');
            // Log the exception for debugging
            \Log::error('Admin login error:', [
                'exception' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'An error occurred during login. Please try again.');
        }
    }

    /**
     * Log changes to the audit log
     *
     * @param string $action The action performed (create, update, delete)
     * @param string $staffType The type of staff (doctor, nurse, pharmacist)
     * @param string $staffId The ID of the staff member
     * @param array $oldData The data before changes (null for create)
     * @param array $newData The new data after changes (null for delete)
     * @return void
     */
    private function logAudit($action, $staffType, $staffId, $oldData = null, $newData = null)
    {
        try {
            // Get the admin who made the change
            $adminId = session('admin_id', 'unknown');
            $adminName = session('admin_name', 'Unknown Admin');

            // Create the audit log entry
            $logEntry = [
                'timestamp' => date('Y-m-d H:i:s'),
                'action' => $action,
                'staff_type' => $staffType,
                'staff_id' => $staffId,
                'admin_id' => $adminId,
                'admin_name' => $adminName,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ];

            // For updates, calculate and store what changed
            if ($action === 'update' && $oldData && $newData) {
                $changes = [];

                foreach ($newData as $key => $value) {
                    // Skip password from changes log for security
                    if ($key === 'password') {
                        if (isset($oldData[$key]) && $oldData[$key] !== $value) {
                            $changes[$key] = [
                                'from' => '******',
                                'to' => '******',
                                'changed' => true
                            ];
                        }
                        continue;
                    }

                    // Check if the field exists in old data
                    if (isset($oldData[$key])) {
                        // Check if the value changed
                        if ($oldData[$key] !== $value) {
                            $changes[$key] = [
                                'from' => $oldData[$key],
                                'to' => $value,
                                'changed' => true
                            ];
                        }
                    } else {
                        // This is a new field
                        $changes[$key] = [
                            'from' => null,
                            'to' => $value,
                            'changed' => true
                        ];
                    }
                }

                // Check for removed fields
                foreach ($oldData as $key => $value) {
                    if (!isset($newData[$key]) && $key !== 'password') {
                        $changes[$key] = [
                            'from' => $value,
                            'to' => null,
                            'changed' => true
                        ];
                    }
                }

                $logEntry['changes'] = $changes;
            } elseif ($action === 'create' && $newData) {
                // For create, store the new data (excluding password)
                $createData = $newData;
                if (isset($createData['password'])) {
                    $createData['password'] = '******';
                }
                $logEntry['created_data'] = $createData;
            } elseif ($action === 'delete' && $oldData) {
                // For delete, store the deleted data (excluding password)
                $deleteData = $oldData;
                if (isset($deleteData['password'])) {
                    $deleteData['password'] = '******';
                }
                $logEntry['deleted_data'] = $deleteData;
            }

            // Push to the audit log table
            $this->database->getReference($this->auditLogTable)->push($logEntry);

        } catch (\Exception $e) {
            \Log::error('Error creating audit log: ' . $e->getMessage());
            // Silent failure - don't stop the main operation if logging fails
        }
    }

    //registration page
    public function reg_doctor(){
        return view('firebase.admin.reg_doctor');
    }

    // store doctor
    public function store_doctor(Request $request)
    {
        \Log::info('try1');
        // Validate input fields
        $request->validate([
            'numberMMC' => 'required|numeric|digits:5', // Enforce exactly 5 digits
            'numberAPC' => 'required|numeric|digits:5', // Enforce exactly 5 digits
        ]);

        // Prepare data to store
        \Log::info('try2');
        $postData = [
            'fname' => $request->fullname,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'department' => $request->department,
            'designation' => $request->designation,
            'numberMMC' => $request->numberMMC,
            'numberAPC' => $request->numberAPC,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => session('admin_name', 'Admin'),
        ];

        \Log::info('try3');
        // Store data in Firebase
        $postRef = $this->database->getReference($this->tablenames)->push($postData);

        // Check if data is stored successfully
        if ($postRef) {
            // Log the audit for creation
            $staffId = $postRef->getKey();
            $this->logAudit('create', 'doctor', $staffId, null, $postData);

            return redirect(route('firebase.admin.reg_doctor'))->with('success', 'Doctor Added Successfully');
        } else {
            return redirect(route('firebase.admin.reg_doctor'))->with('error', 'Doctor Not Added');
        }
    }

    // show doctor data
    public function show_doctor()
    {
        // Fetch all doctors from Firebase
        $doctors = $this->database->getReference($this->tablenames)->getValue();
        // Pass the data to the view
        return view('firebase.admin.show_doctor', compact('doctors'));
    }

    //edit doctor
    public function edit_doctor($id)
    {
        $key = $id;
        $editdata = $this->database->getReference($this->tablenames.'/'.$key)->getValue();

        if (!$editdata) {
            return redirect('show_doctor')->with('status', 'Doctor ID not Found');
        }

        // Fetch audit logs for this doctor
        $auditLogs = [];

        try {
            // Query the audit logs where staff_id equals our doctor ID
            $logsRef = $this->database->getReference($this->auditLogTable)
                ->orderByChild('staff_id')
                ->equalTo($key)
                ->getValue();

            if ($logsRef) {
                foreach ($logsRef as $logKey => $logData) {
                    if ($logData['staff_type'] === 'doctor') {
                        $logData['key'] = $logKey;
                        $auditLogs[$logKey] = $logData;
                    }
                }

                // Sort logs by timestamp (most recent first)
                uasort($auditLogs, function($a, $b) {
                    return strtotime($b['timestamp']) - strtotime($a['timestamp']);
                });
            }
        } catch (\Exception $e) {
            // Create database index if it doesn't exist
            \Log::warning('Error fetching audit logs: ' . $e->getMessage());
            // Continue without audit logs rather than failing
        }

        return view('firebase.admin.edit_doctor', compact('editdata', 'key', 'auditLogs'));
    }

    // update doctor
    public function updatedoctor(Request $request, $id)
    {
        $key = $id;

        // Fetch the existing data for this doctor
        $existingData = $this->database->getReference($this->tablenames . '/' . $key)->getValue();

        // Prepare the update data
        $updateData = [
            'fname' => $request->fullname,
            'email' => $request->email,
            'phone' => $request->phone,
            'department' => $request->department,
            'designation' => $request->designation,
            'numberMMC' => $request->numberMMC,
            'numberAPC' => $request->numberAPC,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => session('admin_name', 'Admin'),
        ];

        // Only hash the password if it has been changed
        if ($request->password && $request->password !== $existingData['password']) {
            $updateData['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        } else {
            // Keep the existing password if not changed
            $updateData['password'] = $existingData['password'];
        }

        // Update the data in the database
        $res_updated = $this->database->getReference($this->tablenames . '/' . $key)->update($updateData);

        if ($res_updated) {
            // Log the audit for update
            $this->logAudit('update', 'doctor', $key, $existingData, $updateData);

            return redirect(route('firebase.admin.show_doctor'))->with('success', 'Doctor Updated Successfully');
        } else {
            return redirect(route('firebase.admin.show_doctor'))->with('error', 'Doctor Not Updated');
        }
    }


    //delete doctor
    public function deletedoctor($id){
        $key = $id;

        // Get existing data before deletion
        $existingData = $this->database->getReference($this->tablenames . '/' . $key)->getValue();

        $del_data = $this->database->getReference($this->tablenames.'/'.$key)->remove();
        if($del_data){
            // Log the audit for deletion
            $this->logAudit('delete', 'doctor', $key, $existingData, null);

            return redirect(route('firebase.admin.show_doctor'))->with('success','Doctor Deleted Successfully');
        }
        else{
            return redirect(route('firebase.admin.show_doctor'))->with('error','Doctor Not Deleted');
        }
    }

    //registration page nurse
    public function reg_nurse(){
        return view('firebase.admin.reg_nurse');
    }

    // store nurse
    public function store_nurse(Request $request)
    {
        \Log::info('try1');
        // Validate input fields
        $request->validate([
            'numberMMC' => 'required|numeric|digits:5', // Enforce exactly 5 digits
            'numberAPC' => 'required|numeric|digits:5', // Enforce exactly 5 digits
        ]);

        // Prepare data to store
        \Log::info('try2');
        $postData = [
            'fname' => $request->fullname,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'department' => $request->department,
            'designation' => $request->designation,
            'numberMMC' => $request->numberMMC,
            'numberAPC' => $request->numberAPC,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => session('admin_name', 'Admin'),
        ];

        \Log::info('try3');
        // Store data in Firebase
        $postRef = $this->database->getReference($this->tablenurse)->push($postData);

        // Check if data is stored successfully
        if ($postRef) {
            // Log the audit for creation
            $staffId = $postRef->getKey();
            $this->logAudit('create', 'nurse', $staffId, null, $postData);

            return redirect(route('firebase.admin.reg_nurse'))->with('success', 'Nurse Added Successfully');
        } else {
            return redirect(route('firebase.admin.reg_nurse'))->with('error', 'Nurse Not Added');
        }
    }

    // show nurse database
    public function show_nurse()
    {
        // Fetch all nurse from Firebase
        $nurses = $this->database->getReference($this->tablenurse)->getValue();
        // Pass the data to the view
        return view('firebase.admin.show_nurse', compact('nurses'));
    }

    //edit nurse
    public function edit_nurse($id)
    {
        $key = $id;
        $editdata = $this->database->getReference($this->tablenurse)->getChild($key)->getValue();

        if (!$editdata) {
            return redirect('show_nurse')->with('status', 'Nurse ID not Found');
        }

        // Fetch audit logs for this nurse
        $auditLogs = [];

        try {
            // Query the audit logs where staff_id equals our nurse ID
            $logsRef = $this->database->getReference($this->auditLogTable)
                ->orderByChild('staff_id')
                ->equalTo($key)
                ->getValue();

            if ($logsRef) {
                foreach ($logsRef as $logKey => $logData) {
                    if ($logData['staff_type'] === 'nurse') {
                        $logData['key'] = $logKey;
                        $auditLogs[$logKey] = $logData;
                    }
                }

                // Sort logs by timestamp (most recent first)
                uasort($auditLogs, function($a, $b) {
                    return strtotime($b['timestamp']) - strtotime($a['timestamp']);
                });
            }
        } catch (\Exception $e) {
            \Log::warning('Error fetching audit logs: ' . $e->getMessage());
            // Continue without audit logs rather than failing
        }

        return view('firebase.admin.edit_nurse', compact('editdata', 'key', 'auditLogs'));
    }

    // update nurse
    public function updatenurse(Request $request, $id)
    {
        $key = $id;

        // Fetch the existing data for this nurse
        $existingData = $this->database->getReference($this->tablenurse . '/' . $key)->getValue();

        // Prepare the update data
        $updateData = [
            'fname' => $request->fullname,
            'email' => $request->email,
            'phone' => $request->phone,
            'department' => $request->department,
            'designation' => $request->designation,
            'numberMMC' => $request->numberMMC,
            'numberAPC' => $request->numberAPC,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => session('admin_name', 'Admin'),
        ];

        // Only hash the password if it has been changed
        if ($request->password && $request->password !== $existingData['password']) {
            $updateData['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        } else {
            // Keep the existing password if not changed
            $updateData['password'] = $existingData['password'];
        }

        // Update the data in the database
        $res_updated = $this->database->getReference($this->tablenurse . '/' . $key)->update($updateData);

        if ($res_updated) {
            // Log the audit for update
            $this->logAudit('update', 'nurse', $key, $existingData, $updateData);

            return redirect(route('firebase.admin.show_nurse'))->with('success', 'Nurse Updated Successfully');
        } else {
            return redirect(route('firebase.admin.show_nurse'))->with('error', 'Nurse Not Updated');
        }
    }


    //delete nurse
    public function deletenurse($id){
        $key = $id;

        // Get existing data before deletion
        $existingData = $this->database->getReference($this->tablenurse . '/' . $key)->getValue();

        $del_data = $this->database->getReference($this->tablenurse.'/'.$key)->remove();
        if($del_data){
            // Log the audit for deletion
            $this->logAudit('delete', 'nurse', $key, $existingData, null);

            return redirect(route('firebase.admin.show_nurse'))->with('success','Nurse Deleted Successfully');
        }
        else{
            return redirect(route('firebase.admin.show_nurse'))->with('error','Nurse Not Deleted');
        }
    }

    //registration page pharmacist
    public function reg_pharmacist(){
        return view('firebase.admin.reg_pharmacist');
    }

    // store pharmacist
    public function store_pharmacist(Request $request)
    {
        \Log::info('try1');
        // Validate input fields
        $request->validate([
            'numberMMC' => 'required|numeric|digits:5', // Enforce exactly 5 digits
            'numberAPC' => 'required|numeric|digits:5', // Enforce exactly 5 digits
        ]);

        // Prepare data to store
        \Log::info('try2');
        $postData = [
            'fname' => $request->fullname,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'numberMMC' => $request->numberMMC,
            'numberAPC' => $request->numberAPC,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => session('admin_name', 'Admin'),
        ];

        \Log::info('try3');
        // Store data in Firebase
        $postRef = $this->database->getReference($this->tablepharmacist)->push($postData);

        // Check if data is stored successfully
        if ($postRef) {
            // Log the audit for creation
            $staffId = $postRef->getKey();
            $this->logAudit('create', 'pharmacist', $staffId, null, $postData);

            return redirect(route('firebase.admin.reg_pharmacist'))->with('success', 'Pharmacist Added Successfully');
        } else {
            return redirect(route('firebase.admin.reg_pharmacist'))->with('error', 'Pharmacist Not Added');
        }
    }

    // show pharmacist database
    public function show_pharmacist()
    {
        // Fetch all pharmacist from Firebase
        $pharmacists = $this->database->getReference($this->tablepharmacist)->getValue();
        // Pass the data to the view
        return view('firebase.admin.show_pharmacist', compact('pharmacists'));
    }

    //edit pharmacist
    public function edit_pharmacist($id){
        $key = $id;
        $editdata = $this->database->getReference($this->tablepharmacist)->getChild($key)->getValue();

        if (!$editdata) {
            return redirect('show_pharmacist')->with('status', 'Pharmacist ID not Found');
        }

        // Fetch audit logs for this pharmacist
        $auditLogs = [];

        try {
            // Query the audit logs where staff_id equals our pharmacist ID
            $logsRef = $this->database->getReference($this->auditLogTable)
                ->orderByChild('staff_id')
                ->equalTo($key)
                ->getValue();

            if ($logsRef) {
                foreach ($logsRef as $logKey => $logData) {
                    if ($logData['staff_type'] === 'pharmacist') {
                        $logData['key'] = $logKey;
                        $auditLogs[$logKey] = $logData;
                    }
                }

                // Sort logs by timestamp (most recent first)
                uasort($auditLogs, function($a, $b) {
                    return strtotime($b['timestamp']) - strtotime($a['timestamp']);
                });
            }
        } catch (\Exception $e) {
            \Log::warning('Error fetching audit logs: ' . $e->getMessage());
            // Continue without audit logs rather than failing
        }

        return view('firebase.admin.edit_pharmacist', compact('editdata', 'key', 'auditLogs'));
    }

    // update pharmacist
    public function updatepharmacist(Request $request, $id)
    {
        $key = $id;

        // Fetch the existing data for this pharmacist
        $existingData = $this->database->getReference($this->tablepharmacist . '/' . $key)->getValue();

        // Prepare the update data
        $updateData = [
            'fname' => $request->fullname,
            'email' => $request->email,
            'phone' => $request->phone,
            'numberMMC' => $request->numberMMC,
            'numberAPC' => $request->numberAPC,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => session('admin_name', 'Admin'),
        ];

        // Only hash the password if it has been changed
        if ($request->password && $request->password !== $existingData['password']) {
            $updateData['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        } else {
            // Keep the existing password if not changed
            $updateData['password'] = $existingData['password'];
        }

        // Update the data in the database
        $res_updated = $this->database->getReference($this->tablepharmacist . '/' . $key)->update($updateData);

        if ($res_updated) {
            // Log the audit for update
            $this->logAudit('update', 'pharmacist', $key, $existingData, $updateData);

            return redirect(route('firebase.admin.show_pharmacist'))->with('success', 'Pharmacist Updated Successfully');
        } else {
            return redirect(route('firebase.admin.show_pharmacist'))->with('error', 'Pharmacist Not Updated');
        }
    }


    //delete pharmacist
    public function deletepharmacist($id){
        $key = $id;

        // Get existing data before deletion
        $existingData = $this->database->getReference($this->tablepharmacist . '/' . $key)->getValue();

        $del_data = $this->database->getReference($this->tablepharmacist.'/'.$key)->remove();
        if($del_data){
            // Log the audit for deletion
            $this->logAudit('delete', 'pharmacist', $key, $existingData, null);

            return redirect(route('firebase.admin.show_pharmacist'))->with('success','Pharmacist Deleted Successfully');
        }
        else{
            return redirect(route('firebase.admin.show_pharmacist'))->with('error','Pharmacist Not Deleted');
        }
    }

    /**
     * Show the audit log page
     *
     * @return \Illuminate\View\View
     */
    public function auditLogs(Request $request)
    {
        try {
            // Get filter parameters
            $staffType = $request->input('staff_type', 'all');
            $action = $request->input('action', 'all');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            // Get logs from Firebase
            $logs = $this->database->getReference($this->auditLogTable)->getValue() ?: [];

            // Reverse order to show newest first
            $logs = array_reverse($logs, true);

            // Filter by staff type and action if needed
            if ($staffType !== 'all' || $action !== 'all' || $startDate || $endDate) {
                $logs = array_filter($logs, function($log) use ($staffType, $action, $startDate, $endDate) {
                    $typeMatch = $staffType === 'all' || ($log['staff_type'] ?? '') === $staffType;
                    $actionMatch = $action === 'all' || ($log['action'] ?? '') === $action;

                    $dateMatch = true;
                    if ($startDate && $endDate) {
                        $logTime = strtotime($log['timestamp'] ?? '0');
                        $startTime = strtotime($startDate . ' 00:00:00');
                        $endTime = strtotime($endDate . ' 23:59:59');
                        $dateMatch = $logTime >= $startTime && $logTime <= $endTime;
                    }

                    return $typeMatch && $actionMatch && $dateMatch;
                });
            }

            return view('firebase.admin.audit_logs', [
                'logs' => $logs,
                'staffType' => $staffType,
                'action' => $action,
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading audit logs: ' . $e->getMessage());
            return back()->with('error', 'Error loading audit logs: ' . $e->getMessage());
        }
    }

    /**
     * Fix Firebase Database Rules
     *
     * This function is for fixing the error with Firebase Rules
     * It's meant to be called once to set the indexOn property for audit logs
     */
    public function fixFirebaseRules()
    {
        try {
            // Create an index on 'staff_id' for faster searches
            $rules = [
                'audit_logs' => [
                    '.indexOn' => ['staff_id', 'staff_type', 'timestamp']
                ]
            ];

            // Actually setting the rules requires Firebase Admin SDK and might not be
            // possible via the standard client library
            return back()->with('success', 'Firebase rules should be updated manually in the Firebase console. Add ".indexOn": ["staff_id", "staff_type", "timestamp"] for path "/audit_logs".');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
