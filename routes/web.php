<?php

use App\Http\Controllers\IssuerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\CertificateIssuanceController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\SchoolProfileController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\Student\StudentAuthController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\StudentProfileController;
use App\Http\Controllers\Student\StudentPasswordResetController;

// Public routes
Route::get('/', function () {
    return redirect()->route('verification.form');
});

// Public certificate verification (Rate Limited: 20 requests per minute)
Route::middleware('throttle:20,1')->group(function () {
    Route::get('/verify/{certificateId}', [VerificationController::class, 'verify'])->name('verify');
    Route::get('/verification', [VerificationController::class, 'showForm'])->name('verification.form');
    Route::post('/verification/search', [VerificationController::class, 'search'])->name('verification.search');
});

// Public certificate download via secure token (Rate Limited: 10 downloads per minute)
Route::middleware('throttle:10,1')->group(function () {
    Route::get('/certificate/download/{token}', [CertificateController::class, 'downloadViaToken'])->name('certificate.download.token');
});

// Authentication routes (Rate Limited: 5 login attempts per minute to prevent brute force)
Route::middleware(['guest', 'throttle:20,1'])->prefix('staff')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Password Change (accessible to all authenticated users)
    Route::get('/password/change', [PasswordChangeController::class, 'showForm'])->name('password.change');
    Route::post('/password/change', [PasswordChangeController::class, 'update'])->name('password.update');

    // Dashboard (accessible to both roles)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Super Admin routes
    Route::middleware('super_admin')->group(function () {
        // School management
        Route::resource('schools', SchoolController::class);
        Route::post('/schools/{school}/toggle-status', [SchoolController::class, 'toggleStatus'])->name('schools.toggle-status');

        // School approval workflow
        Route::get('/admin/schools/pending', [SchoolController::class, 'pending'])->name('schools.pending');
        Route::post('/admin/schools/{school}/approve', [SchoolController::class, 'approve'])->name('schools.approve');
        Route::post('/admin/schools/{school}/reject', [SchoolController::class, 'reject'])->name('schools.reject');
        Route::post('/admin/schools/{school}/suspend', [SchoolController::class, 'suspend'])->name('schools.suspend');

        // Package management
        Route::resource('packages', PackageController::class);
        Route::post('/packages/{package}/toggle-status', [PackageController::class, 'toggleStatus'])->name('packages.toggle-status');

        // Template management
        Route::resource('templates', TemplateController::class);
        Route::get('/templates/{template}/preview', [TemplateController::class, 'preview'])->name('templates.preview');
        Route::post('/templates/{template}/toggle-status', [TemplateController::class, 'toggleStatus'])->name('templates.toggle-status');

        // Super Admin Analytics
        Route::get('/analytics/super-admin', [AnalyticsController::class, 'superAdmin'])->name('analytics.super');

        // Invoice management (Super Admin can view all invoices)
        Route::get('/invoices/overdue', [InvoiceController::class, 'overdue'])->name('invoices.overdue');
    });

    // API endpoint for getting class sections (accessible to all authenticated users)
    Route::get('/api/classes/{class}/sections', [SchoolClassController::class, 'getSections'])->name('classes.sections');

    // School Admin routes
    Route::middleware('school_admin')->group(function () {
        // Events/Competitions
        Route::resource('events', EventController::class);

        // Classes
        Route::resource('classes', SchoolClassController::class);

        // Issuer Management
        Route::resource('issuers', IssuerController::class);
        Route::post('/issuers/{issuer}/toggle-status', [IssuerController::class, 'toggleStatus'])->name('issuers.toggle-status');

        // Certificate Approvals
        Route::prefix('approvals')->name('approvals.')->group(function () {
            Route::get('/', [ApprovalController::class, 'index'])->name('index');
            Route::post('/certificates/{certificate}/approve', [ApprovalController::class, 'approveCertificate'])->name('approve');
            Route::post('/certificates/{certificate}/reject', [ApprovalController::class, 'rejectCertificate'])->name('reject');
            Route::post('/bulk-approve', [ApprovalController::class, 'bulkApprove'])->name('bulk-approve');
            Route::post('/bulk-reject', [ApprovalController::class, 'bulkReject'])->name('bulk-reject');
        });

        // School Profile Management
        Route::get('/school/profile', [SchoolProfileController::class, 'edit'])->name('school.profile');
        Route::post('/school/profile', [SchoolProfileController::class, 'update'])->name('school.profile.update');
        Route::post('/school/profile/delete-image', [SchoolProfileController::class, 'deleteImage'])->name('school.profile.delete-image');
    });

    // Issuer, School Admin, and Super Admin routes (Certificate Issuance)
    Route::middleware('issuer')->group(function () {
        // Multi-step Certificate Issuance Wizard
        Route::prefix('issue')->name('issue.')->group(function () {
            Route::get('/', [CertificateIssuanceController::class, 'step1'])->name('step1');
            Route::get('/load-students', [CertificateIssuanceController::class, 'loadStudents'])->name('load-students');
            Route::post('/step2', [CertificateIssuanceController::class, 'step2'])->name('step2');
            Route::post('/step3', [CertificateIssuanceController::class, 'step3'])->name('step3');
            Route::match(['get', 'post'], '/step4', [CertificateIssuanceController::class, 'step4'])->name('step4');
            Route::post('/confirm', [CertificateIssuanceController::class, 'confirm'])->name('confirm');
        });
    });

    // Analytics (accessible to all authenticated users)
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // School Admin and Super Admin routes
    Route::group([], function () {
        // Student management
        Route::resource('students', StudentController::class);
        Route::get('/students/import/form', [StudentController::class, 'importForm'])->name('students.import.form');
        Route::post('/students/import', [StudentController::class, 'import'])->name('students.import');

        // Student credential management
        Route::post('/students/{student}/generate-credentials', [StudentController::class, 'generateCredentials'])->name('students.generate-credentials');
        Route::post('/students/{student}/send-credentials', [StudentController::class, 'sendCredentials'])->name('students.send-credentials');
        Route::post('/students/{student}/toggle-active', [StudentController::class, 'toggleActive'])->name('students.toggle-active');
        Route::post('/students/{student}/reset-password', [StudentController::class, 'resetPassword'])->name('students.reset-password');

        // Certificate management
        Route::resource('certificates', CertificateController::class)->only(['index', 'create', 'show']);
        Route::post('/certificates/generate', [CertificateController::class, 'generate'])->name('certificates.generate');
        Route::get('/certificates/{certificate}/download', [CertificateController::class, 'download'])->name('certificates.download');
        Route::post('/certificates/bulk-print', [CertificateController::class, 'bulkPrint'])->name('certificates.bulk-print');

        // Send certificate notifications
        Route::post('/certificates/{certificate}/send-email', [CertificateController::class, 'sendEmail'])->name('certificates.send-email');
        Route::post('/certificates/{certificate}/send-whatsapp', [CertificateController::class, 'sendWhatsApp'])->name('certificates.send-whatsapp');

        // Invoice management (accessible to both Super Admin and School Admin)
        Route::resource('invoices', InvoiceController::class)->only(['index', 'show', 'edit', 'update']);
        Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
    });
});

