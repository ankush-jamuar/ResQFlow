<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\HospitalController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmergencyController;
use App\Http\Controllers\MedicalReportController;
use App\Http\Controllers\FamilyMemberController;

Route::get('/', function () {
    $familyMembers = auth()->check() ? auth()->user()->familyMembers : collect();
    return view('welcome', compact('familyMembers'));
});

// Public/Guest Emergency routes
Route::post('/emergency', [EmergencyController::class, 'store'])->middleware('throttle:sos')->name('emergency.store');
Route::get('/emergency/track', [EmergencyController::class, 'track'])->name('emergency.track');
Route::post('/emergency/{emergency}/cancel', [EmergencyController::class, 'cancel'])->name('emergency.cancel');

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/history', [UserController::class, 'history'])->name('user.history');

    // Added missing sidebar profile route
    Route::get('/user/profile', [UserController::class, 'medicalProfile'])->name('user.profile');
    
    // Family Health Ecosystem
    Route::get('/profile/medical', [UserController::class, 'medicalProfile'])->name('user.medical');
    Route::post('/profile/medical', [UserController::class, 'updateMedicalProfile'])->name('user.medical.update');
    Route::post('/family', [UserController::class, 'storeFamilyMember'])->name('user.family.store');
    Route::delete('/family/{member}', [UserController::class, 'destroyFamilyMember'])->name('user.family.destroy');
    
    // AI Integration
    Route::post('/api/ai/parse-report', [UserController::class, 'parseMedicalReport'])->name('api.ai.parse');
});

Route::middleware(['auth', 'role:hospital', 'impersonation.log'])->group(function () {
    Route::get('/hospital/dashboard', [HospitalController::class, 'dashboard'])->name('hospital.dashboard');
    
    // Fleet (Ambulance) CRUD
    Route::get('/hospital/fleet', [HospitalController::class, 'fleet'])->name('hospital.fleet');
    Route::post('/hospital/fleet', [HospitalController::class, 'storeAmbulance'])->name('hospital.fleet.store');
    Route::post('/hospital/fleet/{ambulance}/maintenance', [HospitalController::class, 'logMaintenance'])->name('hospital.fleet.maintenance');
    Route::put('/hospital/fleet/{ambulance}', [HospitalController::class, 'updateAmbulance'])->name('hospital.fleet.update');
    Route::delete('/hospital/fleet/{ambulance}', [HospitalController::class, 'destroyAmbulance'])->name('hospital.fleet.destroy');

    // Drivers & Resources
    Route::get('/hospital/drivers', [HospitalController::class, 'drivers'])->name('hospital.drivers');
    Route::get('/hospital/resources', [HospitalController::class, 'resources'])->name('hospital.resources');
    Route::post('/hospital/resources', [HospitalController::class, 'storeResource'])->name('hospital.resources.store');
    Route::put('/hospital/resources/{resource}', [HospitalController::class, 'updateResource'])->name('hospital.resources.update');

    // Mission Queue
    Route::get('/hospital/queue', [HospitalController::class, 'queue'])->name('hospital.queue');
    
    // Emergency dispatch actions
    Route::post('/emergency/{emergency}/status', [EmergencyController::class, 'updateStatus'])->name('emergency.updateStatus');
    Route::post('/emergency/{emergency}/reject', [EmergencyController::class, 'reject'])->name('emergency.reject');
    Route::get('/hospital/emergencies/latest', [HospitalController::class, 'latestEmergencies'])->name('hospital.emergencies.latest');
});


Route::middleware(['auth', 'role:admin', 'impersonation.log'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/hospitals', [AdminController::class, 'hospitals'])->name('admin.hospitals');
    
    // Authority Actions
    Route::post('/admin/hospitals/{hospital}/warn', [AdminController::class, 'issueWarning'])->name('admin.hospitals.warn');
    Route::post('/admin/hospitals/{hospital}/suspend', [AdminController::class, 'suspendHospital'])->name('admin.hospitals.suspend');
    Route::post('/admin/hospitals/{hospital}/reinstate', [AdminController::class, 'reinstateHospital'])->name('admin.hospitals.reinstate');
    Route::post('/admin/emergencies/{emergency}/reassign', [AdminController::class, 'forceReassign'])->name('admin.emergencies.reassign');
    
    Route::get('/admin/ambulances', [AdminController::class, 'ambulances'])->name('admin.ambulances');
    Route::get('/admin/drivers', [AdminController::class, 'drivers'])->name('admin.drivers');
    Route::get('/admin/emergencies', [AdminController::class, 'emergencies'])->name('admin.emergencies');
    Route::get('/admin/analytics', [AdminController::class, 'analytics'])->name('admin.analytics');
    Route::get('/admin/audits', [AdminController::class, 'audits'])->name('admin.audits');

    // System Health & Observability (PHASE 3)
    Route::get('/admin/system-health', [\App\Http\Controllers\Admin\SystemHealthController::class, 'index'])->name('admin.health');
    Route::get('/admin/api/system-stats', [\App\Http\Controllers\Admin\SystemHealthController::class, 'stats'])->name('admin.api.stats');
    Route::get('/admin/api/failed-jobs', [\App\Http\Controllers\Admin\SystemHealthController::class, 'failedJobs'])->name('admin.api.failed-jobs');
    Route::post('/admin/api/failed-jobs/{id}/retry', [\App\Http\Controllers\Admin\SystemHealthController::class, 'retryJob'])->name('admin.api.retry-job');
    Route::delete('/admin/api/failed-jobs/{id}', [\App\Http\Controllers\Admin\SystemHealthController::class, 'deleteJob'])->name('admin.api.delete-job');

    // Incident History & Replay (PHASE 3)
    Route::get('/admin/incidents/{id}', [\App\Http\Controllers\Admin\IncidentController::class, 'show'])->name('admin.incidents.show');

    Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::post('/admin/login-as-hospital', [AdminController::class, 'loginAsHospital'])->name('admin.loginAsHospital');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/admin/return', [AdminController::class, 'returnToAdmin'])->name('admin.return');

    // AI Medical Intelligence
    Route::prefix('medical')->name('medical.')->group(function () {
        Route::get('/reports', [MedicalReportController::class, 'index'])->name('reports.index');
        Route::post('/reports', [MedicalReportController::class, 'store'])->name('reports.store');
        Route::get('/reports/{report}', [MedicalReportController::class, 'show'])->name('reports.show');
        Route::get('/reports/{report}/download', [MedicalReportController::class, 'download'])
            ->name('reports.download')
            ->middleware('signed');
        Route::get('/reports/{report}/preview', [MedicalReportController::class, 'preview'])
            ->name('reports.preview')
            ->middleware('signed');
    });

    // Family Health Ecosystem
    Route::prefix('family')->name('family.')->group(function () {
        Route::get('/members', [FamilyMemberController::class, 'index'])->name('members.index');
        Route::post('/members', [FamilyMemberController::class, 'store'])->name('members.store');
        Route::delete('/members/{member}', [FamilyMemberController::class, 'destroy'])->name('members.destroy');
        Route::get('/members/{member}/health', [UserController::class, 'getFamilyMemberHealth'])->name('members.health');
    });
});

Route::get('/api/ping', function () {
    return response()->json(['status' => 'ok']);
})->name('api.ping');

Route::post('/notifications/clear', function () {
    auth()->user()->unreadNotifications->markAsRead();
    return back();
})->middleware('auth')->name('notifications.clear');

require __DIR__.'/auth.php';