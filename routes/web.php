<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Firebase\Contactcontroller;
use App\Http\Controllers\Firebase\AdminController;
use App\Http\Controllers\Firebase\Doctorcontroller;
use App\Http\Controllers\Firebase\Nursecontroller;
use App\Http\Controllers\Firebase\Pharmacistcontroller;

Route::get('/', function () {
    return view('welcome');
});



// route for super admin
Route::get('createadmin', [Contactcontroller::class, 'createadmin']); // register admin
Route::post('createadmin', [Contactcontroller::class, 'storeadmin']);// store admin
Route::get('addadmin', [Contactcontroller::class, 'addadmin']); // show admin
Route::get('edit-admin/{id}', [Contactcontroller::class, 'editadmin']); // edit admin
Route::put('update-admin/{id}',[Contactcontroller::class, 'updateadmin']);// update admin
Route::get('delete-admin/{id}', [Contactcontroller::class, 'deleteadmin']); // delete admin
Route::get('/admin/audit-logs', [AdminController::class, 'auditLogs'])
    ->name('firebase.admin.audit_logs')
    ->middleware(['admin_auth']);

//route for admin
Route::prefix('admin')->name('firebase.admin.')->group(function () {

    Route::get('login', [AdminController::class, 'index'])->name('loginadmin');
    Route::post('admin/sublogin', [AdminController::class, 'loginadmin'])->name('loginadmin.submit');
    Route::get('dashboard', [Admincontroller::class, 'dashboard'])->name('dashboard');
    Route::post('logout', [AdminController::class, 'logout'])->name('logout'); // Define the logout route



// admin-doctor route
    Route::get('registration doctor', [AdminController::class, 'reg_doctor'])->name('reg_doctor');// register doctor
    Route::post('registration doctor', [AdminController::class, 'store_doctor'])->name('store_doctor');// store doctor registration
    Route::get('show-doctor', [AdminController::class, 'show_doctor'])->name('show_doctor');// show doctor
    Route::get('edit-doctor/{id}', [AdminController::class, 'edit_doctor'])->name('edit_doctor'); // edit doctor
    Route::put('update-doctor/{id}',[AdminController::class, 'updatedoctor'])->name('update_doctor');// update doctor
    Route::get('delete-doctor/{id}', [AdminController::class, 'deletedoctor'])->name('delete_doctor');

//admin-nurse route
    Route::get('registration nurse', [AdminController::class, 'reg_nurse'])->name('reg_nurse');// register nurse
    Route::post('registration nurse', [AdminController::class, 'store_nurse'])->name('store_nurse');// store doctor registration
    Route::get('show-nurse', [AdminController::class, 'show_nurse'])->name('show_nurse');// show nurse
    Route::get('edit-nurse/{id}', [AdminController::class, 'edit_nurse'])->name('edit_nurse'); // edit nurse
    Route::put('update-nurse/{id}',[AdminController::class, 'updatenurse'])->name('update_nurse');// update nurse
    Route::get('delete-nurse/{id}', [AdminController::class, 'deletenurse'])->name('delete_nurse');

//admin-pharmacist route
    Route::get('registration pharmacist', [AdminController::class, 'reg_pharmacist'])->name('reg_pharmacist');// register nurse
    Route::post('registration pharmacist', [AdminController::class, 'store_pharmacist'])->name('store_pharmacist');// store doctor registration
    Route::get('show-pharmacist', [AdminController::class, 'show_pharmacist'])->name('show_pharmacist');// show nurse
    Route::get('edit-pharmacist/{id}', [AdminController::class, 'edit_pharmacist'])->name('edit_pharmacist'); // edit nurse
    Route::put('update-pharmacist/{id}',[AdminController::class, 'updatepharmacist'])->name('update_pharmacist');// update nurse
    Route::get('delete-pharmacist/{id}',[AdminController::class, 'deletepharmacist'])->name('delete_pharmacist');// delete nurse
});

// route for doctor
Route::prefix('doctor')->name('firebase.doctor.')->group(function () {

    Route::get('login', [DoctorController::class, 'index'])->name('logindoctor');
    Route::post('doctor/sublogin', [DoctorController::class, 'logindoctor'])->name('logindoctor.submit');
    Route::get('dashboard', [Doctorcontroller::class, 'dashboard'])->name('dashboard');
    Route::post('logout', [DoctorController::class, 'logout'])->name('logout'); // Define the logout route
    Route::get('medical-report', [DoctorController::class, 'medical_report'])->name('medical_report');// Route to display the medical report page with patient details (GET request)
    Route::post('medical-report', [DoctorController::class, 'storeMedicalReport'])->name('storeMedicalReport');// Route to handle storing the medical report (POST request)
    Route::get('doctor/search_patient', [DoctorController::class, 'search'])->name('search'); // Search patient form
    Route::get('doctor/search_result', [DoctorController::class, 'searchPatient'])->name('searchPatient'); // Display search results

    // Doctor Appointment Routes
    Route::get('/doctor/appointments', [Doctorcontroller::class, 'appointments'])->name('appointments');
    Route::post('/doctor/appointments/{id}/update', [Doctorcontroller::class, 'updateAppointmentStatus'])->name('updateAppointment');



});

