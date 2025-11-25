<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FormTemplateController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ShareableLinkController;
use App\Http\Controllers\WorkPermitController;
use Illuminate\Support\Facades\Route;

// ==================== PUBLIC ROUTES ====================

Route::get('/', function () {
    return view('home');
})->name('home');

// ==================== AUTH ROUTES ====================

Route::middleware('guest')->controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
});

Route::middleware('auth')->post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==================== ADMIN ROUTES ====================

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Kullanıcı Yönetimi
    Route::prefix('users')->name('users.')->controller(AdminController::class)->group(function () {
        Route::get('/', 'users')->name('index');
        Route::post('/', 'createUser')->name('create');
        Route::get('{user}/edit', 'getUserForEdit')->name('edit');
        Route::put('{user}', 'updateUser')->name('update');
        Route::delete('{user}', 'deleteUser')->name('delete');
    });

    // Şirket Yönetimi
    Route::prefix('companies')->name('companies.')->controller(AdminController::class)->group(function () {
        Route::get('/', 'companies')->name('index');
        Route::post('/', 'createCompany')->name('create');
        Route::get('{company}/edit', 'getCompanyForEdit')->name('edit');
        Route::put('{company}', 'updateCompany')->name('update');
        Route::post('{company}/toggle-status', 'toggleCompanyStatus')->name('toggle-status');
    });

    // İş İzin Yönetimi - TEK MERKEZİ YÖNETİM
    Route::prefix('work-permits')->name('work-permits.')->controller(WorkPermitController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('{workPermit}', 'show')->name('show');

        // Onay İşlemleri - YENİ SİSTEM
        Route::post('{workPermit}/approve-step', 'approveStep')->name('approve-step');
        Route::post('{workPermit}/initiate-closing', 'initiateClosing')->name('initiate-closing');
        Route::get('{workPermit}/pdf', 'generatePdf')->name('pdf');
        Route::get('{workPermit}/view-pdf', 'viewFinalPdf')->name('view-pdf');

        // YENİ PDF VE EMAIL ROUTE'LARI - ADMIN İÇİN EKLENDİ
        Route::get('{workPermit}/final-pdf/view', 'viewFinalPdf')->name('final-pdf.view');
        Route::get('{workPermit}/final-pdf/download', 'downloadFinalPdf')->name('final-pdf.download');
        Route::post('{workPermit}/generate-final-pdf', 'generateFinalPdfManual')->name('generate-final-pdf');
        Route::post('{workPermit}/send-final-email', 'sendFinalEmailManual')->name('send-final-email');

        // Shareable Links
        Route::prefix('{workPermit}/shareable-links')
            ->name('shareable-links.')
            ->controller(ShareableLinkController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::delete('{shareableLink}', 'destroy')->name('destroy');
                Route::post('{shareableLink}/toggle', 'toggle')->name('toggle');
            });
    });
});

// ==================== COMPANY ROUTES ====================

Route::middleware(['auth', 'company'])->prefix('company')->name('company.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'company_index'])->name('dashboard');

    Route::get('/birim-amiri', [DashboardController::class, 'birimAmiriDashboard'])->name('birim-amiri');
    Route::get('/alan-amiri', [DashboardController::class, 'alanAmiriDashboard'])->name('alan-amiri');
    Route::get('/isg-uzmani', [DashboardController::class, 'isgUzmaniDashboard'])->name('isg-uzmani');
    Route::get('/isveren-vekili', [DashboardController::class, 'isverenVekiliDashboard'])->name('isveren-vekili');
    Route::get('/calisan', [DashboardController::class, 'calisanDashboard'])->name('calisan');

    // İş İzinleri Route'ları
    Route::prefix('work-permits')->name('work-permits.')->controller(WorkPermitController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('{workPermit}', 'show')->name('show');
        Route::post('{workPermit}/approve-step', 'approveStep')->name('approve-step');
        Route::get('{workPermit}/closing-form', 'showClosingForm')->name('closing-form');
        Route::post('{workPermit}/initiate-closing', 'initiateClosing')->name('initiate-closing');
        Route::get('{workPermit}/pdf', 'generatePdf')->name('pdf');
        Route::get('{workPermit}/view-pdf', 'viewFinalPdf')->name('view-pdf');

        // YENİ PDF VE EMAIL ROUTE'LARI
        Route::get('{workPermit}/final-pdf/view', 'viewFinalPdf')->name('final-pdf.view');
        Route::get('{workPermit}/final-pdf/download', 'downloadFinalPdf')->name('final-pdf.download');
        Route::post('{workPermit}/generate-final-pdf', 'generateFinalPdfManual')->name('generate-final-pdf');
        Route::post('{workPermit}/send-final-email', 'sendFinalEmailManual')->name('send-final-email');

        // Shareable Links
        Route::prefix('{workPermit}/shareable-links')
            ->name('shareable-links.')
            ->controller(ShareableLinkController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::delete('{shareableLink}', 'destroy')->name('destroy');
                Route::post('{shareableLink}/toggle', 'toggle')->name('toggle');
            });
    });

    Route::post('work-permits/{workPermit}/quick-approve', [DashboardController::class, 'quickApprove'])
        ->name('work-permits.quick-approve');

    // Şirket İşlemleri
    Route::prefix('companies')->name('companies.')->controller(CompanyController::class)->group(function () {
        Route::get('create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('switch/{company}', 'switchCompany')->name('switch');
    });

    // Departman Yönetimi
    Route::resource('departments', DepartmentController::class);

    // Form Template Yönetimi
    Route::resource('form-templates', FormTemplateController::class);
    Route::prefix('form-templates')->name('form-templates.')->group(function () {
        Route::post('{template}/set-default', [FormTemplateController::class, 'setDefault'])->name('set-default');
        Route::post('{template}/toggle-publish', [FormTemplateController::class, 'togglePublish'])->name('toggle-publish');
    });

    // Raporlama
    Route::prefix('reports')->name('reports.')->controller(ReportController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('work-permits', 'workPermits')->name('work-permits');
        Route::get('approvals', 'approvals')->name('approvals');
        Route::get('export', 'exportWorkPermits')->name('export');
        Route::get('stats', 'dashboardStats')->name('stats');
    });
});

// ==================== USER ROUTES ====================

Route::middleware('auth')->prefix('users')->name('users.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'users_index'])->name('dashboard');

    // İş İzinleri (Kullanıcı görünümü)
    Route::prefix('work-permits')->name('work-permits.')->controller(WorkPermitController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('{workPermit}', 'show')->name('show');
        Route::post('{workPermit}/approve-step', 'approveStep')->name('approve-step');

        // USER İÇİN PDF ROUTE'LARI (SADECE GÖRÜNTÜLEME)
        Route::get('{workPermit}/final-pdf/view', 'viewFinalPdf')->name('final-pdf.view');
        Route::get('{workPermit}/final-pdf/download', 'downloadFinalPdf')->name('final-pdf.download');
    });

    Route::prefix('departments')->name('departments.')->controller(DepartmentController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{department}/edit', 'edit')->name('edit');
        Route::put('/{department}', 'update')->name('update');
        Route::delete('/{department}', 'destroy')->name('destroy');
    });
});

Route::get('/departments/{department}/positions', [DepartmentController::class, 'positions'])
    ->name('departments.positions');

// ==================== FALLBACK ROUTE ====================

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});


