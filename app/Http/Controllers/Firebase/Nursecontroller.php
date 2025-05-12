<?php

namespace App\Http\Controllers\Firebase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;
use Illuminate\Support\Facades\Hash;
use Google\Cloud\Firestore\FirestoreClient;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Nursecontroller extends Controller
{
    protected $database;
    protected $tablenurse;
    protected $tablesctest;
    protected $registerpatient;
    protected $merge_patient;
    protected $patient_checkin;
    protected $chats;

    protected $appointments;


    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->tablenurse = 'nurse'; // Ensure this matches your Firebase structure
        $this->tablesctest = 'sctest'; // Ensure this matches your Firebase structure
        $this->registerpatient = 'register_patient'; // Ensure this matches your Firebase structure
        $this->merge_patient = 'merge_patient'; // Ensure this matches your Firebase structure
        $this->patient_checkin = 'patient_checkin';// data checkin patient
        $this->chats = 'chats'; // Add this new property for chats
        $this->appointments = 'appointments'; // Add this new property for chats
    }

    // Display the nurse login page
    public function index()
    {
        return view('firebase.nurse.index');
    }


    public function dashboard()
    {
        try {

            $fname = session('nurse_name', 'Nurse');

            // Fetch patient data from Firebase
            $patientData = $this->database->getReference('full_patientdata')->getValue() ?: [];
            $patientCount = count($patientData);

            // Calculate today's appointments
            $appointmentsToday = 0;
            $today = date('Y-m-d');
            $appointments = $this->database->getReference('appointments')->getValue() ?: [];

            foreach ($appointments as $appointment) {
                if (isset($appointment['date']) && substr($appointment['date'], 0, 10) === $today) {
                    $appointmentsToday++;
                }
            }

            // Count pending appointments for notification badge
            $pendingAppointments = 0;
            foreach ($appointments as $appointment) {
                if (isset($appointment['status']) && $appointment['status'] === 'pending') {
                    $pendingAppointments++;
                }
            }

            // Calculate vitals recorded today
            $vitalsRecorded = 0;
            foreach ($patientData as $patient) {
                if (isset($patient['vitals']) && is_array($patient['vitals'])) {
                    foreach ($patient['vitals'] as $vital) {
                        if (isset($vital['date']) && substr($vital['date'], 0, 10) === $today) {
                            $vitalsRecorded++;
                        }
                    }
                }
            }

            // Calculate average blood pressure
            $systolicSum = 0;
            $diastolicSum = 0;
            $bpCount = 0;

            foreach ($patientData as $patient) {
                if (isset($patient['vitals']) && is_array($patient['vitals'])) {
                    foreach ($patient['vitals'] as $vital) {
                        if (isset($vital['date']) && substr($vital['date'], 0, 10) === $today &&
                            isset($vital['bp_systolic']) && isset($vital['bp_diastolic'])) {
                            $systolicSum += (int)$vital['bp_systolic'];
                            $diastolicSum += (int)$vital['bp_diastolic'];
                            $bpCount++;
                        }
                    }
                }
            }

            $avgBP = $bpCount > 0 ?
                round($systolicSum / $bpCount) . "/" . round($diastolicSum / $bpCount) :
                '120/80'; // Default if no BP readings

            // Recent chats for the dashboard
            $recentChats = $this->getRecentChats();

            // NEW: Recent appointments for the dashboard
            $recentAppointments = $this->getRecentAppointments();

            // Calculate accepted appointments for today
            $acceptedAppointments = 0;
            foreach ($appointments as $appointment) {
                if (isset($appointment['date']) && substr($appointment['date'], 0, 10) === $today &&
                    isset($appointment['status']) && $appointment['status'] === 'accepted') {
                    $acceptedAppointments++;
                }
            }

// Calculate cancelled appointments for today
            $cancelledAppointments = 0;
            foreach ($appointments as $appointment) {
                if (isset($appointment['date']) && substr($appointment['date'], 0, 10) === $today &&
                    isset($appointment['status']) && ($appointment['status'] === 'cancelled' || $appointment['status'] === 'declined')) {
                    $cancelledAppointments++;
                }
            }

// Then add these variables to your compact() function:
            return view('firebase.nurse.dashboard', compact(
                'fname',
                'patientCount',
                'appointmentsToday',
                'vitalsRecorded',
                'avgBP',
                'recentChats',
                'recentAppointments',
                'pendingAppointments',
                'acceptedAppointments',
                'cancelledAppointments'
            ));
        } catch (\Exception $e) {
            \Log::error('Error loading nurse dashboard: ' . $e->getMessage());
            $fname = session('nurse_name', 'Nurse');
            return view('firebase.nurse.dashboard', compact('fname'))
                ->with('error', 'Error loading dashboard data: ' . $e->getMessage());
        }
    }

    private function getRecentChats($limit = 3)
    {
        try {
            // Get nurse ID from session
            $nurseId = session('nurse_id');

            if (!$nurseId) {
                return [];
            }

            // Get chats from Firebase
            $chatsRef = $this->database->getReference($this->chats);
            $chats = $chatsRef->getValue() ?: [];

            $recentChats = [];
            foreach ($chats as $chatId => $chat) {
                // Only include chats explicitly assigned to this nurse or new chats that need assignment
                if (
                    // Case 1: Chat is explicitly assigned to this nurse
                    (isset($chat['assigned_nurse_id']) && $chat['assigned_nurse_id'] === $nurseId) ||
                    // Case 2: Chat has a preferred_nurse_id that matches this nurse
                    (isset($chat['preferred_nurse_id']) && $chat['preferred_nurse_id'] === $nurseId) ||
                    // Case 3: New chat with no assignment yet that mentions this nurse's name
                    (!isset($chat['assigned_nurse_id']) && isset($chat['preferred_nurse_name']) &&
                        stripos($chat['preferred_nurse_name'], session('nurse_name')) !== false)
                ) {
                    // Extract patient ID from chat ID format (patient-id_chat-id)
                    $patientId = null;
                    if (strpos($chatId, '_') !== false) {
                        $patientId = substr($chatId, 0, strpos($chatId, '_'));
                    }

                    if ($patientId) {
                        // Get patient data from register_patient collection
                        $patientRef = $this->database->getReference($this->registerpatient . '/' . $patientId);
                        $patientData = $patientRef->getValue();

                        if ($patientData) {
                            // Get last message and count unread messages
                            $lastMessage = null;
                            $unreadCount = 0;

                            if (isset($chat['messages']) && is_array($chat['messages'])) {
                                $messages = $chat['messages'];

                                // Make sure there are messages before trying to get the last one
                                if (!empty($messages)) {
                                    $lastMessageKey = array_key_last($messages);
                                    $lastMessageData = $messages[$lastMessageKey];

                                    if (is_array($lastMessageData)) {
                                        $lastMessage = [
                                            'text' => isset($lastMessageData['text']) ? $lastMessageData['text'] : 'No message content',
                                            'timestamp' => isset($lastMessageData['timestamp']) ? $lastMessageData['timestamp'] : time() * 1000,
                                            'isFromNurse' => isset($lastMessageData['isFromNurse']) ? $lastMessageData['isFromNurse'] : false
                                        ];

                                        // Count unread messages from patient (those not from the nurse)
                                        foreach ($messages as $message) {
                                            if (isset($message['isFromNurse']) && $message['isFromNurse'] === false &&
                                                (!isset($message['is_read']) || $message['is_read'] === false)) {
                                                $unreadCount++;
                                            }
                                        }
                                    }
                                }
                            }

                            // Add preferred nurse name display if available
                            $preferredNurse = isset($chat['preferred_nurse_name']) ?
                                'Preferred: ' . $chat['preferred_nurse_name'] : '';

                            $recentChats[] = [
                                'chat_id' => $chatId,
                                'patient_id' => $patientId,
                                'patient_name' => isset($patientData['name']) ? $patientData['name'] : 'Unknown Patient',
                                'preferred_nurse' => $preferredNurse,
                                'last_message' => $lastMessage,
                                'unread_count' => $unreadCount,
                                'is_new' => !isset($chat['assigned_nurse_id'])
                            ];
                        }
                    }
                }
            }

            // Sort by last message timestamp (most recent first)
            usort($recentChats, function($a, $b) {
                if (!isset($a['last_message']) && !isset($b['last_message'])) return 0;
                if (!isset($a['last_message'])) return 1;
                if (!isset($b['last_message'])) return -1;

                $aTime = isset($a['last_message']['timestamp']) ? $a['last_message']['timestamp'] : 0;
                $bTime = isset($b['last_message']['timestamp']) ? $b['last_message']['timestamp'] : 0;

                return $bTime - $aTime;
            });

            // Return limited number of chats
            return array_slice($recentChats, 0, $limit);

        } catch (\Exception $e) {
            \Log::error('Get recent chats error: ' . $e->getMessage());
            return [];
        }
    }

    public function viewChat($chatId)
    {
        try {
            $nurseId = session('nurse_id');
            $nurseName = session('nurse_name', 'Nurse');

            if (!$nurseId) {
                return redirect()->route('firebase.nurse.loginnurse')
                    ->with('error', 'Please login to access chat');
            }

            // Extract patient ID from chat ID format
            $patientId = null;
            if (strpos($chatId, '_') !== false) {
                $patientId = substr($chatId, 0, strpos($chatId, '_'));
            }

            // Get the chat data
            $chatRef = $this->database->getReference($this->chats . '/' . $chatId);
            $chat = $chatRef->getValue();

            if (!$chat) {
                return redirect()->route('firebase.nurse.dashboard')
                    ->with('error', 'Chat not found');
            }

            // Check if this nurse is authorized to view this chat
            if (isset($chat['assigned_nurse_id']) && $chat['assigned_nurse_id'] !== $nurseId &&
                (!isset($chat['preferred_nurse_id']) || $chat['preferred_nurse_id'] !== $nurseId)) {
                // Chat is assigned to another nurse and this nurse is not preferred
                return redirect()->route('firebase.nurse.dashboard')
                    ->with('error', 'You do not have permission to view this conversation');
            }

            // If chat is new and this nurse is the preferred nurse, assign it to them
            if (!isset($chat['assigned_nurse_id']) &&
                (isset($chat['preferred_nurse_id']) && $chat['preferred_nurse_id'] === $nurseId ||
                    isset($chat['preferred_nurse_name']) && stripos($chat['preferred_nurse_name'], $nurseName) !== false)) {

                $chatRef->update([
                    'assigned_nurse_id' => $nurseId,
                    'assigned_nurse_name' => $nurseName,
                    'last_viewed_by' => [
                        'nurse_id' => $nurseId,
                        'nurse_name' => $nurseName,
                        'timestamp' => round(microtime(true) * 1000)
                    ]
                ]);
            }

            // Get patient data
            $patientData = [];
            if ($patientId) {
                $patientRef = $this->database->getReference($this->registerpatient . '/' . $patientId);
                $patientData = $patientRef->getValue() ?: [];
            }

            // Format messages for display
            $messages = [];
            if (isset($chat['messages']) && is_array($chat['messages'])) {
                foreach ($chat['messages'] as $msgId => $msg) {
                    // Check if necessary keys exist to prevent errors
                    if (is_array($msg)) {
                        $timestamp = isset($msg['timestamp']) ? $msg['timestamp'] : time() * 1000;

                        $messages[] = [
                            'id' => $msgId,
                            'text' => isset($msg['text']) ? $msg['text'] : 'No content',
                            'isFromNurse' => isset($msg['isFromNurse']) ? $msg['isFromNurse'] : false,
                            'nurse_name' => isset($msg['nurse_name']) ? $msg['nurse_name'] : null,
                            'timestamp' => $timestamp,
                            'time' => date('h:i A', $timestamp / 1000),
                            'date' => date('Y-m-d', $timestamp / 1000)
                        ];
                    }
                }
            }

            // Mark messages as read
            if (isset($chat['messages']) && is_array($chat['messages'])) {
                foreach ($chat['messages'] as $msgId => $msg) {
                    if (isset($msg['isFromNurse']) && $msg['isFromNurse'] === false &&
                        (!isset($msg['is_read']) || $msg['is_read'] === false)) {
                        $this->database->getReference($this->chats . '/' . $chatId . '/messages/' . $msgId . '/is_read')->set(true);
                    }
                }
            }

            // Sort messages by timestamp
            usort($messages, function($a, $b) {
                return $a['timestamp'] - $b['timestamp'];
            });

            // Group messages by date
            $groupedMessages = [];
            foreach ($messages as $msg) {
                $date = $msg['date'];
                if (!isset($groupedMessages[$date])) {
                    $groupedMessages[$date] = [];
                }
                $groupedMessages[$date][] = $msg;
            }

            // Find the last nurse who replied (to display in the header)
            $lastNurseReply = null;
            if (!empty($messages)) {
                for ($i = count($messages) - 1; $i >= 0; $i--) {
                    if ($messages[$i]['isFromNurse'] && !empty($messages[$i]['nurse_name'])) {
                        $lastNurseReply = $messages[$i]['nurse_name'];
                        break;
                    }
                }
            }

            return view('firebase.nurse.chat', compact(
                'chatId',
                'patientId',
                'patientData',
                'groupedMessages',
                'nurseName',
                'lastNurseReply'
            ));

        } catch (\Exception $e) {
            \Log::error('View chat error: ' . $e->getMessage());
            return redirect()->route('firebase.nurse.dashboard')
                ->with('error', 'Error loading chat: ' . $e->getMessage());
        }
    }

    public function sendMessage(Request $request, $chatId)
    {
        try {
            $nurseId = session('nurse_id');
            $nurseName = session('nurse_name', 'Nurse');

            if (!$nurseId) {
                return redirect()->route('firebase.nurse.loginnurse')
                    ->with('error', 'Please login to send messages');
            }

            $request->validate([
                'message' => 'required|string|max:1000',
            ]);

            // Create new message
            $newMessage = [
                'text' => $request->message,
                'isFromNurse' => true,
                'nurse_id' => $nurseId,
                'nurse_name' => $nurseName,
                'timestamp' => round(microtime(true) * 1000), // Current timestamp in milliseconds
                'is_read' => false
            ];

            // Add message to chat
            $messagesRef = $this->database->getReference($this->chats . '/' . $chatId . '/messages');
            $messagesRef->push($newMessage);

            // Update the assigned nurse for this chat
            $chatRef = $this->database->getReference($this->chats . '/' . $chatId);
            $chatRef->update([
                'assigned_nurse_id' => $nurseId,
                'assigned_nurse_name' => $nurseName,
                'last_activity' => round(microtime(true) * 1000)
            ]);

            return redirect()->back();

        } catch (\Exception $e) {
            \Log::error('Send message error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error sending message: ' . $e->getMessage());
        }
    }

    public function allChats()
    {
        try {
            $nurseId = session('nurse_id');

            if (!$nurseId) {
                return redirect()->route('firebase.nurse.loginnurse')
                    ->with('error', 'Please login to access chats');
            }

            // Get all chats, no limit
            $allChats = $this->getRecentChats(100);

            return view('firebase.nurse.all_chats', compact('allChats'));

        } catch (\Exception $e) {
            \Log::error('All chats error: ' . $e->getMessage());
            return redirect()->route('firebase.nurse.dashboard')
                ->with('error', 'Error loading conversations: ' . $e->getMessage());
        }
    }

    public function logout()
    {
        session()->flush(); // Clear all session data
        return redirect(route('firebase.nurse.loginnurse'))->with('success', 'Logged out successfully.');
    }

    // Handle nurse login
    public function loginnurse(Request $request)
    {
        \Log::info('nurse login initiated', $request->all());

        // Validate input fields
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try {
            // Fetch all nurse data from Firebase
            $nurses = $this->database->getReference($this->tablenurse)->getValue();
            \Log::info('nurses fetched from Firebase', ['nurse' => $nurses]);
            \Log::info('checkpoint A');

            // Check if nurses data is available and valid
            if (!$nurses || !is_array($nurses)) {
                return redirect()->back()->with('error', 'Invalid email or password.');
            }
            \Log::info('checkpoint B');
            foreach ($nurses as $nurseId => $nurseData) {
                \Log::info('Checking nurse record:', ['nurseId' => $nurseId, 'nurseData' => $nurseData]);

                \Log::info('checkpoint C');
                // Validate structure and match email
                if (
                    isset($nurseData['email'], $nurseData['password']) &&
                    $nurseData['email'] === $request->input('email') &&
                    \Illuminate\Support\Facades\Hash::check($request->input('password'), $nurseData['password'])
                ) {
                    \Log::info('nurse authenticated successfully', ['nurse_id' => $nurseId]);


                    // Store session data for nurse
                    session([
                        'nurse_id' => $nurseId,
                        'nurse_email' => $nurseData['email'],
                        'nurse_name' => $nurseData['fname'],
                    ]);
                    \Log::info('checkpoint D');

                    return redirect(route('firebase.nurse.dashboard'))->with('success', 'Welcomeback!');
                }
            }
            \Log::info('checkpoint E');
            // If no matching nurse found
            return redirect()->back()->with('error', 'Invalid email or password.');
        } catch (\Exception $e) {
            \Log::info('checkpoint F');
            // Log the exception for debugging
            \Log::error('nurse login error:', [
                'exception' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'An error occurred during login. Please try again.');
        }
    }

    public function sctest($id)
    {
        // Retrieve patient details from Firebase using the provided ID
        $patientRef = $this->database->getReference('register_patient/' . $id);
        $patient = $patientRef->getValue();

        // Check if the patient exists
        if (!$patient) {
            return redirect()->route('firebase.nurse.searchPatient')->with('error', 'Patient not found.');
        }

        // Return the patient details to the view
        return view('firebase.nurse.sctest', compact('patient', 'id'));
    }

    public function store_sctest(Request $request, $id)
    {
        // Validate the screening test inputs
        $validatedData = $request->validate([
            'bloodpm_systolic' => 'required|numeric|min:1',
            'bloodpm_diastolic' => 'required|numeric|min:1',
            'tempreturec' => 'required|numeric|min:30|max:45',
            'plusec' => 'required|numeric|min:30|max:200',
            'respiratingr' => 'required|numeric|min:5|max:60',
            'weight' => 'required|numeric|min:20|max:300',
            'height' => 'required|numeric|min:50|max:250',
        ]);

        // Convert height from cm to meters
        $heightInMeters = $request->height / 100;

        // Calculate BMI
        $bmi = round($request->weight / ($heightInMeters * $heightInMeters), 1);

        // Determine BMI Status
        if ($bmi < 18.5) {
            $bmiStatus = "Underweight";
        } elseif ($bmi >= 18.5 && $bmi < 24.9) {
            $bmiStatus = "Normal";
        } elseif ($bmi >= 25 && $bmi < 29.9) {
            $bmiStatus = "Overweight";
        } else {
            $bmiStatus = "Obese";
        }

        // Get patient demographic data from Firebase using the ID
        $patientRef = $this->database->getReference($this->registerpatient . '/' . $id);
        $patientData = $patientRef->getValue();

        if (!$patientData) {
            return redirect()->back()->with('error', 'Patient not found.');
        }

        // Merge screening test data with demographic data
        $mergedData = array_merge($patientData, [
            'bloodpm' => $request->bloodpm_systolic . '/' . $request->bloodpm_diastolic . ' mmHg',
            'tempreturec' => $request->tempreturec . ' °C',
            'plusec' => $request->plusec . ' BPM',
            'respiratingr' => $request->respiratingr . ' BPM',
            'weight' => $request->weight . ' kg',
            'height' => $request->height . ' cm',
            'bmi' => $bmi . ' kg/m²',
            'bmi_status' => $bmiStatus,
        ]);

        // Store the merged data in Firebase under `merge_patient` collection
        $this->database->getReference('merge_patient/' . $id)->set($mergedData);

        return redirect(route('firebase.nurse.sctest', ['id' => $id]))->with('success', 'Patient Screening Test Saved Successfully');
    }

    public function register_patient()
    {
        // Get today's date for default values
        $today = now()->toDateString();

        // Generate a unique visit number prefix based on year and month
        $visitPrefix = 'V-' . now()->format('Ym');

        // Get the last visit number from Firebase to increment
        $lastVisitRef = $this->database->getReference('visit_counter/last_number');
        $lastVisitNumber = $lastVisitRef->getValue() ?? 0;
        $nextVisitNumber = $lastVisitNumber + 1;

        // Format with leading zeros (V-YYYYMM-0001)
        $suggestedVisitNo = $visitPrefix . '-' . str_pad($nextVisitNumber, 4, '0', STR_PAD_LEFT);

        return view('firebase.nurse.registerpatient', compact('today', 'suggestedVisitNo'));
    }

    public function storePatient(Request $request)
    {
        // Validate input fields with more comprehensive rules
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female,Other',
            'location' => 'nullable|string|max:255',
            'id_no' => 'nullable|string|max:50',
            'dob' => 'required|date',
            'nationality' => 'nullable|string|max:255',
            'visit_no' => 'nullable|string|max:50',
            'age' => 'nullable|numeric|min:0|max:120',
            'race' => 'nullable|string|max:255',
            'allergies' => 'nullable|string|max:500',
            'medical_alerts' => 'nullable|string|max:500',
            'height' => 'nullable|numeric|min:1|max:300',
            'weight' => 'nullable|numeric|min:1|max:500',
            'blood_type' => 'nullable|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'contact' => 'nullable|string|max:50',
            'medical_history' => 'nullable|string|max:1000',
            'family_history' => 'nullable|string|max:1000',
            'chronic_conditions' => 'nullable|string|max:500',
            'current_medications' => 'nullable|string|max:500',
        ]);

        // Calculate BMI if height and weight are provided
        $bmi = null;
        $bmiStatus = null;

        if ($request->filled('height') && $request->filled('weight') && $request->height > 0) {
            // Convert height from cm to meters
            $heightInMeters = $request->height / 100;

            // Calculate BMI
            $bmi = round($request->weight / ($heightInMeters * $heightInMeters), 1);

            // Determine BMI Status
            if ($bmi < 18.5) {
                $bmiStatus = "Underweight";
            } elseif ($bmi >= 18.5 && $bmi < 24.9) {
                $bmiStatus = "Normal";
            } elseif ($bmi >= 25 && $bmi < 29.9) {
                $bmiStatus = "Overweight";
            } else {
                $bmiStatus = "Obese";
            }
        }

        // Update visit counter in Firebase
        if ($request->filled('visit_no')) {
            $visitParts = explode('-', $request->visit_no);
            if (count($visitParts) >= 3) {
                $visitNumber = (int) end($visitParts);
                $this->database->getReference('visit_counter/last_number')->set($visitNumber);
            }
        }

        // Prepare data to store with additional fields
        $patientData = [
            'name' => $request->name,
            'gender' => $request->gender,
            'location' => $request->location,
            'id_no' => $request->id_no,
            'dob' => $request->dob,
            'nationality' => $request->nationality,
            'visit_no' => $request->visit_no,
            'age' => $request->age,
            'race' => $request->race,
            'allergies' => $request->allergies,
            'medical_alerts' => $request->medical_alerts,
            'height' => $request->filled('height') ? $request->height . ' cm' : null,
            'weight' => $request->filled('weight') ? $request->weight . ' kg' : null,
            'blood_type' => $request->blood_type,
            'contact' => $request->contact,
            'medical_history' => $request->medical_history,
            'family_history' => $request->family_history,
            'chronic_conditions' => $request->chronic_conditions,
            'current_medications' => $request->current_medications,
            'created_at' => now()->toDateTimeString(),
            'created_by' => session('nurse_name') ?? 'Unknown',
        ];

        // Add BMI data if calculated
        if ($bmi !== null) {
            $patientData['bmi'] = $bmi . ' kg/m²';
            $patientData['bmi_status'] = $bmiStatus;
        }

        // Log data for debugging
        \Log::info('Patient Registration Data:', $patientData);

        // Store data in Firebase
        $postRef = $this->database->getReference($this->registerpatient)->push($patientData);
        $patientId = $postRef->getKey();

        // Check if data is stored successfully
        if ($postRef) {
            // Determine what to do next based on patient's needs
            if ($request->has('needs_screening')) {
                // Redirect to screening test if needed
                return redirect(route('firebase.nurse.sctest', ['id' => $patientId]))
                    ->with('success', 'Patient registered successfully. Please complete the screening test.');
            } else {
                // Generate QR code for check-in if needed
                if ($request->has('generate_qr')) {
                    return redirect(route('firebase.nurse.checkin', ['id' => $patientId]))
                        ->with('success', 'Patient registered successfully. QR code generated for check-in.');
                }

                // Default redirect
                return redirect(route('firebase.nurse.register_patient'))
                    ->with('success', 'Patient registered successfully with ID: ' . $patientId);
            }
        } else {
            return redirect(route('firebase.nurse.register_patient'))
                ->with('error', 'Failed to register patient. Please try again.');
        }
    }

    public function search()
    {
        try {
            // Get all patient count for dashboard metrics
            $patientsRef = $this->database->getReference($this->registerpatient);
            $patientsSnapshot = $patientsRef->getSnapshot();
            $totalPatients = $patientsSnapshot->numChildren();

            // Get today's check-ins count
            $today = now()->format('Y-m-d');
            $checkinRef = $this->database->getReference($this->patient_checkin);
            $checkins = $checkinRef->getValue() ?: [];

            $todayCheckins = 0;
            foreach ($checkins as $checkin) {
                if (isset($checkin['checkin_date']) && $checkin['checkin_date'] === $today) {
                    $todayCheckins++;
                }
            }

            // Get recent patients (last 5)
            $recentPatients = [];

            // Use the indexed query - requires .indexOn rule in Firebase
            try {
                $patientsData = $patientsRef->orderByChild('created_at')->limitToLast(5)->getValue();

                if ($patientsData) {
                    foreach ($patientsData as $key => $patient) {
                        $recentPatients[] = [
                            'id' => $key,
                            'name' => $patient['name'] ?? 'Unknown',
                            'id_no' => $patient['id_no'] ?? 'N/A',
                            'created_at' => $patient['created_at'] ?? now()->toDateTimeString(),
                        ];
                    }
                    // Sort by most recent first
                    usort($recentPatients, function($a, $b) {
                        return strtotime($b['created_at']) - strtotime($a['created_at']);
                    });
                }
            } catch (\Exception $e) {
                // Fallback if index is not yet set up
                \Log::warning('Firebase index error: ' . $e->getMessage());

                // Fallback to retrieving all and sorting manually
                $allPatients = $patientsRef->getValue() ?: [];
                $patientsWithTimestamp = [];

                foreach ($allPatients as $key => $patient) {
                    $timestamp = isset($patient['created_at']) ? strtotime($patient['created_at']) : 0;
                    $patientsWithTimestamp[$key] = [
                        'data' => $patient,
                        'timestamp' => $timestamp
                    ];
                }

                // Sort by timestamp (most recent first)
                uasort($patientsWithTimestamp, function($a, $b) {
                    return $b['timestamp'] - $a['timestamp'];
                });

                // Take only the first 5 after sorting
                $counter = 0;
                foreach ($patientsWithTimestamp as $key => $patientData) {
                    if ($counter >= 5) break;

                    $patient = $patientData['data'];
                    $recentPatients[] = [
                        'id' => $key,
                        'name' => $patient['name'] ?? 'Unknown',
                        'id_no' => $patient['id_no'] ?? 'N/A',
                        'created_at' => $patient['created_at'] ?? now()->toDateTimeString(),
                    ];
                    $counter++;
                }
            }

            return view('firebase.nurse.search_patient', compact('totalPatients', 'todayCheckins', 'recentPatients'));
        } catch (\Exception $e) {
            \Log::error('Search dashboard error: ' . $e->getMessage());
            return view('firebase.nurse.search_patient')->with('error', 'Error loading dashboard data');
        }
    }

    //search patient code
    public function searchPatient(Request $request)
    {
        try {
            \Log::info('Search started');

            // Validate input
            $request->validate([
                'search' => 'required|string|max:255',
            ]);

            $searchQuery = strtolower(trim($request->input('search')));
            $searchType = $request->input('search_type', 'all'); // Default to 'all'

            // ✅ Use Firebase Realtime Database instead of Firestore
            $patientsRef = $this->database->getReference($this->registerpatient);

            $patients = [];
            \Log::info('Querying Firebase Realtime Database with criteria: ' . $searchType);

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
                        case 'id':
                            $matchFound = isset($patient['id_no']) && stripos($patient['id_no'], $searchQuery) !== false;
                            break;
                        case 'visit':
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
                        // Check if merged (screening) data exists
                        $mergedData = $this->database->getReference($this->merge_patient . '/' . $key)->getValue();

                        // Create a patient record with all available fields
                        $patientRecord = [
                            'id' => $key, // Firebase unique key
                            'name' => $patient['name'] ?? 'Unknown',
                            'id_no' => $patient['id_no'] ?? 'N/A',
                            'gender' => $patient['gender'] ?? 'N/A',
                            'age' => $patient['age'] ?? 'N/A',
                            'dob' => $patient['dob'] ?? 'N/A',
                            'visit_no' => $patient['visit_no'] ?? 'N/A',
                            'location' => $patient['location'] ?? 'N/A',
                            'contact' => $patient['contact'] ?? 'N/A',
                            'nationality' => $patient['nationality'] ?? 'N/A',
                            'race' => $patient['race'] ?? 'N/A',
                            'blood_type' => $patient['blood_type'] ?? 'N/A',
                            'height' => $patient['height'] ?? 'N/A',
                            'weight' => $patient['weight'] ?? 'N/A',
                            'allergies' => $patient['allergies'] ?? null,
                            'medical_alerts' => $patient['medical_alerts'] ?? null,
                            'medical_history' => $patient['medical_history'] ?? null,
                            'family_history' => $patient['family_history'] ?? null,
                            'chronic_conditions' => $patient['chronic_conditions'] ?? null,
                            'current_medications' => $patient['current_medications'] ?? null,
                            'bmi' => $patient['bmi'] ?? null,
                            'bmi_status' => $patient['bmi_status'] ?? null,
                            'created_at' => $patient['created_at'] ?? null,
                            'created_by' => $patient['created_by'] ?? null,
                            'has_screening' => false
                        ];

                        // Add screening data if available
                        if ($mergedData) {
                            $patientRecord['has_screening'] = true;
                            $patientRecord['bloodpm'] = $mergedData['bloodpm'] ?? null;
                            $patientRecord['tempreturec'] = $mergedData['tempreturec'] ?? null;
                            $patientRecord['plusec'] = $mergedData['plusec'] ?? null;
                            $patientRecord['respiratingr'] = $mergedData['respiratingr'] ?? null;
                            // If merged data has more accurate BMI info, use it
                            if (isset($mergedData['bmi'])) {
                                $patientRecord['bmi'] = $mergedData['bmi'];
                                $patientRecord['bmi_status'] = $mergedData['bmi_status'] ?? null;
                            }
                        }

                        $patients[$key] = $patientRecord;
                    }
                }
            }

            // Sort patients by most recently created first
            if (!empty($patients)) {
                uasort($patients, function($a, $b) {
                    $aTime = isset($a['created_at']) ? strtotime($a['created_at']) : 0;
                    $bTime = isset($b['created_at']) ? strtotime($b['created_at']) : 0;
                    return $bTime - $aTime; // Most recent first
                });
            }

            \Log::info('Search completed with ' . count($patients) . ' results.');

            // Get search metrics for the view
            $totalPatients = count($patientsSnapshot);
            $resultCount = count($patients);

            return view('firebase.nurse.search_patient', compact('patients', 'searchQuery', 'searchType', 'totalPatients', 'resultCount'));
        } catch (\Exception $e) {
            \Log::error('Firebase Search Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Search failed: ' . $e->getMessage());
        }
    }

    //Qr code -> checkin
    public function checkin($id)
    {
        // Reference to the 'register_patient' node in Firebase
        $reference = $this->database->getReference($this->registerpatient . '/' . $id);

        // Get the patient data from Firebase
        $patient = $reference->getValue();

        // Check if the patient data exists
        if (empty($patient)) {
            abort(404, 'Patient not found.');
        }

        // Construct the full URL for the check-in
        $url = url('/nurse/patient/checkin/scan/' . $id);  // Use 'scan' for patient to check-in

        // Generate the QR Code
        $qrCode = QrCode::size(300)->generate($url); // Generate QR code directly from the URL

        // Pass the patient and QR code HTML to the view
        return view('firebase.nurse.checkin', compact('patient', 'qrCode'));
    }

    //data went scan
    public function scanCheckin($id)
    {
        // Reference to the 'register_patient' node in Firebase
        $reference = $this->database->getReference($this->registerpatient . '/' . $id);

        // Get the patient data from Firebase
        $patient = $reference->getValue();

        // Check if the patient data exists
        if (empty($patient)) {
            abort(404, 'Patient not found.');
        }

        // Store the check-in data in the 'patient_checkin' node in Firebase
        $checkinReference = $this->database->getReference($this->patient_checkin);

        // Prepare the check-in data to be stored
        $checkinData = [
            'patient_id' => $id,
            'patient_name' => $patient['name'],
            'checkin_time' => now(), // Store the current time in Firebase format
        ];

        // Push the data to Firebase
        $checkinReference->push($checkinData);

        // Optionally, return a response or redirect
        return response()->json(['status' => 'Check-in successful', 'message' => 'Patient has successfully checked in.']);
    }
    //edit patient controller
    public function editPatient($id)
    {
        try {
            // Reference to the 'register_patient' node in Firebase
            $reference = $this->database->getReference($this->registerpatient . '/' . $id);

            // Get the patient data from Firebase
            $patient = $reference->getValue();

            // Check if the patient data exists
            if (empty($patient)) {
                return redirect()->route('firebase.nurse.search')
                    ->with('error', 'Patient not found.');
            }

            // Convert patient array to object for blade template compatibility
            $patientObj = json_decode(json_encode($patient));
            $patientObj->id = $id; // Add the Firebase key as id

            // Check for edit history if available
            $editHistoryRef = $this->database->getReference('patient_edit_history/' . $id);
            $edits = $editHistoryRef->getValue() ?: [];

            // Convert to array format for the view
            $editsArray = [];
            foreach ($edits as $editKey => $editData) {
                $editData['key'] = $editKey;
                $editsArray[] = json_decode(json_encode($editData));
            }

            // Sort edit history by updated_at timestamp (most recent first)
            usort($editsArray, function($a, $b) {
                return strtotime($b->updated_at ?? 0) - strtotime($a->updated_at ?? 0);
            });

            return view('firebase.nurse.editpatient', compact('patientObj', 'editsArray'));
        } catch (\Exception $e) {
            \Log::error('Error loading patient edit form: ' . $e->getMessage());
            return redirect()->route('firebase.nurse.search')
                ->with('error', 'Error loading patient data: ' . $e->getMessage());
        }
    }

    public function updatePatient(Request $request, $id)
    {
        try {
            // Validate input fields
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'gender' => 'required|in:Male,Female,Other',
                'location' => 'nullable|string|max:255',
                'id_no' => 'nullable|string|max:50',
                'dob' => 'required|date',
                'nationality' => 'nullable|string|max:255',
                'visit_no' => 'nullable|string|max:50',
                'age' => 'nullable|numeric|min:0|max:120',
                'race' => 'nullable|string|max:255',
                'allergies' => 'nullable|string|max:500',
                'medical_alerts' => 'nullable|string|max:500',
                'height' => 'nullable|numeric|min:1|max:300',
                'weight' => 'nullable|numeric|min:1|max:500',
                'blood_type' => 'nullable|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
                'contact' => 'nullable|string|max:50',
                'medical_history' => 'nullable|string|max:1000',
                'family_history' => 'nullable|string|max:1000',
            ]);

            // Reference to the patient in Firebase
            $patientRef = $this->database->getReference($this->registerpatient . '/' . $id);

            // Get current patient data
            $currentPatient = $patientRef->getValue();

            if (!$currentPatient) {
                return redirect()->route('firebase.nurse.search')
                    ->with('error', 'Patient not found.');
            }

            // Track changes for audit log
            $changedFields = [];
            foreach ($validatedData as $key => $value) {
                // Check if the field exists and has changed
                if (isset($currentPatient[$key]) && $currentPatient[$key] != $value) {
                    $changedFields[] = $key;
                } elseif (!isset($currentPatient[$key]) && !empty($value)) {
                    $changedFields[] = $key; // New field added
                }
            }

            // Calculate BMI if height and weight are provided
            if ($request->filled('height') && $request->filled('weight') && $request->height > 0) {
                // Convert height from cm to meters
                $heightInMeters = $request->height / 100;

                // Calculate BMI
                $bmi = round($request->weight / ($heightInMeters * $heightInMeters), 1);

                // Determine BMI Status
                if ($bmi < 18.5) {
                    $bmiStatus = "Underweight";
                } elseif ($bmi >= 18.5 && $bmi < 24.9) {
                    $bmiStatus = "Normal";
                } elseif ($bmi >= 25 && $bmi < 29.9) {
                    $bmiStatus = "Overweight";
                } else {
                    $bmiStatus = "Obese";
                }

                // Add BMI to data
                $validatedData['bmi'] = $bmi . ' kg/m²';
                $validatedData['bmi_status'] = $bmiStatus;

                // Check if BMI changed
                if ((!isset($currentPatient['bmi']) || $currentPatient['bmi'] != $validatedData['bmi'])) {
                    $changedFields[] = 'bmi';
                }
            }

            // Add metadata
            $validatedData['updated_at'] = now()->toDateTimeString();
            $validatedData['updated_by'] = session('nurse_name') ?? 'Unknown Nurse';

            // Update the patient data in Firebase
            $patientRef->update($validatedData);

            // If BMI changed and we have merged patient data, update that too
            if (in_array('bmi', $changedFields) || in_array('height', $changedFields) || in_array('weight', $changedFields)) {
                $mergePatientRef = $this->database->getReference($this->merge_patient . '/' . $id);
                $mergePatient = $mergePatientRef->getValue();

                if ($mergePatient) {
                    $mergePatientRef->update([
                        'height' => $validatedData['height'] . ' cm',
                        'weight' => $validatedData['weight'] . ' kg',
                        'bmi' => $validatedData['bmi'],
                        'bmi_status' => $validatedData['bmi_status'],
                    ]);
                }
            }

            // Record edit history if changes were made
            if (!empty($changedFields)) {
                $historyData = [
                    'updated_at' => $validatedData['updated_at'],
                    'updated_by' => $validatedData['updated_by'],
                    'changes' => 'Updated: ' . implode(', ', $changedFields)
                ];

                $this->database->getReference('patient_edit_history/' . $id)->push($historyData);
            }

            return redirect()->route('firebase.nurse.editPatient', $id)
                ->with('success', 'Patient information updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Error updating patient: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error updating patient: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function viewPatient($id)
    {
        try {
            // Reference to the 'register_patient' node in Firebase
            $reference = $this->database->getReference($this->registerpatient . '/' . $id);

            // Get the patient data from Firebase
            $patient = $reference->getValue();

            // Check if the patient data exists
            if (empty($patient)) {
                return redirect()->route('firebase.nurse.search')
                    ->with('error', 'Patient not found.');
            }

            // Check if merged (screening) data exists
            $mergedData = $this->database->getReference($this->merge_patient . '/' . $id)->getValue();

            // Get vitals history if available
            $vitalsRef = $this->database->getReference('patient_vitals/' . $id);
            $vitals = $vitalsRef->getValue() ?: [];

            // Sort vitals by date (most recent first)
            uasort($vitals, function($a, $b) {
                return strtotime($b['recorded_at'] ?? 0) - strtotime($a['recorded_at'] ?? 0);
            });

            // Create a patient object with all available data
            $patientData = array_merge($patient, $mergedData ?: []);
            $patientObj = json_decode(json_encode($patientData));
            $patientObj->id = $id;

            return view('firebase.nurse.patient', compact('patientObj', 'vitals', 'mergedData'));
        } catch (\Exception $e) {
            \Log::error('Error viewing patient: ' . $e->getMessage());
            return redirect()->route('firebase.nurse.search')
                ->with('error', 'Error loading patient data: ' . $e->getMessage());
        }
    }


    /**
     * View all appointments
     */
    public function viewAppointments()
    {
        try {
            $nurseId = session('nurse_id');

            if (!$nurseId) {
                return redirect()->route('firebase.nurse.loginnurse')
                    ->with('error', 'Please login to view appointments');
            }

            // Get appointments from Firebase
            $appointmentsRef = $this->database->getReference('appointments');
            $appointments = $appointmentsRef->getValue() ?: [];

            // Process appointments for display
            $pendingAppointments = [];
            $upcomingAppointments = [];
            $pastAppointments = [];

            $today = now()->format('Y-m-d');

            foreach ($appointments as $id => $appointment) {
                // Skip if required fields are missing
                if (!isset($appointment['date'], $appointment['time'], $appointment['doctorId'], $appointment['doctorName'])) {
                    continue;
                }

                // Build appointment data
                $appointmentData = [
                    'id' => $id,
                    'date' => $appointment['date'],
                    'time' => $appointment['time'],
                    'doctorId' => $appointment['doctorId'],
                    'doctorName' => $appointment['doctorName'],
                    'status' => $appointment['status'] ?? 'pending',
                    'patientId' => $appointment['userId'] ?? null,
                    'patientName' => null,
                    'formattedDate' => date('l, F j, Y', strtotime($appointment['date'])),
                    'status_reason' => $appointment['status_reason'] ?? null
                ];

                // Get patient information if userId exists
                if (isset($appointment['userId'])) {
                    $patientRef = $this->database->getReference($this->registerpatient . '/' . $appointment['userId']);
                    $patientData = $patientRef->getValue();

                    if ($patientData) {
                        $appointmentData['patientName'] = $patientData['name'] ?? 'Unknown Patient';
                        $appointmentData['patientPhone'] = $patientData['contact'] ?? 'No contact info';
                    }
                }

                // Categorize appointments based on status and date
                $status = $appointmentData['status'];

                // Special statuses - completed, cancelled, declined go to past
                if ($status === 'completed' || $status === 'cancelled' || $status === 'declined') {
                    $pastAppointments[] = $appointmentData;
                }
                // Pending stays in pending
                else if ($status === 'pending') {
                    $pendingAppointments[] = $appointmentData;
                }
                // Past date appointments go to past
                else if ($appointment['date'] < $today) {
                    $pastAppointments[] = $appointmentData;
                }
                // Everything else (accepted, rescheduled) goes to upcoming
                else {
                    $upcomingAppointments[] = $appointmentData;
                }
            }

            // Sort appointments by date
            $sortByDate = function($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            };

            $sortByDateDesc = function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            };

            usort($pendingAppointments, $sortByDate);
            usort($upcomingAppointments, $sortByDate);
            usort($pastAppointments, $sortByDateDesc);

            return view('firebase.nurse.appointments', compact(
                'pendingAppointments',
                'upcomingAppointments',
                'pastAppointments'
            ));

        } catch (\Exception $e) {
            \Log::error('Error loading appointments: ' . $e->getMessage());
            return redirect()->route('firebase.nurse.dashboard')
                ->with('error', 'Error loading appointments: ' . $e->getMessage());
        }
    }



    /**
     * Update appointment status (accept/decline)
     */
    public function updateAppointmentStatus(Request $request, $id)
    {
        try {
            $nurseId = session('nurse_id');
            $nurseName = session('nurse_name', 'Nurse');

            if (!$nurseId) {
                return redirect()->route('firebase.nurse.loginnurse')
                    ->with('error', 'Please login to manage appointments');
            }

            // Validate request - UPDATED to include completed and cancelled status values
            $request->validate([
                'status' => 'required|in:accepted,declined,rescheduled,completed,cancelled',
                'reason' => 'nullable|string|max:500',
                'new_date' => 'required_if:status,rescheduled|date|nullable',
                'new_time' => 'required_if:status,rescheduled|string|nullable',
            ]);

            // Get the appointment
            $appointmentRef = $this->database->getReference('appointments/' . $id);
            $appointment = $appointmentRef->getValue();

            if (!$appointment) {
                return redirect()->back()->with('error', 'Appointment not found');
            }

            // Update appointment status
            $updateData = [
                'status' => $request->status,
                'updated_at' => now()->toDateTimeString(),
                'updated_by' => $nurseName,
                'nurse_id' => $nurseId
            ];

            // Add reason if provided
            if ($request->filled('reason')) {
                $updateData['status_reason'] = $request->reason;
            }

            // Handle rescheduling
            if ($request->status === 'rescheduled' && $request->filled('new_date') && $request->filled('new_time')) {
                $updateData['original_date'] = $appointment['date'];
                $updateData['original_time'] = $appointment['time'];
                $updateData['date'] = $request->new_date;
                $updateData['time'] = $request->new_time;
                $updateData['rescheduled_by'] = 'nurse';
            }

            // Handle completion - add completion timestamp
            if ($request->status === 'completed') {
                $updateData['completed_at'] = now()->toDateTimeString();
                $updateData['completed_by'] = $nurseName;
            }

            // Handle cancellation - add cancellation timestamp
            if ($request->status === 'cancelled') {
                $updateData['cancelled_at'] = now()->toDateTimeString();
                $updateData['cancelled_by'] = $nurseName;
            }

            // Update the appointment in Firebase
            $appointmentRef->update($updateData);

            // If accepted, notify the doctor
            if ($request->status === 'accepted' && isset($appointment['doctorId'])) {
                // Create a notification entry for the doctor
                $notificationData = [
                    'type' => 'new_appointment',
                    'title' => 'New Appointment',
                    'message' => 'You have a new appointment on ' .
                        date('l, F j', strtotime($appointment['date'])) .
                        ' at ' . $appointment['time'],
                    'appointment_id' => $id,
                    'patient_id' => $appointment['userId'] ?? null,
                    'patient_name' => $this->getPatientName($appointment['userId'] ?? null),
                    'date' => $appointment['date'],
                    'time' => $appointment['time'],
                    'is_read' => false,
                    'created_at' => now()->toDateTimeString()
                ];

                $this->database->getReference('doctor_notifications/' . $appointment['doctorId'])
                    ->push($notificationData);
            }

            // Notify patient about status change
            if (isset($appointment['userId'])) {
                $patientNotification = [
                    'type' => 'appointment_' . $request->status,
                    'title' => 'Appointment ' . ucfirst($request->status),
                    'message' => 'Your appointment on ' .
                        date('l, F j', strtotime($appointment['date'])) .
                        ' at ' . $appointment['time'] . ' has been ' . $request->status,
                    'appointment_id' => $id,
                    'is_read' => false,
                    'created_at' => now()->toDateTimeString()
                ];

                // Add rescheduled info if applicable
                if ($request->status === 'rescheduled') {
                    $patientNotification['message'] = 'Your appointment has been rescheduled to ' .
                        date('l, F j', strtotime($request->new_date)) .
                        ' at ' . $request->new_time;
                    $patientNotification['new_date'] = $request->new_date;
                    $patientNotification['new_time'] = $request->new_time;
                }

                // Customize message for completed appointments
                if ($request->status === 'completed') {
                    $patientNotification['message'] = 'Your appointment on ' .
                        date('l, F j', strtotime($appointment['date'])) .
                        ' at ' . $appointment['time'] . ' has been marked as completed.';
                }

                // Customize message for cancelled appointments
                if ($request->status === 'cancelled') {
                    $patientNotification['message'] = 'Your appointment on ' .
                        date('l, F j', strtotime($appointment['date'])) .
                        ' at ' . $appointment['time'] . ' has been cancelled.';

                    // Add reason if provided
                    if ($request->filled('reason')) {
                        $patientNotification['message'] .= ' Reason: ' . $request->reason;
                        $patientNotification['cancel_reason'] = $request->reason;
                    }
                }

                $this->database->getReference('patient_notifications/' . $appointment['userId'])
                    ->push($patientNotification);
            }

            return redirect()->route('firebase.nurse.appointments')
                ->with('success', 'Appointment has been ' . $request->status);

        } catch (\Exception $e) {
            \Log::error('Error updating appointment: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error updating appointment: ' . $e->getMessage());
        }
    }

    /**
     * Helper method to get patient name from patient ID
     */
    private function getPatientName($patientId)
    {
        if (!$patientId) {
            return 'Unknown Patient';
        }

        try {
            $patientRef = $this->database->getReference($this->registerpatient . '/' . $patientId);
            $patientData = $patientRef->getValue();

            if ($patientData && isset($patientData['name'])) {
                return $patientData['name'];
            }

            return 'Unknown Patient';
        } catch (\Exception $e) {
            \Log::error('Error getting patient name: ' . $e->getMessage());
            return 'Unknown Patient';
        }
    }

    private function getRecentAppointments($limit = 4)
    {
        try {
            $today = now()->format('Y-m-d');
            $tomorrow = now()->addDay()->format('Y-m-d');

            // Get appointments from Firebase
            $appointmentsRef = $this->database->getReference('appointments');
            $appointments = $appointmentsRef->getValue() ?: [];

            $recentAppointments = [];

            foreach ($appointments as $id => $appointment) {
                // Skip if required fields are missing
                if (!isset($appointment['date'], $appointment['time'], $appointment['doctorId'], $appointment['doctorName'])) {
                    continue;
                }

                // Only include today's and tomorrow's appointments
                if ($appointment['date'] < $today || $appointment['date'] > $tomorrow) {
                    continue;
                }

                // Build appointment data
                $appointmentData = [
                    'id' => $id,
                    'date' => $appointment['date'],
                    'time' => $appointment['time'],
                    'doctorId' => $appointment['doctorId'],
                    'doctorName' => $appointment['doctorName'],
                    'status' => $appointment['status'] ?? 'pending',
                    'patientId' => $appointment['userId'] ?? null,
                    'patientName' => null
                ];

                // Get patient information if userId exists
                if (isset($appointment['userId'])) {
                    $patientRef = $this->database->getReference($this->registerpatient . '/' . $appointment['userId']);
                    $patientData = $patientRef->getValue();

                    if ($patientData) {
                        $appointmentData['patientName'] = $patientData['name'] ?? 'Unknown Patient';
                        $appointmentData['patientPhone'] = $patientData['contact'] ?? 'No contact info';
                    }
                }

                $recentAppointments[] = $appointmentData;
            }

            // Sort by date/time
            usort($recentAppointments, function($a, $b) {
                // Compare dates first
                $dateCompare = strcmp($a['date'], $b['date']);
                if ($dateCompare !== 0) {
                    return $dateCompare;
                }
                // If same date, compare times
                return strcmp($a['time'], $b['time']);
            });

            // Return limited number of appointments
            return array_slice($recentAppointments, 0, $limit);

        } catch (\Exception $e) {
            \Log::error('Get recent appointments error: ' . $e->getMessage());
            return [];
        }
    }
}
