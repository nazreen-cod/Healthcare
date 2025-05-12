<?php

namespace App\Http\Controllers\Firebase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;
use Illuminate\Support\Facades\Hash;

class Doctorcontroller extends Controller
{
    protected $database;
    protected $tablenames;
    protected $medical_report;
    protected $merge_patient;
    protected $full_patientdata;

    protected $appointments;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->tablenames = 'doctor'; // Ensure this matches your Firebase structure
        $this->medical_report = 'medical_report';
        $this->merge_patient = 'merge_patient'; // Ensure this matches your Firebase structure
        $this->full_patientdata = 'full_patientdata';
        $this->appointments = 'appointments'; // Add this new property for app
    }

    // Display the doctor login page
    public function index()
    {
        return view('firebase.doctor.index');
    }

    public function dashboard()
    {
        try {
            // Retrieve the authenticated doctor's ID
            $doctorId = session('doctor_id');

            if (!$doctorId) {
                return redirect()->route('firebase.doctor.index')->with('error', 'You must log in first.');
            }

            // Fetch the doctor's data from Firebase
            $doctorData = $this->database->getReference($this->tablenames . '/' . $doctorId)->getValue();

            if (!$doctorData) {
                return redirect()->route('firebase.doctor.index')->with('error', 'Doctor not found.');
            }

            $fname = $doctorData['fname'] ?? 'Doctor';

            // Get current date
            $today = date('Y-m-d');

            // Fetch appointments from Firebase
            $appointmentsRef = $this->database->getReference('appointments');
            $appointments = $appointmentsRef->getValue() ?: [];

            // Initialize counters
            $appointmentsToday = 0;
            $pendingAppointments = 0;
            $acceptedAppointments = 0;
            $cancelledAppointments = 0;
            $recentAppointments = [];
            $todaysAppointments = []; // Array specifically for today's appointments

            // Process appointments
            foreach ($appointments as $id => $appointment) {
                // Only include appointments for this doctor
                if (isset($appointment['doctorId']) && $appointment['doctorId'] === $doctorId) {

                    // Get patient name
                    $patientName = 'Unknown Patient';
                    $patientPhone = 'N/A';
                    if (isset($appointment['userId'])) {
                        $patientRef = $this->database->getReference('merge_patient/' . $appointment['userId']);
                        $patientData = $patientRef->getValue();
                        if ($patientData && isset($patientData['name'])) {
                            $patientName = $patientData['name'];
                            $patientPhone = $patientData['contact'] ?? 'N/A';
                        }
                    }

                    // Format appointment data
                    $formattedAppointment = [
                        'id' => $id,
                        'date' => $appointment['date'] ?? '',
                        'time' => $appointment['time'] ?? 'Not specified',
                        'patientName' => $patientName,
                        'patientPhone' => $patientPhone,
                        'status' => $appointment['status'] ?? 'pending',
                        'notes' => $appointment['notes'] ?? '',
                    ];

                    // Save to recent appointments list (all upcoming appointments)
                    $recentAppointments[] = $formattedAppointment;

                    // If today's appointment, add to today's list
                    if (isset($appointment['date']) && $appointment['date'] === $today) {
                        $todaysAppointments[] = $formattedAppointment;
                        $appointmentsToday++;

                        // Count by status
                        $status = $appointment['status'] ?? 'pending';
                        if ($status === 'pending') {
                            $pendingAppointments++;
                        } elseif ($status === 'accepted') {
                            $acceptedAppointments++;
                        } elseif ($status === 'cancelled' || $status === 'declined') {
                            $cancelledAppointments++;
                        }
                    }
                }
            }

            // Sort recent appointments by date and time
            usort($recentAppointments, function($a, $b) {
                $dateTimeA = strtotime($a['date'] . ' ' . $a['time']);
                $dateTimeB = strtotime($b['date'] . ' ' . $b['time']);
                return $dateTimeA - $dateTimeB; // Ascending (closest first)
            });

            // Sort today's appointments by time
            usort($todaysAppointments, function($a, $b) {
                return strtotime($a['time']) - strtotime($b['time']); // Sort by time ascending
            });

            // Limit recent appointments to 5
            $recentAppointments = array_slice($recentAppointments, 0, 5);

            // Log the values for debugging
            \Log::info('Doctor dashboard - Appointment counts:', [
                'doctorId' => $doctorId,
                'appointmentsToday' => $appointmentsToday,
                'pendingAppointments' => $pendingAppointments,
                'acceptedAppointments' => $acceptedAppointments,
                'cancelledAppointments' => $cancelledAppointments,
                'todaysAppointmentCount' => count($todaysAppointments)
            ]);

            // Pass data to view including appointments data
            return view('firebase.doctor.dashboard', compact(
                'fname',
                'appointmentsToday',
                'pendingAppointments',
                'acceptedAppointments',
                'cancelledAppointments',
                'recentAppointments',
                'todaysAppointments'
            ));

        } catch (\Exception $e) {
            \Log::error('Error loading doctor dashboard: ' . $e->getMessage());
            return view('firebase.doctor.dashboard', [
                'fname' => session('doctor_name', 'Doctor'),
                'appointmentsToday' => 0,
                'pendingAppointments' => 0,
                'acceptedAppointments' => 0,
                'cancelledAppointments' => 0,
                'recentAppointments' => [],
                'todaysAppointments' => []
            ])->with('error', 'Error loading dashboard data: ' . $e->getMessage());
        }
    }
    /**
     * Helper function to get patient name from patient ID
     */

    private function getPatientName($patientId)
    {
        if (empty($patientId)) {
            return 'Unknown Patient';
        }

        $patientRef = $this->database->getReference('merge_patient/' . $patientId);
        $patientData = $patientRef->getValue();

        return $patientData['name'] ?? 'Unknown Patient';
    }

    /**
     * Update appointment status (accept/decline)
     */
    public function updateAppointmentStatus(Request $request, $appointmentId)
    {
        try {
            // Validate request
            $request->validate([
                'status' => 'required|in:accepted,declined,cancelled,completed'
            ]);

            $doctorId = session('doctor_id');
            if (!$doctorId) {
                return redirect()->route('firebase.doctor.index')->with('error', 'You must log in first.');
            }

            // Get the appointment
            $appointmentRef = $this->database->getReference('appointments/' . $appointmentId);
            $appointment = $appointmentRef->getValue();

            if (!$appointment) {
                return redirect()->back()->with('error', 'Appointment not found.');
            }

            // Verify this is the doctor's appointment
            if ($appointment['doctorId'] !== $doctorId) {
                return redirect()->back()->with('error', 'You are not authorized to update this appointment.');
            }

            // Update the status
            $appointmentRef->update([
                'status' => $request->status,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return redirect()->back()->with('success', 'Appointment status updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Error updating appointment status: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error updating appointment: ' . $e->getMessage());
        }
    }
    public function appointments()
    {
        try {
            // Get doctor ID from session
            $doctorId = session('doctor_id');
            if (!$doctorId) {
                return redirect()->route('firebase.doctor.index')->with('error', 'You must log in first.');
            }

            // Get doctor info
            $doctorData = $this->database->getReference($this->tablenames . '/' . $doctorId)->getValue();
            $fname = $doctorData['fname'] ?? 'Doctor';

            // Get current date
            $today = date('Y-m-d');
            $tomorrow = date('Y-m-d', strtotime('+1 day'));

            // Get all appointments
            $appointmentsRef = $this->database->getReference('appointments');
            $appointments = $appointmentsRef->getValue() ?: [];

            // Filter appointments for this specific doctor
            $doctorAppointments = [];
            $appointmentsToday = 0;
            $pendingAppointments = 0;
            $acceptedAppointments = 0;
            $cancelledAppointments = 0;

            foreach ($appointments as $id => $appointment) {
                // Skip if required fields are missing
                if (!isset($appointment['doctorId'], $appointment['date'])) {
                    continue;
                }

                // Only include appointments assigned to this doctor
                if ($appointment['doctorId'] === $doctorId) {
                    // Format the appointment data
                    $appointmentData = [
                        'id' => $id,
                        'date' => $appointment['date'],
                        'time' => $appointment['time'] ?? 'Not specified',
                        'status' => $appointment['status'] ?? 'pending',
                        'patientId' => $appointment['userId'] ?? null,
                        'patientName' => 'Unknown Patient',
                        'patientPhone' => 'N/A',
                        'notes' => $appointment['notes'] ?? '',
                        'timestamp' => strtotime($appointment['date'] . ' ' . ($appointment['time'] ?? '00:00')),
                        'is_today' => $appointment['date'] === $today,
                        'is_tomorrow' => $appointment['date'] === $tomorrow
                    ];

                    // Get patient information if userId exists
                    if (isset($appointment['userId'])) {
                        $patientRef = $this->database->getReference('merge_patient/' . $appointment['userId']);
                        $patientData = $patientRef->getValue();

                        if ($patientData) {
                            $appointmentData['patientName'] = $patientData['name'] ?? 'Unknown Patient';
                            $appointmentData['patientPhone'] = $patientData['contact'] ?? 'N/A';
                        }
                    }

                    // Count appointments by status for today
                    if ($appointment['date'] === $today) {
                        $appointmentsToday++;

                        $status = $appointment['status'] ?? 'pending';
                        if ($status === 'pending') {
                            $pendingAppointments++;
                        } elseif ($status === 'accepted') {
                            $acceptedAppointments++;
                        } elseif ($status === 'cancelled' || $status === 'declined') {
                            $cancelledAppointments++;
                        }
                    }

                    $doctorAppointments[$id] = $appointmentData;
                }
            }

            // Sort appointments by date and time
            uasort($doctorAppointments, function($a, $b) {
                // First sort by date
                $dateComparison = strcmp($a['date'], $b['date']);
                if ($dateComparison !== 0) {
                    return $dateComparison;
                }

                // If same date, sort by time
                return strcmp($a['time'], $b['time']);
            });

            // Group appointments by date for easier display
            $groupedAppointments = [];
            foreach ($doctorAppointments as $id => $appointment) {
                $groupedAppointments[$appointment['date']][$id] = $appointment;
            }

            return view('firebase.doctor.appointments', compact(
                'groupedAppointments',
                'fname',
                'appointmentsToday',
                'pendingAppointments',
                'acceptedAppointments',
                'cancelledAppointments'
            ));

        } catch (\Exception $e) {
            \Log::error('Error loading doctor appointments: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading appointments: ' . $e->getMessage());
        }
    }


    public function logout()
    {
        session()->flush(); // Clear all session data
        return redirect(route('firebase.doctor.logindoctor'))->with('success', 'Logged out successfully.');
    }

// Handle doctor login
    public function logindoctor(Request $request)
    {
        \Log::info('doctor login initiated', $request->all());

        // Validate input fields
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try {
            // Fetch all doctor data from Firebase
            $doctors = $this->database->getReference($this->tablenames)->getValue();
            \Log::info('doctors fetched from Firebase', ['doctor' => $doctors]);
            \Log::info('checkpoint A');

            // Check if doctors data is available and valid
            if (!$doctors || !is_array($doctors)) {
                return redirect()->back()->with('error', 'Invalid email or password.');
            }
            \Log::info('checkpoint B');
            foreach ($doctors as $doctorId => $doctorData) {
                \Log::info('Checking doctor record:', ['doctorId' => $doctorId, 'doctorData' => $doctorData]);

                \Log::info('checkpoint C');
                // Validate structure and match email
                if (
                    isset($doctorData['email'], $doctorData['password']) &&
                    $doctorData['email'] === $request->input('email') &&
                    \Illuminate\Support\Facades\Hash::check($request->input('password'), $doctorData['password'])
                ) {
                    \Log::info('doctor authenticated successfully', ['doctor_id' => $doctorId]);


                    // Store session data for doctor
                    session([
                        'doctor_id' => $doctorId,
                        'doctor_email' => $doctorData['email'],
                        'doctor_name' => $doctorData['fname'],
                    ]);
                    \Log::info('checkpoint D');

                    return redirect(route('firebase.doctor.dashboard'))->with('success', 'Welcomeback!');
                }
            }
            \Log::info('checkpoint E');
            // If no matching doctor found
            return redirect()->back()->with('error', 'Invalid email or password.');
        } catch (\Exception $e) {
            \Log::info('checkpoint F');
            // Log the exception for debugging
            \Log::error('doctor login error:', [
                'exception' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'An error occurred during login. Please try again.');
        }
    }

    public function medical_report(Request $request)
    {
        try {
            // Get the patient ID from the request
            $patientId = $request->input('id');
            $searchQuery = session('searchQuery');  // Retrieving the search query stored in session

            // Initialize patients array
            $patients = [];

            // ✅ Reference to the merge_patient node
            $patientsRef = $this->database->getReference($this->merge_patient);

            // Check if we have a direct patient ID
            if ($patientId) {
                // Get the specific patient data
                $patientData = $patientsRef->getChild($patientId)->getValue();

                if ($patientData) {
                    // Capture all patient data
                    $processedData = ['id' => $patientId];

                    // Add all fields from the patient record
                    foreach ($patientData as $field => $value) {
                        $processedData[$field] = $value;
                    }

                    $patients[$patientId] = $processedData;
                } else {
                    return redirect()->route('firebase.doctor.searchPatient')
                        ->with('error', 'Patient not found. Please try searching again.');
                }
            }
            // If no specific ID, use the search query
            elseif ($searchQuery) {
                \Log::info('Searching for patients with query: ' . $searchQuery);

                // Get all patients from Firebase
                $patientsSnapshot = $patientsRef->getValue() ?: [];

                foreach ($patientsSnapshot as $key => $patient) {
                    // Check if Name or ID Number matches search query
                    if (
                        (isset($patient['name']) && stripos($patient['name'], $searchQuery) !== false) ||
                        (isset($patient['id_no']) && stripos($patient['id_no'], $searchQuery) !== false) ||
                        (isset($patient['visit_no']) && stripos($patient['visit_no'], $searchQuery) !== false)
                    ) {
                        // Capture all fields from the patient record
                        $processedData = ['id' => $key];

                        // Add all fields dynamically
                        foreach ($patient as $field => $value) {
                            $processedData[$field] = $value;
                        }

                        $patients[$key] = $processedData;
                    }
                }
            } else {
                return redirect()->route('firebase.doctor.searchPatient')
                    ->with('error', 'No search criteria found. Please search for a patient first.');
            }

            // If no matching patients found
            if (empty($patients)) {
                return redirect()->route('firebase.doctor.searchPatient')
                    ->with('error', 'No patients found matching your search criteria.');
            }

            // Check for existing medical reports
            $fullPatientRef = $this->database->getReference($this->full_patientdata);
            $existingReports = $fullPatientRef->getValue() ?: [];

            // For each patient in our results, check if they have an existing medical report
            foreach ($patients as $pId => &$patientData) {
                if (isset($patientData['id_no'])) {
                    $patientIdNo = $patientData['id_no'];

                    foreach ($existingReports as $reportId => $reportData) {
                        if (isset($reportData['id_no']) && $reportData['id_no'] === $patientIdNo) {
                            $patientData['has_report'] = true;
                            $patientData['report_id'] = $reportId;
                            $patientData['existing_report'] = $reportData;
                            $patientData['report_date'] = $reportData['report_date'] ?? date('Y-m-d');
                            break;
                        }
                    }
                }
            }

            // Get additional medical data if available
            foreach ($patients as $pId => &$patientData) {
                // Add calculated fields or enriched data

                // Add vital signs status indicators
                $patientData['vital_signs'] = [];

                // Blood pressure analysis
                if (isset($patientData['bloodpm'])) {
                    $bpParts = explode('/', $patientData['bloodpm']);
                    $systolic = isset($bpParts[0]) ? (int)trim($bpParts[0]) : 0;
                    $diastolic = isset($bpParts[1]) ? (int)trim(explode(' ', $bpParts[1])[0]) : 0;

                    $bpStatus = 'Normal';
                    if ($systolic >= 180 || $diastolic >= 120) {
                        $bpStatus = 'Hypertensive Crisis';
                    } elseif ($systolic >= 140 || $diastolic >= 90) {
                        $bpStatus = 'Stage 2 Hypertension';
                    } elseif ($systolic >= 130 || $diastolic >= 80) {
                        $bpStatus = 'Stage 1 Hypertension';
                    } elseif ($systolic >= 120 && $systolic < 130 && $diastolic < 80) {
                        $bpStatus = 'Elevated';
                    }

                    $patientData['vital_signs']['blood_pressure'] = [
                        'value' => $patientData['bloodpm'],
                        'systolic' => $systolic,
                        'diastolic' => $diastolic,
                        'status' => $bpStatus
                    ];
                }

                // Temperature analysis
                if (isset($patientData['tempreturec'])) {
                    $tempParts = explode(' ', $patientData['tempreturec']);
                    $tempValue = isset($tempParts[0]) ? (float)trim($tempParts[0]) : 0;

                    $tempStatus = 'Normal';
                    if ($tempValue >= 39.5) {
                        $tempStatus = 'High Fever';
                    } elseif ($tempValue >= 38.0) {
                        $tempStatus = 'Fever';
                    } elseif ($tempValue >= 37.5) {
                        $tempStatus = 'Mild Fever';
                    } elseif ($tempValue < 36.0) {
                        $tempStatus = 'Hypothermia';
                    }

                    $patientData['vital_signs']['temperature'] = [
                        'value' => $patientData['tempreturec'],
                        'numeric' => $tempValue,
                        'status' => $tempStatus
                    ];
                }

                // Pulse rate analysis
                if (isset($patientData['plusec'])) {
                    $pulseParts = explode(' ', $patientData['plusec']);
                    $pulseValue = isset($pulseParts[0]) ? (int)trim($pulseParts[0]) : 0;

                    $pulseStatus = 'Normal';
                    if ($pulseValue > 100) {
                        $pulseStatus = 'Tachycardia';
                    } elseif ($pulseValue < 60) {
                        $pulseStatus = 'Bradycardia';
                    }

                    $patientData['vital_signs']['pulse'] = [
                        'value' => $patientData['plusec'],
                        'numeric' => $pulseValue,
                        'status' => $pulseStatus
                    ];
                }

                // Respiratory rate analysis
                if (isset($patientData['respiratingr'])) {
                    $respParts = explode(' ', $patientData['respiratingr']);
                    $respValue = isset($respParts[0]) ? (int)trim($respParts[0]) : 0;

                    $respStatus = 'Normal';
                    if ($respValue > 20) {
                        $respStatus = 'Tachypnea';
                    } elseif ($respValue < 12) {
                        $respStatus = 'Bradypnea';
                    }

                    $patientData['vital_signs']['respiratory_rate'] = [
                        'value' => $patientData['respiratingr'],
                        'numeric' => $respValue,
                        'status' => $respStatus
                    ];
                }

                // BMI analysis
                if (isset($patientData['bmi'])) {
                    $bmiValue = (float)$patientData['bmi'];

                    $bmiStatus = isset($patientData['bmi_status']) ? $patientData['bmi_status'] : '';
                    if (!$bmiStatus) {
                        if ($bmiValue >= 30) {
                            $bmiStatus = 'Obese';
                        } elseif ($bmiValue >= 25) {
                            $bmiStatus = 'Overweight';
                        } elseif ($bmiValue >= 18.5) {
                            $bmiStatus = 'Normal';
                        } else {
                            $bmiStatus = 'Underweight';
                        }
                    }

                    $patientData['vital_signs']['bmi'] = [
                        'value' => $bmiValue,
                        'status' => $bmiStatus
                    ];
                }

                // Medical history if available from other sources
                if (isset($patientData['medical_history']) && empty($patientData['medical_history'])) {
                    // Check for chronic conditions and add to medical history
                    $chronicConditions = [];
                    if (isset($patientData['chronic_conditions'])) {
                        $chronicConditions[] = "Chronic conditions: " . $patientData['chronic_conditions'];
                    }

                    // Add other relevant medical history
                    if (!empty($chronicConditions)) {
                        $patientData['medical_history'] = implode("\n", $chronicConditions);
                    }
                }

                // If we have an existing report, pre-fill the form fields
                if (isset($patientData['existing_report'])) {
                    $reportData = $patientData['existing_report'];

                    // Set form values from existing report
                    $patientData['form_data'] = [
                        'medical_history' => $reportData['medical_history'] ?? '',
                        'family_history' => $reportData['family_history'] ?? '',
                        'clinical_summary' => $reportData['clinical_summary'] ?? '',
                        'admission_date' => $reportData['admission_date'] ?? '',
                        'principal_doctor' => $reportData['principal_doctor'] ?? '',
                        'reason_for_admission' => $reportData['reason_for_admission'] ?? '',
                        'principal_diagnosis' => $reportData['principal_diagnosis'] ?? '',
                        'secondary_diagnosis' => $reportData['secondary_diagnosis'] ?? '',
                        'other_diagnosis' => $reportData['other_diagnosis'] ?? '',
                        'operation_procedures' => $reportData['operation_procedures'] ?? '',
                        'discharge_date' => $reportData['discharge_date'] ?? '',
                        'condition_at_discharge' => $reportData['condition_at_discharge'] ?? ''
                    ];
                }
            }

            \Log::info('Medical report data prepared for ' . count($patients) . ' patients');

            // Pass all patient data to the view
            return view('firebase.doctor.medical_report', [
                'patients' => $patients,
                'hasExistingReport' => isset($patientData['has_report']) && $patientData['has_report']
            ]);

        } catch (\Exception $e) {
            \Log::error('Error generating medical report: ' . $e->getMessage());
            return redirect()->route('firebase.doctor.searchPatient')
                ->with('error', 'Error generating medical report: ' . $e->getMessage());
        }
    }

    // Handle form submission
    public function storeMedicalReport(Request $request)
    {
        // Validate the input fields
        $validatedData = $request->validate([
            'medical_history' => 'nullable|string',
            'family_history' => 'nullable|string',
            'clinical_summary' => 'nullable|string',

            //<-- other features database -->
//        'admission_date' => 'nullable|date',
//        'principal_doctor' => 'nullable|string|max:255',
//        'reason_for_admission' => 'nullable|string',
//        'principal_diagnosis' => 'nullable|string',
//        'secondary_diagnosis' => 'nullable|string',
//        'other_diagnosis' => 'nullable|string',
//        'operation_procedures' => 'nullable|string',
//        'discharge_date' => 'nullable|date',
//        'condition_at_discharge' => 'nullable|string',
        ]);

        // Prepare medical report data
        $medicalReportData = [
            'medical_history' => $request->medical_history,
            'family_history' => $request->family_history,
            'clinical_summary' => $request->clinical_summary,

            //<-- other features database -->
//        'admission_date' => $request->admission_date,
//        'principal_doctor' => $request->principal_doctor,
//        'reason_for_admission' => $request->reason_for_admission,
//        'principal_diagnosis' => $request->principal_diagnosis,
//        'secondary_diagnosis' => $request->secondary_diagnosis,
//        'other_diagnosis' => $request->other_diagnosis,
//        'operation_procedures' => $request->operation_procedures,
//        'discharge_date' => $request->discharge_date,
//        'condition_at_discharge' => $request->condition_at_discharge,
        ];

        // Retrieve the patient data from Firebase (merge_patient reference)
        $patientRef = $this->database->getReference($this->merge_patient);
        $patientSnapshot = $patientRef->getValue();

        if ($patientSnapshot) {
            // Find the patient to merge the data with (we assume $request->patient_id is provided in the form)
            $patientId = $request->input('patient_id');  // You need to pass this from the form
            if (isset($patientSnapshot[$patientId])) {
                $patientData = $patientSnapshot[$patientId];

                // Merge patient data with the medical report data
                $mergedData = array_merge($patientData, $medicalReportData);

                // Log the merged data for debugging
                \Log::info('Merged Data:', $mergedData);

                // Store the merged data in the full_patientdata table
                $fullPatientDataRef = $this->database->getReference('full_patientdata');
                $postRef = $fullPatientDataRef->push($mergedData);

                // Check if data was successfully stored
                if ($postRef) {
                    return redirect(route('firebase.doctor.storeMedicalReport'))->with('success', 'Medical report added successfully');
                } else {
                    return redirect(route('firebase.doctor.storeMedicalReport'))->with('error', 'Failed to add medical report');
                }
            } else {
                return redirect(route('firebase.doctor.storeMedicalReport'))->with('error', 'Patient not found');
            }
        } else {
            return redirect(route('firebase.doctor.storeMedicalReport'))->with('error', 'No patient data found');
        }
    }



    public function search()
    {
        try {
            // Get metrics for dashboard
            $todayAppointments = 0;
            $pendingPatients = 0;
            $totalPatients = 0;
            $recentPatients = [];

            // Get total patient count
            $patientsRef = $this->database->getReference($this->merge_patient);
            $allPatients = $patientsRef->getValue() ?: [];
            $totalPatients = count($allPatients);

            // Count today's appointments if applicable
            $today = now()->format('Y-m-d');
            $appointmentsRef = $this->database->getReference('appointments');
            $appointments = $appointmentsRef->getValue() ?: [];

            foreach ($appointments as $appt) {
                if (isset($appt['date']) && $appt['date'] === $today) {
                    $todayAppointments++;
                }
            }

            // Get patients who need medical reports
            $fullPatientRef = $this->database->getReference($this->full_patientdata);
            $fullPatients = $fullPatientRef->getValue() ?: [];

            // Create a map of processed patient IDs with reports
            $processedPatientIds = [];
            foreach ($fullPatients as $key => $fullPatient) {
                if (isset($fullPatient['id_no'])) {
                    $processedPatientIds[$fullPatient['id_no']] = [
                        'report_id' => $key,
                        'report_date' => $fullPatient['report_date'] ?? date('Y-m-d')
                    ];
                }
            }

            // Find patients without reports and recent patients
            $patientsWithScreening = [];
            foreach ($allPatients as $key => $patient) {
                // Only count patients with screening data who need reports
                if (isset($patient['id_no']) && isset($patient['bloodpm']) && !isset($processedPatientIds[$patient['id_no']])) {
                    $pendingPatients++;
                    $patientsWithScreening[] = [
                        'id' => $key,
                        'name' => $patient['name'] ?? 'Unknown',
                        'id_no' => $patient['id_no'] ?? 'N/A',
                        'gender' => $patient['gender'] ?? 'N/A',
                        'age' => $patient['age'] ?? 'N/A',
                        'screening_date' => $patient['screening_date'] ?? 'N/A',
                        'screening_time' => $patient['screening_time'] ?? 'N/A'
                    ];
                }
            }

            // Sort patients by screening date (most recent first) if available
            usort($patientsWithScreening, function($a, $b) {
                $dateA = isset($a['screening_date']) ? strtotime($a['screening_date'] . ' ' . ($a['screening_time'] ?? '00:00:00')) : 0;
                $dateB = isset($b['screening_date']) ? strtotime($b['screening_date'] . ' ' . ($b['screening_time'] ?? '00:00:00')) : 0;
                return $dateB - $dateA; // Descending order (most recent first)
            });

            // Take only the first 5 patients for the dashboard
            $recentPatients = array_slice($patientsWithScreening, 0, 5);

            // Return the view with dashboard metrics
            return view('firebase.doctor.search_patient', compact('todayAppointments', 'pendingPatients', 'totalPatients', 'recentPatients'));
        } catch (\Exception $e) {
            \Log::error('Error loading search dashboard: ' . $e->getMessage());
            return view('firebase.doctor.search_patient')->with('error', 'Error loading dashboard data: ' . $e->getMessage());
        }
    }

    public function searchPatient(Request $request)
    {
        try {
            \Log::info('Search started');

            // Validate input with support for search type
            $request->validate([
                'search' => 'required|string|max:255',
                'search_type' => 'nullable|string|in:all,name,id_no,visit_no'
            ]);

            $searchQuery = trim($request->input('search'));
            $searchType = $request->input('search_type', 'all');

            // Store search query in session for other methods
            session(['searchQuery' => $searchQuery]);

            // Get reference to merged patient data
            $patientsRef = $this->database->getReference($this->merge_patient);

            // Get reference to full patient data to check for existing reports
            $fullPatientRef = $this->database->getReference($this->full_patientdata);
            $fullPatients = $fullPatientRef->getValue() ?: [];

            // Create a map of processed patient IDs with reports
            $processedPatientIds = [];
            foreach ($fullPatients as $key => $fullPatient) {
                if (isset($fullPatient['id_no'])) {
                    $processedPatientIds[$fullPatient['id_no']] = [
                        'report_id' => $key,
                        'report_date' => $fullPatient['report_date'] ?? date('Y-m-d')
                    ];
                }
            }

            $patients = [];
            \Log::info('Querying Firebase Realtime Database');

            // 🔍 Fetch all patients from Firebase
            $patientsSnapshot = $patientsRef->getValue();

            if ($patientsSnapshot) {
                foreach ($patientsSnapshot as $key => $patient) {
                    $matchFound = false;

                    // Determine search match based on search type
                    switch ($searchType) {
                        case 'name':
                            $matchFound = isset($patient['name']) && stripos($patient['name'], $searchQuery) !== false;
                            break;
                        case 'id_no':
                            $matchFound = isset($patient['id_no']) && stripos($patient['id_no'], $searchQuery) !== false;
                            break;
                        case 'visit_no':
                            $matchFound = isset($patient['visit_no']) && stripos($patient['visit_no'], $searchQuery) !== false;
                            break;
                        default: // 'all'
                            $matchFound =
                                (isset($patient['name']) && stripos($patient['name'], $searchQuery) !== false) ||
                                (isset($patient['id_no']) && stripos($patient['id_no'], $searchQuery) !== false) ||
                                (isset($patient['visit_no']) && stripos($patient['visit_no'], $searchQuery) !== false);
                            break;
                    }

                    if ($matchFound) {
                        // Check if this patient has a medical report
                        $hasReport = false;
                        $reportId = null;
                        $reportDate = null;

                        if (isset($patient['id_no']) && isset($processedPatientIds[$patient['id_no']])) {
                            $hasReport = true;
                            $reportId = $processedPatientIds[$patient['id_no']]['report_id'];
                            $reportDate = $processedPatientIds[$patient['id_no']]['report_date'];
                        }

                        // Include all available fields from the merge_patient table
                        $patientData = [
                            'id' => $key,
                            'has_report' => $hasReport,
                            'report_id' => $reportId,
                            'report_date' => $reportDate
                        ];

                        // Dynamically include ALL fields from the patient record
                        foreach ($patient as $field => $value) {
                            $patientData[$field] = $value;
                        }

                        // Add to results array
                        $patients[$key] = $patientData;
                    }
                }

                // Sort patients by screening date/time if available
                uasort($patients, function($a, $b) {
                    $dateTimeA = isset($a['screening_date']) ? ($a['screening_date'] . ' ' . ($a['screening_time'] ?? '00:00:00')) : '';
                    $dateTimeB = isset($b['screening_date']) ? ($b['screening_date'] . ' ' . ($b['screening_time'] ?? '00:00:00')) : '';

                    // If no screening dates are available, sort by name
                    if (empty($dateTimeA) && empty($dateTimeB)) {
                        return strcasecmp($a['name'] ?? '', $b['name'] ?? '');
                    } elseif (empty($dateTimeA)) {
                        return 1; // B comes first
                    } elseif (empty($dateTimeB)) {
                        return -1; // A comes first
                    }

                    return strtotime($dateTimeB) - strtotime($dateTimeA); // Descending (newest first)
                });
            }

            \Log::info('Search completed with ' . count($patients) . ' results.');

            // Get metrics for dashboard
            $todayAppointments = 0;
            $pendingPatients = 0;
            $totalPatients = count($patientsSnapshot ?: []);

            // Count today's appointments
            $today = now()->format('Y-m-d');
            $appointmentsRef = $this->database->getReference('appointments');
            $appointments = $appointmentsRef->getValue() ?: [];

            foreach ($appointments as $appt) {
                if (isset($appt['date']) && $appt['date'] === $today) {
                    $todayAppointments++;
                }
            }

            // Return the search results view with all the data
            return view('firebase.doctor.search_patient', compact(
                'patients',
                'searchQuery',
                'searchType',
                'todayAppointments',
                'pendingPatients',
                'totalPatients'
            ));
        } catch (\Exception $e) {
            // Log any exceptions and return an error message
            \Log::error('Firebase Search Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Search failed: ' . $e->getMessage());
        }
    }

}