//route for nurse
Route::prefix('nurse')->name('firebase.nurse.')->group(function () {

    Route::get('login', [NurseController::class, 'index'])->name('loginnurse');
    Route::post('nurse/sublogin', [NurseController::class, 'loginnurse'])->name('loginnurse.submit');
    Route::get('dashboard', [Nursecontroller::class, 'dashboard'])->name('dashboard');
    Route::post('logout', [NurseController::class, 'logout'])->name('logout');
    Route::get('/sctest/{id}', [NurseController::class, 'sctest'])->name('sctest');
    Route::post('/sctest/store/{id}', [NurseController::class, 'store_sctest'])->name('store_sctest');
    Route::get('register_patient', [NurseController::class, 'register_patient'])->name('register_patient');// register_patient page
    Route::post('register_patient', [NurseController::class, 'storePatient'])->name('store_Patient'); // store patient
    Route::get('search-patient', [NurseController::class, 'search'])->name('search');// Route to display the search form
    Route::get('search-results', [NurseController::class, 'searchPatient'])->name('searchPatient');// Route to process search request and return results
    Route::get('view-patient/{id}', [NurseController::class, 'viewPatient'])->name('viewPatient');// Route to view individual patient details
    Route::get('/checkin/{id}', [NurseController::class, 'checkin'])->name('checkin');// Route for qr code generate
    Route::get('nurse/patient/checkin/scan/{id}', [NurseController::class, 'scanCheckin'])->name('scanCheckin');
    Route::get('/nurse/patient/{id}/edit', [Nursecontroller::class, 'editPatient'])->name('editPatient');
    Route::put('/nurse/patient/{id}', [Nursecontroller::class, 'updatePatient'])->name('update_patient');
    Route::get('/nurse/patient/{id}', [Nursecontroller::class, 'viewPatient'])->name('patient');

    // Chat routes
    Route::get('/nurse/chats', [Nursecontroller::class, 'allChats'])->name('allChats');
    Route::get('/nurse/chats/{chatId}', [Nursecontroller::class, 'viewChat'])->name('viewChat');
    Route::post('/nurse/chats/{chatId}/send', [Nursecontroller::class, 'sendMessage'])->name('sendMessage');
    Route::get('/nurse/patient/{patientId}/chat', [Nursecontroller::class, 'startChat'])->name('startChat');

    // Appointment management routes
    Route::get('/appointments', [Nursecontroller::class, 'viewAppointments'])->name('appointments');
    Route::post('/appointments/{id}/update', [Nursecontroller::class, 'updateAppointmentStatus'])->name('appointment.update');
    Route::get('/appointments/create', [Nursecontroller::class, 'createAppointment'])->name('appointment.create');
    Route::post('/appointments/store', [Nursecontroller::class, 'storeAppointment'])->name('appointment.store');

});

//route for pharmacist
Route::prefix('pharmacist')->name('firebase.pharmacist.')->group(function () {

    Route::get('login', [PharmacistController::class, 'index'])->name('loginpharmacist');
    Route::post('pharmacist/sublogin', [PharmacistController::class, 'loginpharmacist'])->name('loginpharmacist.submit');
    Route::get('dashboard', [Pharmacistcontroller::class, 'dashboard'])->name('dashboard');
    Route::post('logout', [PharmacistController::class, 'logout'])->name('logout'); // Define the logout route
    Route::get('pharmacist/search_patient', [PharmacistController::class, 'search'])->name('search'); // Search patient form
    Route::get('pharmacist/search_result', [PharmacistController::class, 'searchPatient'])->name('searchPatient'); // Display search results
    // Patient medication routes
    Route::get('/patient/{patientId}/medications', [Pharmacistcontroller::class, 'patientMedications'])->name('patient.medications');
    Route::post('/mark-medication-pickup/{prescriptionId}', [Pharmacistcontroller::class, 'markMedicationPickup'])->name('mark-medication-pickup');

    // Pharmacist Inventory Routes
    Route::get('/inventory', [Pharmacistcontroller::class, 'inventory'])->name('inventory');
    Route::post('/inventory/add', [Pharmacistcontroller::class, 'addInventory'])->name('inventory.add');
    Route::post('/inventory/update', [Pharmacistcontroller::class, 'updateInventory'])->name('inventory.update');
    Route::delete('/inventory/delete/{id}', [Pharmacistcontroller::class, 'deleteInventory'])->name('inventory.delete');

    // Custom medication dispensing
    Route::post('/custom-dispense', [Pharmacistcontroller::class, 'customDispenseMedications'])->name('custom-dispense');

});