// ============================================
// STUDENT PORTAL ROUTES
// ============================================

// Public Student Profile (LinkedIn-style)
Route::get('/profile/{username}', [PublicProfileController::class, 'show'])->name('profile.public');

// Student Authentication Routes (Guest Only)
Route::middleware(['student.guest', 'throttle:20,1'])->name('student.')->group(function () {
    Route::get('/login', [StudentAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [StudentAuthController::class, 'login']);

    // Registration disabled - Students are imported by schools
    // Route::get('/register', [StudentAuthController::class, 'showRegister'])->name('register');
    // Route::post('/register', [StudentAuthController::class, 'register']);

    // Password Reset Routes
    Route::get('/forgot-password', [StudentPasswordResetController::class, 'showResetRequestForm'])->name('password.request');
    Route::post('/forgot-password', [StudentPasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [StudentPasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [StudentPasswordResetController::class, 'reset'])->name('password.update');
});

// Student Authenticated Routes
Route::middleware(['student.auth'])->prefix('student')->name('student.')->group(function () {
    // Logout
    Route::post('/logout', [StudentAuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

    // Certificate Visibility Toggle
    Route::post('/certificates/{certificate}/toggle-visibility', [StudentDashboardController::class, 'toggleCertificateVisibility'])
        ->name('certificates.toggle-visibility');

    // Profile Management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/edit', [StudentProfileController::class, 'edit'])->name('edit');
        Route::post('/update', [StudentProfileController::class, 'update'])->name('update');
        Route::post('/delete-picture', [StudentProfileController::class, 'deleteProfilePicture'])->name('delete-picture');
        Route::post('/update-password', [StudentProfileController::class, 'updatePassword'])->name('update-password');

        // AJAX endpoint for checking username availability
        Route::post('/check-username', [StudentProfileController::class, 'checkUsername'])->name('check-username');
    });
});
