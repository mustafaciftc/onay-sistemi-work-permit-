<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FormTemplateController;
use App\Http\Controllers\PositionController;
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
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminController::class, 'users'])->name('index');
        Route::post('/', [AdminController::class, 'createUser'])->name('create');
        Route::get('{user}/edit', [AdminController::class, 'getUserForEdit'])->name('edit');
        Route::put('{user}', [AdminController::class, 'updateUser'])->name('update');
        Route::delete('{user}', [AdminController::class, 'deleteUser'])->name('delete');
    });

    // Şirket Yönetimi
    Route::prefix('companies')->name('companies.')->group(function () {
        Route::get('/', [AdminController::class, 'companies'])->name('index');
        Route::post('/', [AdminController::class, 'createCompany'])->name('create');
        Route::get('{company}/edit', [AdminController::class, 'getCompanyForEdit'])->name('edit');
        Route::put('{company}', [AdminController::class, 'updateCompany'])->name('update');
        Route::delete('{company}', [AdminController::class, 'deleteCompany'])->name('delete');
        Route::post('{company}/toggle-status', [AdminController::class, 'toggleCompanyStatus'])->name('toggle-status');
    });

    Route::prefix('departments')->name('departments.')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('index');
        Route::post('/', [DepartmentController::class, 'store'])->name('store');
        Route::get('/{department}/edit', [DepartmentController::class, 'edit'])->name('edit');
        Route::put('/{department}', [DepartmentController::class, 'update'])->name('update');
        Route::delete('/{department}', [DepartmentController::class, 'destroy'])->name('destroy');
        Route::patch('/{department}/toggle', [DepartmentController::class, 'toggleStatus'])->name('toggle');
        Route::get('/{department}/positions', [DepartmentController::class, 'getPositions'])->name('positions');
    });


    Route::prefix('positions')->name('positions.')->group(function () {
        Route::post('/', [PositionController::class, 'store'])->name('store');
        Route::get('{position}/edit', [PositionController::class, 'edit'])->name('edit');
        Route::put('{position}', [PositionController::class, 'update'])->name('update');
        Route::delete('{position}', [PositionController::class, 'destroy'])->name('destroy');
        Route::patch('{position}/toggle', [PositionController::class, 'toggleStatus'])->name('toggle');
    });

    // İş İzin Yönetimi
    Route::prefix('work-permits')->name('work-permits.')->controller(WorkPermitController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('{workPermit}', 'show')->name('show');


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
       Route::get('departments/{department}/positions', 'getPositionsByDepartment')
        ->name('departments.positions');

        Route::get('{workPermit}/closing-form', 'showClosingForm')->name('closing-form');
        Route::post('{workPermit}/initiate-closing', 'initiateClosing')->name('initiate-closing');
        Route::get('{workPermit}/pdf', 'generatePdf')->name('pdf');
        Route::get('{workPermit}/view-pdf', 'viewFinalPdf')->name('view-pdf');

              // Onay İşlemleri
        Route::post('{workPermit}/approve-step', 'approveStep')->name('approve-step');
        Route::post('{workPermit}/initiate-closing', 'initiateClosing')->name('initiate-closing');
        Route::get('{workPermit}/pdf', 'generatePdf')->name('pdf');
        Route::get('{workPermit}/view-pdf', 'viewFinalPdf')->name('view-pdf');

        // PDF ve Email Route'ları
        Route::get('{workPermit}/final-pdf/view', 'viewFinalPdf')->name('final-pdf.view');
        Route::get('{workPermit}/final-pdf/download', 'downloadFinalPdf')->name('final-pdf.download');
        Route::post('{workPermit}/generate-final-pdf', 'generateFinalPdfManual')->name('generate-final-pdf');
        Route::post('{workPermit}/send-final-email', 'sendFinalEmailManual')->name('send-final-email');
        Route::get('/work-permits/{id}/final-pdf', [WorkPermitController::class, 'generateFinalPdf'])
            ->name('work-permits.final-pdf');

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
});

// ==================== FALLBACK ROUTE ====================

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
