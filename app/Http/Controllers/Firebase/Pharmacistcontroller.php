<?php

namespace App\Http\Controllers\Firebase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;
use Illuminate\Support\Facades\Hash;

class Pharmacistcontroller extends Controller
{
    protected $database;
    protected $tablepharmacist;
    protected $full_patientdata;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->tablepharmacist = 'pharmacist'; // Ensure this matches your Firebase structure
        $this->full_patientdata = 'full_patientdata';
    }

    // Display the pharmacist login page
    public function index()
    {
        return view('firebase.pharmacist.index');
    }

    public function dashboard()
    {
        try {
            // Retrieve the authenticated pharmacist's ID
            $pharmacistId = session('pharmacist_id');

            if (!$pharmacistId) {
                return redirect()->route('firebase.pharmacist.index')->with('error', 'You must log in first.');
            }

            // Fetch the pharmacist's data from Firebase
            $pharmacistData = $this->database->getReference($this->tablepharmacist . '/' . $pharmacistId)->getValue();

            if (!$pharmacistData) {
                return redirect()->route('firebase.pharmacist.index')->with('error', 'Pharmacist not found.');
            }

            $fname = $pharmacistData['fname'] ?? 'Pharmacist';

            // Get today's date in Y-m-d format
            $today = date('Y-m-d');

            // Count today's patients from appointments table
            $patientsToday = 0;
            $uniquePatients = [];

            // Get appointments for today
            $appointmentsRef = $this->database->getReference('appointments');
            $allAppointments = $appointmentsRef->getValue() ?: [];

            foreach ($allAppointments as $appointment) {
                // Check if the appointment is for today and is either accepted or completed
                if (isset($appointment['date']) && $appointment['date'] === $today &&
                    isset($appointment['status']) && in_array($appointment['status'], ['accepted', 'completed'])) {

                    // Count unique patients
                    if (isset($appointment['userId']) && !in_array($appointment['userId'], $uniquePatients)) {
                        $uniquePatients[] = $appointment['userId'];
                        $patientsToday++;
                    }
                }
            }

            // Count pending prescriptions
            $pendingCount = 0;
            $prescriptionsRef = $this->database->getReference('prescriptions');
            $allPrescriptions = $prescriptionsRef->getValue() ?: [];

            foreach ($allPrescriptions as $prescription) {
                if (isset($prescription['status']) && $prescription['status'] === 'pending') {
                    $pendingCount++;
                }
            }

            // Count medications dispensed today
            $medicationsDispensed = 0;
            $pickupsRef = $this->database->getReference('medication_pickups');
            $allPickups = $pickupsRef->getValue() ?: [];

            foreach ($allPickups as $pickup) {
                if (isset($pickup['picked_up_at']) && strpos($pickup['picked_up_at'], $today) === 0) {
                    if (isset($pickup['medications']) && is_array($pickup['medications'])) {
                        $medicationsDispensed += count($pickup['medications']);
                    } else {
                        $medicationsDispensed++;
                    }
                }
            }

            // Count low stock medications
            $lowStockCount = 0;
            $inventoryRef = $this->database->getReference('medication_inventory');
            $inventory = $inventoryRef->getValue() ?: [];

            foreach ($inventory as $item) {
                if (isset($item['quantity']) && $item['quantity'] <= 10) {
                    $lowStockCount++;
                }
            }

            // Get active pharmacists count
            $activePharmacists = 1; // Default to at least current user
            $pharmacistsRef = $this->database->getReference($this->tablepharmacist);
            $allPharmacists = $pharmacistsRef->getValue() ?: [];

            $activeCount = 0;
            foreach ($allPharmacists as $pharmacist) {
                if (isset($pharmacist['last_login']) && strpos($pharmacist['last_login'], $today) === 0) {
                    $activeCount++;
                }
            }

            if ($activeCount > 0) {
                $activePharmacists = $activeCount;
            }

            return view('firebase.pharmacist.dashboard', compact(
                'fname',
                'patientsToday',
                'pendingCount',
                'medicationsDispensed',
                'lowStockCount',
                'activePharmacists'
            ));

        } catch (\Exception $e) {
            \Log::error('Pharmacist dashboard error: ' . $e->getMessage());
            return view('firebase.pharmacist.dashboard', [
                'fname' => session('pharmacist_name', 'Pharmacist'),
                'patientsToday' => 0,
                'pendingCount' => 0,
                'medicationsDispensed' => 0,
                'lowStockCount' => 0,
                'activePharmacists' => 1
            ])->with('error', 'Error loading dashboard data: ' . $e->getMessage());
        }
    }


    public function logout()
    {
        session()->flush(); // Clear all session data
        return redirect(route('firebase.pharmacist.loginpharmacist'))->with('success', 'Logged out successfully.');
    }

    // Handle pharmacist pharmacist
    public function loginpharmacist(Request $request)
    {
        \Log::info('pharmacist login initiated', $request->all());

        // Validate input fields
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try {
            // Fetch all pharmacist data from Firebase
            $pharmacists = $this->database->getReference($this->tablepharmacist)->getValue();
            \Log::info('pharmacists fetched from Firebase', ['pharmacist' => $pharmacists]);
            \Log::info('checkpoint A');

            // Check if pharmacists data is available and valid
            if (!$pharmacists || !is_array($pharmacists)) {
                return redirect()->back()->with('error', 'Invalid email or password.');
            }
            \Log::info('checkpoint B');
            foreach ($pharmacists as $pharmacistId => $pharmacistData) {
                \Log::info('Checking pharmacist record:', ['pharmacistId' => $pharmacistId, 'pharmacistData' => $pharmacistData]);

                \Log::info('checkpoint C');
                // Validate structure and match email
                if (
                    isset($pharmacistData['email'], $pharmacistData['password']) &&
                    $pharmacistData['email'] === $request->input('email') &&
                    \Illuminate\Support\Facades\Hash::check($request->input('password'), $pharmacistData['password'])
                ) {
                    \Log::info('pharmacist authenticated successfully', ['pharmacist_id' => $pharmacistId]);


                    // Store session data for pharmacist
                    session([
                        'pharmacist_id' => $pharmacistId,
                        'pharmacist_email' => $pharmacistData['email'],
                        'pharmacist_name' => $pharmacistData['fname'],
                    ]);
                    \Log::info('checkpoint D');

                    return redirect(route('firebase.pharmacist.dashboard'))->with('success', 'Welcomeback!');
                }
            }
            \Log::info('checkpoint E');
            // If no matching pharmacist found
            return redirect()->back()->with('error', 'Invalid email or password.');
        } catch (\Exception $e) {
            \Log::info('checkpoint F');
            // Log the exception for debugging
            \Log::error('pharmacist login error:', [
                'exception' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'An error occurred during login. Please try again.');
        }
    }

    public function search()
    {
        // Return the search form view
        return view('firebase.pharmacist.search_patient');
    }

    public function searchPatient(Request $request)
    {
        try {
            \Log::info('Search started');

            // Validate input
            $request->validate([
                'search' => 'required|string|max:255',
            ]);

            $searchQuery = strtolower(trim($request->input('search')));

            session(['searchQuery' => $searchQuery]);

            // ✅ Use Firebase Realtime Database instead of Firestore
            $patientsRef = $this->database->getReference($this->full_patientdata);

            $patients = [];
            \Log::info('Querying Firebase Realtime Database');

            // 🔍 Fetch all patients from Firebase
            $patientsSnapshot = $patientsRef->getValue();

            if ($patientsSnapshot) {
                foreach ($patientsSnapshot as $key => $patient) {
                    // ✅ Check if Name or ID Number matches search query
                    if (
                        (isset($patient['name']) && stripos($patient['name'], $searchQuery) !== false) ||
                        (isset($patient['id_no']) && $patient['id_no'] == $searchQuery)
                    ) {
                        // If a match is found, add the patient to the results array
                        $patients[$key] = [
                            'id' => $key, // Firebase unique key (patient ID)
                            'name' => $patient['name'] ?? 'N/A', // Patient name
                            'id_no' => $patient['id_no'] ?? 'N/A', // Patient ID number
                            'gender' => $patient['gender'] ?? 'N/A', // Patient gender
                            'age' => $patient['age'] ?? 'N/A', // Patient age
                            'location' => $patient['location'] ?? 'N/A', // Patient location
                            'dob' => $patient['dob'] ?? 'N/A', // Date of birth
                            'nationality' => $patient['nationality'] ?? 'N/A', // Nationality
                            'race' => $patient['race'] ?? 'N/A', // Patient race
                            'allergies' => $patient['allergies'] ?? 'N/A', // Allergies
                            'medical_alerts' => $patient['medical_alerts'] ?? 'N/A', // Medical alerts
                            'bloodpm' => $patient['bloodpm'] ?? 'N/A', // Blood pressure (systolic/diastolic)
                            'bmi' => $patient['bmi'] ?? 'N/A', // BMI
                            'bmi_status' => $patient['bmi_status'] ?? 'N/A', // BMI status
                            'plusec' => $patient['plusec'] ?? 'N/A', // Pulse
                            'respiratingr' => $patient['respiratingr'] ?? 'N/A', // Respiratory rate
                            'tempreturec' => $patient['tempreturec'] ?? 'N/A', // Temperature
                            'weight' => $patient['weight'] ?? 'N/A', // Weight
                            'height' => $patient['height'] ?? 'N/A', // Height
                            'family_history' => $patient['family_history'] ?? 'N/A', // Family history
                            'clinical_summary' => $patient['clinical_summary'] ?? 'N/A', // Clinical summary
                            'medical_history' => $patient['medical_history'] ?? 'N/A', // Medical history
                            // Add any other fields you need to display
                        ];


                    }
                }
            }

            \Log::info('Search completed with ' . count($patients) . ' results.');

            // Return the search results view with the matching patients
            return view('firebase.pharmacist.search_patient', compact('patients'));
        } catch (\Exception $e) {
            // Log any exceptions and return an error message
            \Log::error('Firebase Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Search failed: ' . $e->getMessage());
        }
    }


// View patient prescriptions and medication status with clinical summary integration

    public function patientMedications($patientId)
    {
        try {
            // Get patient data
            $patientRef = $this->database->getReference($this->full_patientdata . '/' . $patientId);
            $patient = $patientRef->getValue();

            if (!$patient) {
                return redirect()->route('firebase.pharmacist.search')->with('error', 'Patient not found.');
            }

            // Get patient prescriptions
            $prescriptionsRef = $this->database->getReference('prescriptions');
            $allPrescriptions = $prescriptionsRef->getValue() ?: [];
            $prescriptions = [];

            foreach ($allPrescriptions as $id => $prescription) {
                if (isset($prescription['patient_id']) && $prescription['patient_id'] === $patientId) {
                    $prescriptions[$id] = $prescription;
                }
            }

            // Get inventory status for all medications
            $inventoryRef = $this->database->getReference('medication_inventory');
            $inventory = $inventoryRef->getValue() ?: [];

            // Create lookup for inventory statuses and prepare full inventory items list
            $inventoryStatuses = [];
            $allInventoryItems = [];

            foreach ($inventory as $itemId => $item) {
                $medicationName = $item['medication_name'] ?? '';
                if (!empty($medicationName)) {
                    // Add to inventory statuses lookup
                    $inventoryStatuses[strtolower($medicationName)] = [
                        'available' => $item['quantity'] ?? 0,
                        'id' => $itemId
                    ];

                    // Add to inventory items list with ID included
                    $item['id'] = $itemId;
                    $item['medication_name_lower'] = strtolower($medicationName); // For case-insensitive searching
                    $allInventoryItems[] = $item;
                }
            }

            // Sort inventory items alphabetically by name for easier selection
            usort($allInventoryItems, function($a, $b) {
                return strcmp($a['medication_name'] ?? '', $b['medication_name'] ?? '');
            });

            // Check medications from prescriptions against inventory
            foreach ($prescriptions as &$prescription) {
                if (isset($prescription['medications']) && is_array($prescription['medications'])) {
                    foreach ($prescription['medications'] as &$med) {
                        $medName = strtolower($med['name'] ?? '');
                        if (isset($inventoryStatuses[$medName])) {
                            $med['inventory_status'] = $inventoryStatuses[$medName];
                        } else {
                            $med['inventory_status'] = ['available' => 0, 'id' => ''];
                        }
                    }
                }
            }

            // Extract medications from clinical summary if present
            $clinicalSummaryMedications = [];
            if (isset($patient['clinical_summary']) && !empty($patient['clinical_summary'])) {
                // Use regex to extract medication names from clinical summary
                // This is a simple approach and might need refinement based on your data format
                preg_match_all('/\b(?:Medication|Med|Rx|Prescribed):\s*([^,;\r\n]+)/i', $patient['clinical_summary'], $matches);

                if (!empty($matches[1])) {
                    foreach ($matches[1] as $medName) {
                        $trimmedMedName = trim($medName);
                        if (!empty($trimmedMedName)) {
                            $clinicalSummaryMedications[] = ['name' => $trimmedMedName];
                        }
                    }
                }
            }

            // Get recent custom dispensed medications for this patient
            $recentCustomMedications = [];
            $medicationPickupsRef = $this->database->getReference('medication_pickups');
            $medicationPickups = $medicationPickupsRef->orderByChild('patient_id')->equalTo($patientId)->limitToLast(5)->getValue() ?: [];

            foreach ($medicationPickups as $pickup) {
                if (isset($pickup['custom_medications']) && is_array($pickup['custom_medications'])) {
                    foreach ($pickup['custom_medications'] as $med) {
                        if (!in_array($med['name'], array_column($recentCustomMedications, 'name'))) {
                            $recentCustomMedications[] = $med;
                        }
                    }
                }
            }

            // Pass all data to the view
            return view('firebase.pharmacist.patient_medications', [
                'patient' => $patient,
                'patientId' => $patientId,
                'prescriptions' => $prescriptions,
                'inventoryStatuses' => $inventoryStatuses,
                'clinicalSummaryMedications' => $clinicalSummaryMedications,
                'allInventoryItems' => $allInventoryItems,
                'recentCustomMedications' => $recentCustomMedications
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading patient medications: ' . $e->getMessage());
            return redirect()->route('firebase.pharmacist.search')->with('error', 'Error loading patient data: ' . $e->getMessage());
        }
    }

    /**
     * Extract medication information from clinical summary
     *
     * @param string $clinicalSummary The clinical summary text
     * @param string $patientId The patient's ID
     * @return array Array of prescription data
     */
    private function extractMedicationsFromClinicalSummary($clinicalSummary, $patientId)
    {
        $prescriptions = [];

        // Check if the clinical summary is in JSON format
        if ($this->isJson($clinicalSummary)) {
            $data = json_decode($clinicalSummary, true);

            // If it contains a medications array
            if (isset($data['medications']) && is_array($data['medications'])) {
                // Generate a unique ID for this prescription based on clinical summary
                $prescriptionId = 'cs_' . md5($patientId . time());

                $prescriptions[$prescriptionId] = [
                    'patient_id' => $patientId,
                    'medications' => $data['medications'],
                    'created_at' => $data['date'] ?? date('Y-m-d H:i:s'),
                    'source' => 'clinical_summary',
                    'status' => isset($data['medication_status']) ? $data['medication_status'] : 'pending',
                    'notes' => $data['notes'] ?? 'Extracted from clinical summary'
                ];
            }
        } else {
            // If it's plain text, try to extract medication information using patterns
            // This is a simplified example - you may need more complex pattern matching
            $medicationPatterns = [
                '/Medication(?:s)?\s*:\s*([^\.]+)/i',
                '/Prescribed\s*:\s*([^\.]+)/i',
                '/Drug(?:s)?\s*:\s*([^\.]+)/i'
            ];

            $medications = [];
            foreach ($medicationPatterns as $pattern) {
                if (preg_match($pattern, $clinicalSummary, $matches)) {
                    $medicationText = trim($matches[1]);
                    $medicationItems = explode(',', $medicationText);

                    foreach ($medicationItems as $item) {
                        if (!empty(trim($item))) {
                            $medications[] = [
                                'name' => trim($item),
                                'dosage' => 'As directed',
                                'quantity' => 1,
                                'instructions' => 'See clinical summary for details'
                            ];
                        }
                    }
                }
            }

            if (!empty($medications)) {
                $prescriptionId = 'cs_' . md5($patientId . time());
                $prescriptions[$prescriptionId] = [
                    'patient_id' => $patientId,
                    'medications' => $medications,
                    'created_at' => date('Y-m-d H:i:s'),
                    'source' => 'clinical_summary_text',
                    'status' => 'pending',
                    'notes' => 'Extracted from clinical summary text'
                ];
            }
        }

        return $prescriptions;
    }

    /**
     * Check if a string is valid JSON
     *
     * @param string $string The string to check
     * @return bool True if valid JSON, false otherwise
     */
    private function isJson($string) {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

// Update the markMedicationPickup method to handle the clinical_summary_ID case
    public function markMedicationPickup(Request $request, $prescriptionId)
    {
        try {
            $pharmacistId = session('pharmacist_id');
            $pharmacistName = session('pharmacist_name');
            $status = $request->input('status');
            $patientId = $request->input('patient_id') ?? null;
            $fromClinicalSummary = $request->input('from_clinical_summary') ?? false;

            if (!$pharmacistId) {
                return redirect()->back()->with('error', 'Pharmacist authentication required.');
            }

            // Handle clinical summary medication
            if ($fromClinicalSummary && $patientId) {
                $patientRef = $this->database->getReference($this->full_patientdata . '/' . $patientId);
                $patientData = $patientRef->getValue();

                if (!$patientData) {
                    return redirect()->back()->with('error', 'Patient not found.');
                }

                if ($status === 'dispensed') {
                    // Extract medications from clinical summary
                    $clinicalSummary = $patientData['clinical_summary'] ?? '';
                    $extractedMedications = $this->extractMedicationsFromText($clinicalSummary);

                    // Format medications for inventory deduction
                    $medicationsToDeduct = [];
                    foreach ($extractedMedications as $med) {
                        $medicationsToDeduct[] = [
                            'name' => $med,
                            'quantity' => 1 // Default to 1 per medication since we don't have exact quantities
                        ];
                    }

                    // Attempt to deduct from inventory
                    $inventoryResults = $this->deductFromInventory($medicationsToDeduct);

                    // Only mark as dispensed if inventory deduction was successful
                    if ($inventoryResults['success']) {
                        $patientRef->update([
                            'clinical_summary_status' => 'dispensed',
                            'clinical_summary_updated_at' => date('Y-m-d H:i:s'),
                            'clinical_summary_updated_by' => $pharmacistName
                        ]);

                        // Record this pickup
                        $this->recordMedicationPickup($patientId, $extractedMedications, $pharmacistId, $pharmacistName);

                        return redirect()->back()->with('success', 'Clinical summary medications marked as dispensed and inventory updated.');
                    } else {
                        // Create error message with details
                        $errorMsg = 'Cannot dispense medications due to inventory issues:';

                        if (!empty($inventoryResults['not_found'])) {
                            $notFoundMeds = array_column($inventoryResults['not_found'], 'name');
                            $errorMsg .= '<br>- Not found in inventory: ' . implode(', ', $notFoundMeds);
                        }

                        if (!empty($inventoryResults['insufficient'])) {
                            $errorMsg .= '<br>- Insufficient quantities for: ';
                            foreach ($inventoryResults['insufficient'] as $item) {
                                $errorMsg .= '<br>  * ' . $item['name'] . ' (Need: ' . $item['requested'] . ', Available: ' . $item['available'] . ')';
                            }
                        }

                        return redirect()->back()->with('error', $errorMsg);
                    }
                } else {
                    $patientRef->update([
                        'clinical_summary_status' => 'pending',
                        'clinical_summary_updated_at' => date('Y-m-d H:i:s'),
                        'clinical_summary_updated_by' => $pharmacistName
                    ]);

                    return redirect()->back()->with('success', 'Clinical summary medications marked as pending.');
                }
            } else {
                // Handle regular prescription
                $prescriptionRef = $this->database->getReference('prescriptions/' . $prescriptionId);
                $prescription = $prescriptionRef->getValue();

                if (!$prescription) {
                    return redirect()->back()->with('error', 'Prescription not found.');
                }

                if ($status === 'dispensed') {
                    // Check if we have medications to deduct from inventory
                    if (isset($prescription['medications']) && is_array($prescription['medications'])) {
                        // Attempt to deduct from inventory
                        $inventoryResults = $this->deductFromInventory($prescription['medications']);

                        // Only mark as dispensed if inventory deduction was successful
                        if ($inventoryResults['success']) {
                            $prescriptionRef->update([
                                'status' => 'dispensed',
                                'dispensed_at' => date('Y-m-d H:i:s'),
                                'dispensed_by' => $pharmacistName,
                                'dispensed_by_id' => $pharmacistId
                            ]);

                            // Record this pickup
                            $patientId = $prescription['patient_id'] ?? null;
                            if ($patientId) {
                                $this->recordMedicationPickup($patientId, $prescription['medications'], $pharmacistId, $pharmacistName, $prescriptionId);
                            }

                            return redirect()->back()->with('success', 'Prescription marked as dispensed and inventory updated.');
                        } else {
                            // Create error message with details
                            $errorMsg = 'Cannot dispense medications due to inventory issues:';

                            if (!empty($inventoryResults['not_found'])) {
                                $notFoundMeds = array_column($inventoryResults['not_found'], 'name');
                                $errorMsg .= '<br>- Not found in inventory: ' . implode(', ', $notFoundMeds);
                            }

                            if (!empty($inventoryResults['insufficient'])) {
                                $errorMsg .= '<br>- Insufficient quantities for: ';
                                foreach ($inventoryResults['insufficient'] as $item) {
                                    $errorMsg .= '<br>  * ' . $item['name'] . ' (Need: ' . $item['requested'] . ', Available: ' . $item['available'] . ')';
                                }
                            }

                            return redirect()->back()->with('error', $errorMsg);
                        }
                    } else {
                        return redirect()->back()->with('error', 'No medications found in this prescription.');
                    }
                } else {
                    $prescriptionRef->update([
                        'status' => 'pending',
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                    return redirect()->back()->with('success', 'Prescription marked as pending.');
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error handling medication pickup: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error updating status: ' . $e->getMessage());
        }
    }

    /**
     * Record medication pickup in dedicated collection
     */
    private function recordMedicationPickup($patientId, $medications, $pharmacistId, $pharmacistName, $prescriptionId = null)
    {
        try {
            $pickupRef = $this->database->getReference('medication_pickups')->push();
            $pickupRef->set([
                'patient_id' => $patientId,
                'medications' => $medications,
                'pharmacist_id' => $pharmacistId,
                'pharmacist_name' => $pharmacistName,
                'prescription_id' => $prescriptionId,
                'picked_up_at' => date('Y-m-d H:i:s')
            ]);
            return true;
        } catch (\Exception $e) {
            \Log::error('Error recording medication pickup: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Handle medications from clinical summary
     *
     * @param Request $request
     * @param string $patientId
     * @return \Illuminate\Http\RedirectResponse
     */
    private function handleClinicalSummaryMedication(Request $request, $patientId)
    {
        try {
            // Validate that the patient ID is present
            if (!$patientId) {
                return redirect()->back()->with('error', 'Patient ID is required.');
            }

            // Get pharmacist ID for tracking
            $pharmacistId = session('pharmacist_id');
            if (!$pharmacistId) {
                return redirect()->route('login')->with('error', 'You must log in first.');
            }

            // Get the patient record
            $patientRef = $this->database->getReference($this->full_patientdata . '/' . $patientId);
            $patient = $patientRef->getValue();

            if (!$patient) {
                return redirect()->back()->with('error', 'Patient not found.');
            }

            // Get the clinical summary if it exists
            if (!isset($patient['clinical_summary']) || empty($patient['clinical_summary'])) {
                return redirect()->back()->with('error', 'No clinical summary found for this patient.');
            }

            $status = $request->input('status');
            $timestamp = date('Y-m-d H:i:s');

            // 1. Update the patient record with clinical summary status
            $patientRef->update([
                'clinical_summary_status' => $status,
                'clinical_summary_updated_at' => $timestamp,
                'clinical_summary_updated_by' => $pharmacistId
            ]);

            // 2. Create or update clinical_summary_medications table entry
            $this->ensureClinicalSummaryMedicationsTable();

            // Generate a unique ID for this medication status record
            $medicationStatusId = $this->database->getReference('clinical_summary_medications')->push([
                'patient_id' => $patientId,
                'pharmacist_id' => $pharmacistId,
                'status' => $status,
                'updated_at' => $timestamp,
                'clinical_summary' => $patient['clinical_summary'],
                'pharmacist_name' => session('pharmacist_name') ?? 'Unknown'
            ])->getKey();

            // 3. Extract medications from clinical summary if possible
            $extractedMeds = $this->extractMedicationsFromText($patient['clinical_summary']);

            // 4. If status is dispensed, also record in medication_pickups and update inventory
            if ($status === 'dispensed') {
                // Record the pickup
                $this->database->getReference('medication_pickups')->push([
                    'prescription_id' => 'clinical_summary_' . $patientId,
                    'patient_id' => $patientId,
                    'pharmacist_id' => $pharmacistId,
                    'picked_up_at' => $timestamp,
                    'medications' => $extractedMeds,
                    'method' => 'clinical_summary',
                    'clinical_summary_medication_id' => $medicationStatusId
                ]);

                // Update inventory if we have extracted medications
                if (!empty($extractedMeds)) {
                    foreach ($extractedMeds as $medication) {
                        $this->updateInventory($medication['name'] ?? 'Unknown medication', $medication['quantity'] ?? 1);
                    }
                }

                $message = 'Clinical summary medications marked as dispensed successfully.';
            } else {
                $message = 'Clinical summary medications marked as pending.';
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            \Log::error('Error handling clinical summary medication: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error updating status: ' . $e->getMessage());
        }
    }

    /**
     * Ensure the clinical_summary_medications table exists in Firebase
     */
    private function ensureClinicalSummaryMedicationsTable()
    {
        try {
            // Check if the table exists by trying to read from it
            $ref = $this->database->getReference('clinical_summary_medications');
            $ref->getSnapshot();

            // No need to create anything as Firebase will auto-create the path
            return true;
        } catch (\Exception $e) {
            \Log::error('Error checking clinical_summary_medications table: ' . $e->getMessage());
            // Firebase will create the table automatically when we write to it
            return false;
        }
    }

    /**
     * Extract medication information from clinical summary text
     *
     * @param string $text
     * @return array
     */
    private function extractMedicationsFromText($text)
    {
        $medications = [];

        // Try to find medication information in the text
        $patterns = [
            '/medication(?:s)?:?\s*([^\.]+)/i',
            '/prescription(?:s)?:?\s*([^\.]+)/i',
            '/drug(?:s)?:?\s*([^\.]+)/i',
            '/prescribe(?:d)?:?\s*([^\.]+)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $medText = trim($matches[1]);
                $medItems = explode(',', $medText);

                foreach ($medItems as $item) {
                    $item = trim($item);
                    if (!empty($item)) {
                        // Try to extract dosage information if present
                        if (preg_match('/([^\d]+)\s*(\d+\s*mg|\d+\s*ml|\d+\s*g)/i', $item, $medMatches)) {
                            $medications[] = [
                                'name' => trim($medMatches[1]),
                                'dosage' => trim($medMatches[2]),
                                'quantity' => 1,
                                'instructions' => 'As directed in clinical summary'
                            ];
                        } else {
                            $medications[] = [
                                'name' => $item,
                                'dosage' => 'As directed',
                                'quantity' => 1,
                                'instructions' => 'As directed in clinical summary'
                            ];
                        }
                    }
                }

                // If we found medications with the current pattern, stop searching
                if (!empty($medications)) {
                    break;
                }
            }
        }

        return $medications;
    }

    /**
     * Update medication inventory
     *
     * @param string $medicationName
     * @param int $quantity
     * @return void
     */

    private function sanitizeFirebaseKey($key)
    {
        return str_replace(
            ['.', '#', '$', '[', ']', '/', '\\'],
            ['_DOT_', '_HASH_', '_DOLLAR_', '_LBRACK_', '_RBRACK_', '_SLASH_', '_BSLASH_'],
            $key
        );
    }

    /**
     * Display inventory management page
     */
    public function inventory()
    {
        try {
            // Get all medications in inventory
            $inventoryRef = $this->database->getReference('medication_inventory');
            $inventory = $inventoryRef->getValue() ?: [];

            // Format for view with proper keys
            $formattedInventory = [];
            foreach ($inventory as $key => $item) {
                $item['id'] = $key;
                $formattedInventory[] = $item;
            }

            // Sort by name
            usort($formattedInventory, function($a, $b) {
                return strcasecmp($a['medication_name'] ?? '', $b['medication_name'] ?? '');
            });

            // Get low stock items (quantity <= 10)
            $lowStockItems = array_filter($formattedInventory, function($item) {
                return isset($item['quantity']) && $item['quantity'] <= 10;
            });

            return view('firebase.pharmacist.inventory', [
                'inventory' => $formattedInventory,
                'lowStockItems' => $lowStockItems
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading inventory: ' . $e->getMessage());
        }
    }

    /**
     * Add new medication to inventory
     */
    public function addInventory(Request $request)
    {
        $request->validate([
            'medication_name' => 'required|string|max:100',
            'quantity' => 'required|integer|min:0',
            'category' => 'required|string|max:50',
            'description' => 'nullable|string',
            'dosage' => 'nullable|string|max:50',
            'expiry_date' => 'nullable|date'
        ]);

        try {
            $inventoryRef = $this->database->getReference('medication_inventory')->push();

            $inventoryRef->set([
                'medication_name' => $request->medication_name,
                'quantity' => (int) $request->quantity,
                'category' => $request->category,
                'description' => $request->description,
                'dosage' => $request->dosage,
                'expiry_date' => $request->expiry_date,
                'created_at' => date('Y-m-d H:i:s'),
                'last_updated' => date('Y-m-d H:i:s'),
                'updated_by' => session('pharmacist_id') ?? 'unknown'
            ]);

            return redirect()->route('firebase.pharmacist.inventory')->with('success', 'Medication added to inventory successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add medication: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Update medication inventory
     */
    public function updateInventory(Request $request)
    {
        $request->validate([
            'id' => 'required|string',
            'medication_name' => 'required|string|max:100',
            'quantity' => 'required|integer|min:0',
            'category' => 'required|string|max:50',
            'description' => 'nullable|string',
            'dosage' => 'nullable|string|max:50',
            'expiry_date' => 'nullable|date'
        ]);

        try {
            $inventoryRef = $this->database->getReference('medication_inventory/' . $request->id);

            $inventoryRef->update([
                'medication_name' => $request->medication_name,
                'quantity' => (int) $request->quantity,
                'category' => $request->category,
                'description' => $request->description,
                'dosage' => $request->dosage,
                'expiry_date' => $request->expiry_date,
                'last_updated' => date('Y-m-d H:i:s'),
                'updated_by' => session('pharmacist_id') ?? 'unknown'
            ]);

            return redirect()->route('firebase.pharmacist.inventory')->with('success', 'Medication updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update medication: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Delete medication from inventory
     */
    public function deleteInventory($id)
    {
        try {
            $inventoryRef = $this->database->getReference('medication_inventory/' . $id);
            $inventoryRef->remove();

            return redirect()->route('firebase.pharmacist.inventory')->with('success', 'Medication removed from inventory successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete medication: ' . $e->getMessage());
        }
    }

    /**
     * Deduct medications from inventory when dispensed
     *
     */
    private function deductFromInventory($medications)
    {
        $results = [
            'success' => true,
            'deducted' => [],
            'not_found' => [],
            'insufficient' => []
        ];

        // Get the current inventory
        $inventoryRef = $this->database->getReference('medication_inventory');
        $inventory = $inventoryRef->getValue() ?: [];

        foreach ($medications as $medication) {
            $medName = $medication['name'] ?? '';
            $quantityToDeduct = isset($medication['quantity']) ? (int)$medication['quantity'] : 1;
            $found = false;

            // Look for the medication in inventory by name
            foreach ($inventory as $itemId => $item) {
                if (strtolower($item['medication_name']) === strtolower($medName)) {
                    $found = true;
                    $currentQuantity = isset($item['quantity']) ? (int)$item['quantity'] : 0;

                    // Check if there's enough in inventory
                    if ($currentQuantity >= $quantityToDeduct) {
                        // Deduct from inventory
                        $newQuantity = $currentQuantity - $quantityToDeduct;
                        $inventoryRef->getChild($itemId)->update([
                            'quantity' => $newQuantity,
                            'last_updated' => date('Y-m-d H:i:s'),
                            'updated_by' => session('pharmacist_name') ?? 'Pharmacist'
                        ]);

                        $results['deducted'][] = [
                            'name' => $medName,
                            'quantity' => $quantityToDeduct,
                            'remaining' => $newQuantity
                        ];

                        // Log this transaction
                        $this->logInventoryTransaction($itemId, $medName, $quantityToDeduct, 'dispensed', $medication);
                    } else {
                        $results['insufficient'][] = [
                            'name' => $medName,
                            'requested' => $quantityToDeduct,
                            'available' => $currentQuantity
                        ];
                        $results['success'] = false;
                    }
                    break;
                }
            }

            if (!$found) {
                $results['not_found'][] = ['name' => $medName];
                $results['success'] = false;
            }
        }

        return $results;
    }

    /**
     * Log an inventory transaction

     */
    private function logInventoryTransaction($itemId, $medicationName, $quantity, $type, $details = [])
    {
        try {
            $transactionRef = $this->database->getReference('inventory_transactions')->push();
            $transactionRef->set([
                'item_id' => $itemId,
                'medication_name' => $medicationName,
                'quantity' => $quantity,
                'type' => $type,
                'details' => $details,
                'timestamp' => date('Y-m-d H:i:s'),
                'pharmacist_id' => session('pharmacist_id') ?? 'unknown',
                'pharmacist_name' => session('pharmacist_name') ?? 'Pharmacist'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error logging inventory transaction: ' . $e->getMessage());
        }
    }

    /**
     * Custom medication dispensing directly from inventory
     */
    public function customDispense(Request $request)
    {
        try {
            $patientId = $request->input('patient_id');
            $medicationsJson = $request->input('medications');
            $notes = $request->input('notes', '');

            if (!$patientId || !$medicationsJson) {
                return redirect()->back()->with('error', 'Missing required information');
            }

            // Decode medications array
            $medications = json_decode($medicationsJson, true);

            if (empty($medications)) {
                return redirect()->back()->with('error', 'No medications selected');
            }

            // Check patient exists
            $patientRef = $this->database->getReference($this->full_patientdata . '/' . $patientId);
            $patient = $patientRef->getValue();

            if (!$patient) {
                return redirect()->back()->with('error', 'Patient not found');
            }

            // Create custom prescription record
            $customPrescriptionRef = $this->database->getReference('prescriptions')->push();
            $customPrescriptionId = $customPrescriptionRef->getKey();

            $customPrescriptionRef->set([
                'patient_id' => $patientId,
                'patient_name' => $patient['name'] ?? 'Unknown',
                'medications' => $medications,
                'notes' => $notes,
                'source' => 'pharmacist_custom',
                'status' => 'dispensed',
                'created_at' => date('Y-m-d H:i:s'),
                'dispensed_at' => date('Y-m-d H:i:s'),
                'dispensed_by' => session('pharmacist_name') ?? 'Pharmacist',
                'dispensed_by_id' => session('pharmacist_id') ?? 'Unknown'
            ]);

            // Deduct from inventory
            $deductResults = $this->deductFromInventory($medications);

            // Record this pickup
            $this->recordMedicationPickup($patientId, $medications, session('pharmacist_id'), session('pharmacist_name'), $customPrescriptionId);

            // Create message based on results
            if ($deductResults['success']) {
                $message = "Medications successfully dispensed to patient.";
            } else {
                $warningItems = [];

                if (!empty($deductResults['not_found'])) {
                    $notFoundNames = array_column($deductResults['not_found'], 'name');
                    $warningItems[] = "Some medications were not found in inventory: " . implode(', ', $notFoundNames);
                }

                if (!empty($deductResults['insufficient'])) {
                    $insufficientItems = array_map(function($item) {
                        return $item['name'] . " (requested: " . $item['requested'] . ", available: " . $item['available'] . ")";
                    }, $deductResults['insufficient']);
                    $warningItems[] = "Some medications had insufficient quantities: " . implode(', ', $insufficientItems);
                }

                $message = "Medications dispensed with warnings: " . implode(' ', $warningItems);
            }

            return redirect()->route('firebase.pharmacist.patient_medications', ['patientId' => $patientId])
                ->with($deductResults['success'] ? 'success' : 'warning', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error dispensing custom medications: ' . $e->getMessage());
        }
    }

    /**
     * Handle dispensing custom medications
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function customDispenseMedications(Request $request)
    {
        try {
            $request->validate([
                'patient_id' => 'required',
                'medications' => 'required|json',
                'notes' => 'nullable|string'
            ]);

            $patientId = $request->input('patient_id');
            $medications = json_decode($request->input('medications'), true);

            if (empty($medications)) {
                return redirect()->route('firebase.pharmacist.search', ['patientId' => $patientId])
                    ->with('error', 'No medications selected for dispensing');
            }

            $pharmacistId = session('pharmacist_id');
            $pharmacistName = session('pharmacist_name');

            // Deduct from inventory
            $inventoryResults = $this->deductFromInventory($medications);

            if (!$inventoryResults['success']) {
                $errorMessage = 'Failed to dispense: ';
                if (!empty($inventoryResults['not_found'])) {
                    $errorMessage .= 'Some medications were not found in inventory. ';
                }
                if (!empty($inventoryResults['insufficient'])) {
                    $errorMessage .= 'Some medications have insufficient quantity.';
                }

                // Fix: Changed 'firebase.pharmacist.patient-medications' to 'firebase.pharmacist.patient_medications'
                return redirect()->route('firebase.pharmacist.patient.medications', ['patientId' => $patientId])
                    ->with('error', $errorMessage);
            }

            // Record the pickup
            $pickupData = [
                'patient_id' => $patientId,
                'pharmacist_id' => $pharmacistId,
                'pharmacist_name' => $pharmacistName,
                'custom_medications' => $medications,
                'notes' => $request->input('notes'),
                'picked_up_at' => date('Y-m-d H:i:s'),
                'type' => 'custom_dispensing'
            ];

            $pickupRef = $this->database->getReference('medication_pickups')->push();
            $pickupRef->set($pickupData);

            // Fix: Changed 'firebase.pharmacist.patient-medications' to 'firebase.pharmacist.patient_medications'
            return redirect()->route('firebase.pharmacist.patient.medications', ['patientId' => $patientId])
                ->with('success', 'Medications successfully dispensed to patient');

        } catch (\Exception $e) {
            \Log::error('Error dispensing custom medications: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error dispensing medications: ' . $e->getMessage());
        }
    }
}
