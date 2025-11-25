<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Enums\WorkPermitStatus;
use Illuminate\Support\Facades\Log;
use App\Models\WorkPermitForm;
use App\Models\WorkPermitApproval;
use Illuminate\Support\Facades\Validator;
use App\Models\Company;
use App\Models\CompanyDepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Notifications\WorkPermitApprovalNotification;
use App\Events\WorkPermitStatusUpdated;
use App\Events\NewApprovalAssigned;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Models\DepartmentPosition;
use Illuminate\Http\RedirectResponse;

class WorkPermitController extends Controller
{
    // 4 ADIMLI ONAY SİSTEMİ - OPTİMİZE EDİLMİŞ
    private const APPROVAL_STEPS = [
        1 => [
            'status' => 'pending_unit_approval',
            'role' => 'birim_amiri',
            'step' => 'unit_manager',
            'label' => 'Birim Amiri Onayı',
            'previous_step' => null
        ],
        2 => [
            'status' => 'pending_area_approval',
            'role' => 'alan_amiri',
            'step' => 'area_manager',
            'label' => 'Alan Amiri Onayı',
            'previous_step' => 'unit_manager'
        ],
        3 => [
            'status' => 'pending_safety_approval',
            'role' => 'isg_uzmani',
            'step' => 'safety_specialist',
            'label' => 'İSG Uzmanı Onayı',
            'previous_step' => 'area_manager'
        ],
        4 => [
            'status' => 'pending_employer_approval',
            'role' => 'isveren_vekili',
            'step' => 'employer_representative',
            'label' => 'İşveren Vekili Onayı',
            'previous_step' => 'safety_specialist'
        ]
    ];

    // Durum akışı
    private const STATUS_FLOW = [
        'pending_unit_approval' => 'pending_area_approval',
        'pending_area_approval' => 'pending_safety_approval',
        'pending_safety_approval' => 'pending_employer_approval',
        'pending_employer_approval' => 'approved',
        'pending_area_close' => 'pending_safety_close',
        'pending_safety_close' => 'pending_employer_close',
        'pending_employer_close' => 'closed',
    ];

    /**
     * İş izinleri listesi
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $company = $this->getUserCompany($user);

        if (!$company && !$user->isAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Aktif bir şirket bulunamadı.');
        }

        // Filtreleme
        $filters = $request->only(['department_id', 'status', 'work_type', 'search']);

        if ($user->isAdmin()) {
            $workPermits = $this->getFilteredWorkPermits($filters);
            $departments = CompanyDepartment::where('is_active', true)->orderBy('name')->get();
        } else {
            $workPermits = $this->getFilteredWorkPermits($filters, $company->id);
            $departments = CompanyDepartment::where('company_id', $company->id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        $stats = $this->getWorkPermitStats($company->id ?? null);

        return view('admin.work-permits.index', compact('workPermits', 'departments', 'stats', 'filters'));
    }

    /**
     * İş izni oluşturma formu
     */
    public function create()
    {
        $user = Auth::user();
        $company = $this->getUserCompany($user);

        if (!$company) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Aktif bir şirket bulunamadı.');
        }

        $departments = CompanyDepartment::with(['positions'])
            ->where('company_id', $company->id)
            ->where('is_active', true)
            ->get();

        return view('admin.work-permits.create', compact('departments', 'company'));
    }

    /**
     * İş izni kaydetme
     */
    public function store(Request $request)
    {
        Log::info('🎯 WorkPermit oluşturma başlıyor', ['user_id' => Auth::id()]);

        $validator = Validator::make($request->all(), [
            'department_id' => 'required|exists:company_departments,id',
            'position_id' => 'required|exists:department_positions,id',
            'title' => 'required|string|max:255',
            'work_type' => 'required|in:sıcak,elektrik,yuk_kaldirma,kazı,diğer',
            'work_description' => 'required|string',
            'location' => 'required|string|max:255',
            'risks' => 'required|array|min:1',
            'risks.*' => 'required|string',
            'control_measures' => 'required|array|min:1',
            'control_measures.*' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'worker_name' => 'required|string|max:255',
            'tools_equipment' => 'required|string',
            'emergency_procedures' => 'required|string',
        ], [
            'start_date.after_or_equal' => 'Başlangıç tarihi bugünden önce olamaz.',
            'end_date.after' => 'Bitiş tarihi başlangıç tarihinden sonra olmalıdır.',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation hatası', $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Lütfen tüm zorunlu alanları doğru şekilde doldurun.');
        }

        $user = Auth::user();
        $companyId = $user->company_id;

        if (!$companyId) {
            return redirect()->back()
                ->with('error', 'Kullanıcının bir şirkete atanmamış.')
                ->withInput();
        }

        return DB::transaction(function () use ($request, $validator, $user, $companyId) {
            try {
                $validated = $validator->validated();

                // Departman ve pozisyon kontrolü
                if (!$this->validateDepartmentAndPosition($validated['department_id'], $validated['position_id'], $companyId)) {
                    throw new \Exception('Seçilen departman veya pozisyon geçersiz.');
                }

                // İzin kodu oluştur
                $permitNumber = WorkPermitForm::where('company_id', $companyId)->count() + 1;
                $permitCode = $this->generatePermitCode($companyId, $permitNumber);

                // Work permit oluştur
                $workPermit = WorkPermitForm::create($this->prepareWorkPermitData($validated, $companyId, $user->id, $permitNumber, $permitCode));

                Log::info('✅ WorkPermit oluşturuldu', [
                    'id' => $workPermit->id,
                    'permit_code' => $workPermit->permit_code,
                    'status' => $workPermit->status
                ]);

                // Onay sürecini başlat
                $this->initializeOpeningApprovals($workPermit);

                // Event ve bildirim
                event(new WorkPermitStatusUpdated($workPermit, 'created', 'Yeni iş izni oluşturuldu.'));
                $this->sendNextApprovalNotification($workPermit);

                return redirect()->route('admin.work-permits.show', $workPermit)
                    ->with('success', "İş izni {$workPermit->permit_code} başarıyla oluşturuldu.");
            } catch (\Exception $e) {
                Log::error('💥 İş izni oluşturma hatası: ' . $e->getMessage());
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'İş izni oluşturulurken hata: ' . $e->getMessage());
            }
        });
    }

    /**
     * İş izni detay sayfası
     */
    public function show(WorkPermitForm $workPermit)
    {
        $this->authorize('view', $workPermit);

        // Eager loading ile performans optimizasyonu
        $workPermit->load([
            'company',
            'creator',
            'department',
            'position',
            'approvals.user' => function ($query) {
                $query->select('id', 'name', 'email', 'role');
            }
        ]);

        $approvalHistory = $workPermit->approvals()
            ->with('user:id,name,email,role')
            ->orderBy('created_at')
            ->get();

        return view('admin.work-permits.show', compact('workPermit', 'approvalHistory'));
    }

    /**
     * Onay/Reddet İşlemi - OPTİMİZE EDİLMİŞ VERSİYON
     */
    public function approveStep(Request $request, WorkPermitForm $workPermit)
    {
        $user = Auth::user();
        $action = $request->input('action'); // 'approve' veya 'reject'

        Log::info('🔔 Onay işlemi başlıyor', [
            'work_permit_id' => $workPermit->id,
            'user_id' => $user->id,
            'action' => $action,
            'current_status' => $workPermit->status
        ]);

        // Yetki kontrolü
        if (!$this->userCanApproveCurrentStep($workPermit, $user)) {
            return redirect()->back()->with('error', 'Bu adımı onaylama/reddetme yetkiniz yok.');
        }

        return DB::transaction(function () use ($workPermit, $user, $action, $request) {
            try {
                if ($action === 'reject') {
                    return $this->rejectPermit($workPermit, $user, $request->comments ?? null);
                }

                return $this->processApproval($workPermit, $user, $request);
            } catch (\Exception $e) {
                Log::error('💥 Onay işlemi hatası: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Onay işlemi sırasında hata: ' . $e->getMessage());
            }
        });
    }
    /**
     * Onay işlemini işle - YENİ OPTİMİZE METOD
     */
    private function processApproval(WorkPermitForm $workPermit, User $user, Request $request): RedirectResponse
    {
        $currentStep = $this->getCurrentStep($workPermit->status);

        Log::info('✅ Onay işleniyor', [
            'current_step' => $currentStep,
            'total_steps' => count(self::APPROVAL_STEPS)
        ]);

        // Mevcut adımı onayla
        $this->approveCurrentStep($workPermit, $user, $request);

        // Son adım mı? (4. adım - İşveren Vekili onayı)
        if ($currentStep === 4) {
            $this->finalizeAfterAllApprovals($workPermit);
            return redirect()->back()->with('success', '🎉 Tüm onaylar tamamlandı! İş izni aktif ve PDF oluşturuldu.');
        }

        // Sonraki adıma geç
        $nextStep = $currentStep + 1;
        $nextStatus = self::APPROVAL_STEPS[$nextStep]['status'];
        $workPermit->update(['status' => $nextStatus]);

        // Sonraki onaycıya bildirim gönder
        $this->sendNextApprovalNotification($workPermit);

        $nextRoleLabel = self::APPROVAL_STEPS[$nextStep]['label'];
        return redirect()->back()->with('success', "✅ Onaylandı! Şimdi {$nextRoleLabel} bekleniyor.");
    }

    /**
     * Tüm onaycılar onayladıktan sonra işlemi sonlandır - YENİ METOD
     */
    private function finalizeAfterAllApprovals(WorkPermitForm $workPermit): void
    {
        try {
            // Durumu approved yap
            $workPermit->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);

            // Final PDF oluştur
            $pdfPath = $this->generateFinalPdf($workPermit);

            // Final email gönder
            $this->sendFinalApprovalEmail($workPermit, $pdfPath);

            // Oluşturucuya bildirim gönder
            if ($workPermit->creator) {
                $workPermit->creator->notify(new WorkPermitApprovalNotification($workPermit, 'approved'));
            }

            event(new WorkPermitStatusUpdated(
                $workPermit,
                'approved',
                'İş izni tüm onaylardan geçerek başarıyla aktif hale getirildi.',
                Auth::user()
            ));

            Log::info('🎉 Tüm onaylar tamamlandı', [
                'work_permit_id' => $workPermit->id,
                'permit_code' => $workPermit->permit_code,
                'pdf_path' => $pdfPath
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Final onay işlemi hatası: ' . $e->getMessage());
            throw $e;
        }
    }


private function sendFinalApprovalEmail(WorkPermitForm $workPermit, string $pdfPath): void
{
    try {
        // WorkPermit'i tazele
        $workPermit->refresh()->load(['creator', 'company']);

        $user = $workPermit->creator;

        if (!$user || !$user->email) {
            Log::warning('📧 Email gönderilemedi: Kullanıcı veya email bulunamadı', [
                'work_permit_id' => $workPermit->id,
                'user_id' => $workPermit->created_by,
                'user' => $user ? 'exists' : 'null',
                'email' => $user?->email ?? 'null'
            ]);
            return;
        }

        Log::info('📧 Email gönderme başlıyor', [
            'work_permit_id' => $workPermit->id,
            'email' => $user->email,
            'user_name' => $user->name,
            'pdf_path' => $pdfPath,
            'pdf_exists' => Storage::exists($pdfPath)
        ]);

        // Güvenli email verisi
        $data = [
            'workPermit' => $workPermit,
            'user' => $user,
            'approvalDate' => now()->format('d.m.Y H:i')
        ];

        // Email gönder
        Mail::send('emails.work-permit-final-approved', $data,
            function ($message) use ($user, $workPermit, $pdfPath) {
                $message->to($user->email, $user->name)
                    ->subject("✅ İş İzniniz Onaylandı - {$workPermit->permit_code}");

                // PDF ekle (eğer varsa)
                if (Storage::exists($pdfPath)) {
                    $fullPath = storage_path("app/{$pdfPath}");
                    if (file_exists($fullPath)) {
                        $message->attach($fullPath, [
                            'as' => "is-izni-{$workPermit->permit_code}.pdf",
                            'mime' => 'application/pdf',
                        ]);
                        Log::info('📎 PDF eklendi', ['path' => $fullPath]);
                    }
                }
            }
        );

        Log::info('✅ Email başarıyla gönderildi', [
            'work_permit_id' => $workPermit->id,
            'email' => $user->email
        ]);

    } catch (\Exception $e) {
        Log::error('❌ Email gönderme hatası: ' . $e->getMessage(), [
            'exception' => $e->getTraceAsString()
        ]);
        // Email hatası işlemi durdurmamalı
    }
}

private function createFinalPdf(WorkPermitForm $workPermit): string
{
    try {
        Log::info('🚀 PDF oluşturma başlıyor', ['work_permit_id' => $workPermit->id]);

        // Verileri yükle
        $workPermit->load([
            'company',
            'creator',
            'department',
            'position',
            'approvals.user'
        ]);

        // Basit PDF view data
        $data = [
            'workPermit' => $workPermit,
            'currentDate' => now()->format('d.m.Y H:i')
        ];

        // PDF oluştur
        $pdf = PDF::loadView('admin.work-permits.final-pdf', $data)
            ->setPaper('a4')
            ->setOptions(['defaultFont' => 'helvetica']);

        // Dosya adı ve path - WINDOWS UYUMLU
        $cleanPermitCode = str_replace([' ', '-'], '_', $workPermit->permit_code);
        $filename = "is-izni-{$cleanPermitCode}.pdf";
        $path = "work-permits/{$filename}";

        Log::info('📁 PDF kayıt bilgileri', [
            'filename' => $filename,
            'path' => $path,
            'storage_path' => storage_path('app'),
            'full_path' => storage_path("app/{$path}")
        ]);

        // PDF'i kaydet
        Storage::put($path, $pdf->output());

        $fileSize = Storage::size($path);
        $fileExists = Storage::exists($path);

        Log::info('✅ PDF kaydedildi', [
            'path' => $path,
            'file_size' => $fileSize,
            'file_exists' => $fileExists,
            'files_in_directory' => Storage::files('work-permits')
        ]);

        // Database'e kaydet
        $workPermit->final_pdf_path = $path;
        $workPermit->save();

        Log::info('💾 Database güncellendi', ['final_pdf_path' => $path]);

        return $path;

    } catch (\Exception $e) {
        Log::error('💥 Final PDF oluşturma hatası: ' . $e->getMessage());
        throw new \Exception('PDF oluşturulamadı: ' . $e->getMessage());
    }
}
    /**
     * Final PDF görüntüleme - DÜZELTİLMİŞ
     */
    public function viewFinalPdf(WorkPermitForm $workPermit)
    {
        $this->authorize('view', $workPermit);

        if (!$workPermit->final_pdf_path || !Storage::exists($workPermit->final_pdf_path)) {
            // PDF yoksa oluştur
            try {
                $pdfPath = $this->generateFinalPdf($workPermit);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'PDF oluşturulurken hata: ' . $e->getMessage());
            }
        }

        $filePath = storage_path("app/{$workPermit->final_pdf_path}");

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'PDF dosyası bulunamadı.');
        }

        return response()->file($filePath);
    }


  /**
 * PDF indirme - PATH DÜZELTMESİ
 */
public function downloadFinalPdf(WorkPermitForm $workPermit)
{
    $this->authorize('view', $workPermit);

    try {
        Log::info('📥 PDF indirme isteği', ['work_permit_id' => $workPermit->id]);

        // Database'den taze veri al
        $workPermit->refresh();

        Log::info('🔍 Mevcut PDF durumu', [
            'final_pdf_path' => $workPermit->final_pdf_path,
            'path_exists' => $workPermit->final_pdf_path ? Storage::exists($workPermit->final_pdf_path) : false
        ]);

        // PDF yoksa oluştur
        if (!$workPermit->final_pdf_path || !Storage::exists($workPermit->final_pdf_path)) {
            Log::info('🔄 PDF bulunamadı, oluşturuluyor...');
            $this->createFinalPdf($workPermit);
            $workPermit->refresh();
        }

        // WINDOWS PATH DÜZELTMESİ
        $filePath = storage_path('app' . DIRECTORY_SEPARATOR . $workPermit->final_pdf_path);

        Log::info('🔧 Path kontrolü', [
            'database_path' => $workPermit->final_pdf_path,
            'constructed_path' => $filePath,
            'file_exists' => file_exists($filePath)
        ]);

        if (!file_exists($filePath)) {
            // Storage'dan doğrudan kontrol et
            if (!Storage::exists($workPermit->final_pdf_path)) {
                throw new \Exception("PDF Storage'da bulunamadı: {$workPermit->final_pdf_path}");
            }

            // Storage'dan dosyayı al
            $fileContent = Storage::get($workPermit->final_pdf_path);
            $filename = "is-izni-{$workPermit->permit_code}.pdf";

            Log::info('📦 Storage\'dan direkt içerik gönderiliyor', [
                'filename' => $filename,
                'content_size' => strlen($fileContent)
            ]);

            return response($fileContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Length' => strlen($fileContent)
            ]);
        }

        $filename = "is-izni-{$workPermit->permit_code}.pdf";

        Log::info('✅ PDF indirme hazır', [
            'filename' => $filename,
            'file_path' => $filePath
        ]);

        return response()->download($filePath, $filename);

    } catch (\Exception $e) {
        Log::error('❌ PDF indirme hatası: ' . $e->getMessage());
        return redirect()->back()->with('error', 'PDF indirilemedi: ' . $e->getMessage());
    }
}

    /**
     * Manuel PDF oluşturma - AJAX için
     */
    public function generateFinalPdfManual(WorkPermitForm $workPermit)
    {
        $this->authorize('view', $workPermit);

        try {
            // PDF oluştur - recursive çağrıyı önle
            $pdfPath = $this->createFinalPdf($workPermit);

            return response()->json([
                'success' => true,
                'message' => 'PDF başarıyla oluşturuldu',
                'pdf_path' => $pdfPath
            ]);
        } catch (\Exception $e) {
            Log::error('PDF oluşturma hatası: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'PDF oluşturulamadı: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Manuel email gönderme - AJAX için
     */
    public function sendFinalEmailManual(WorkPermitForm $workPermit)
    {
        $this->authorize('view', $workPermit);

        try {
            // Önce PDF'i kontrol et, yoksa oluştur
            if (!$workPermit->final_pdf_path || !Storage::exists($workPermit->final_pdf_path)) {
                $pdfPath = $this->createFinalPdf($workPermit); // createFinalPdf kullan
                $workPermit->refresh();
            } else {
                $pdfPath = $workPermit->final_pdf_path;
            }

            // Email gönder
            $this->sendFinalApprovalEmail($workPermit, $pdfPath);

            return response()->json([
                'success' => true,
                'message' => 'Email başarıyla gönderildi'
            ]);
        } catch (\Exception $e) {
            Log::error('Email gönderme hatası: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Email gönderilirken hata: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Mevcut adımı onayla - YENİ METOD
     */
    private function approveCurrentStep(WorkPermitForm $workPermit, User $user, Request $request): void
    {
        $currentStep = $this->getCurrentStep($workPermit->status);
        $currentStepConfig = self::APPROVAL_STEPS[$currentStep];

        $extraData = [];

        // Alan Amiri özel alanları
        if ($workPermit->status === 'pending_area_approval') {
            $extraData = [
                'energy_cut_off' => $request->boolean('energy_cut_off', false),
                'area_cleaned' => $request->boolean('area_cleaned', false),
                'no_conflict_with_other_works' => $request->boolean('no_conflict_with_other_works', false),
            ];
        }

        // İSG Uzmanı özel alanları
        if ($workPermit->status === 'pending_safety_approval') {
            $extraData = [
                'gas_measurement_done' => $request->boolean('gas_measurement_done', false),
                'ppe_checked' => $request->boolean('ppe_checked', false),
                'additional_procedures_verified' => $request->boolean('additional_procedures_verified', false),
            ];
        }

        // Ekstra verileri güncelle
        if (!empty($extraData)) {
            $workPermit->update($extraData);
        }

        // Onay kaydını oluştur/güncelle
        WorkPermitApproval::updateOrCreate(
            [
                'work_permit_id' => $workPermit->id,
                'step' => $currentStepConfig['step'],
            ],
            [
                'user_id' => $user->id,
                'status' => 'approved',
                'comments' => $request->comments,
                'approved_at' => now(),
                'type' => 'opening'
            ]
        );

        Log::info('✅ Adım onaylandı', [
            'work_permit_id' => $workPermit->id,
            'step' => $currentStepConfig['step'],
            'user_id' => $user->id
        ]);
    }

    /**
     * Kullanıcı bu adımı onaylayabilir mi?
     */
    private function userCanApproveCurrentStep(WorkPermitForm $workPermit, User $user): bool
    {
        $currentStep = $this->getCurrentStep($workPermit->status);

        if (!isset(self::APPROVAL_STEPS[$currentStep])) {
            return false;
        }

        $requiredRole = self::APPROVAL_STEPS[$currentStep]['role'];
        $userRole = $user->role instanceof \BackedEnum ? $user->role->value : $user->role;

        Log::info('🔍 Rol kontrolü', [
            'required_role' => $requiredRole,
            'user_role' => $userRole,
            'is_admin' => $user->isAdmin(),
            'can_approve' => $userRole === $requiredRole || $user->isAdmin()
        ]);

        return $userRole === $requiredRole || $user->isAdmin();
    }

    /**
     * Mevcut adım numarasını al - GÜNCELLENMİŞ
     */
    private function getCurrentStep(string $status): int
    {
        foreach (self::APPROVAL_STEPS as $step => $config) {
            if ($config['status'] === $status) {
                return $step;
            }
        }
        return 0;
    }

    /**
     * Reddetme - OPTİMİZE EDİLMİŞ
     */
    private function rejectPermit(WorkPermitForm $workPermit, User $user, ?string $comments): RedirectResponse
    {
        $currentStep = $this->getCurrentStep($workPermit->status);
        $currentStepConfig = self::APPROVAL_STEPS[$currentStep];

        Log::info('❌ Reddetme işlemi', [
            'work_permit_id' => $workPermit->id,
            'current_step' => $currentStep,
            'step_config' => $currentStepConfig
        ]);

        // Reddedildi durumuna güncelle
        $workPermit->update([
            'status' => 'rejected',
            'rejection_reason' => $comments ?? 'Red nedeni belirtilmedi.',
            'rejected_by' => $user->id,
            'rejected_at' => now(),
        ]);

        // Onay kaydını güncelle (reddedildi olarak)
        WorkPermitApproval::updateOrCreate(
            [
                'work_permit_id' => $workPermit->id,
                'step' => $currentStepConfig['step'],
            ],
            [
                'user_id' => $user->id,
                'status' => 'rejected',
                'comments' => $comments,
                'approved_at' => now(), // reddedilme zamanı
                'type' => 'opening'
            ]
        );

        // Önceki onayları sıfırla (sadece kendisinden sonraki onayları)
        $this->resetSubsequentApprovals($workPermit, $currentStepConfig['step']);

        // Oluşturucuya bildirim gönder
        if ($workPermit->creator) {
            $workPermit->creator->notify(new WorkPermitApprovalNotification($workPermit, 'rejected'));
        }

        event(new WorkPermitStatusUpdated(
            $workPermit,
            'rejected',
            'İş izni reddedildi: ' . ($comments ?? 'Sebep belirtilmedi.'),
            $user
        ));

        Log::info('✅ Reddetme işlemi tamamlandı', ['work_permit_id' => $workPermit->id]);

        return redirect()->back()->with('success', 'İş izni başarıyla reddedildi.');
    }

    /**
     * Sonraki onayları sıfırla - YENİ METOD
     */
    private function resetSubsequentApprovals(WorkPermitForm $workPermit, string $rejectedStep): void
    {
        $stepsToReset = [];

        // Reddedilen adımdan sonraki tüm adımları bul
        foreach (self::APPROVAL_STEPS as $stepConfig) {
            if ($this->isStepAfter($stepConfig['step'], $rejectedStep)) {
                $stepsToReset[] = $stepConfig['step'];
            }
        }

        // Sonraki onay kayıtlarını sil
        if (!empty($stepsToReset)) {
            WorkPermitApproval::where('work_permit_id', $workPermit->id)
                ->whereIn('step', $stepsToReset)
                ->delete();

            Log::info('🔄 Sonraki onaylar sıfırlandı', [
                'work_permit_id' => $workPermit->id,
                'rejected_step' => $rejectedStep,
                'reset_steps' => $stepsToReset
            ]);
        }
    }

    /**
     * Adım sıralamasını kontrol et - YENİ METOD
     */
    private function isStepAfter(string $step, string $referenceStep): bool
    {
        $stepOrder = [
            'unit_manager' => 1,
            'area_manager' => 2,
            'safety_specialist' => 3,
            'employer_representative' => 4
        ];

        return ($stepOrder[$step] ?? 99) > ($stepOrder[$referenceStep] ?? 0);
    }

    /**
     * Onay kaydı oluştur
     */
    private function recordApproval(WorkPermitForm $workPermit, User $user, string $status, ?string $comments): void
    {
        $step = match ($workPermit->status) {
            'pending_unit_approval' => 'unit_manager',
            'pending_area_approval', 'pending_area_close' => 'area_manager',
            'pending_safety_approval', 'pending_safety_close' => 'safety_specialist',
            'pending_employer_approval', 'pending_employer_close' => 'employer_representative',
            default => 'unknown',
        };

        WorkPermitApproval::updateOrCreate(
            [
                'work_permit_id' => $workPermit->id,
                'user_id' => $user->id,
                'step' => $step,
            ],
            [
                'status' => $status,
                'comments' => $comments,
                'approved_at' => $status !== 'pending' ? now() : null,
                'type' => str_contains($workPermit->status, 'close') ? 'closing' : 'opening',
            ]
        );
    }

    // YENİ PDF ROUTE METHODLARI
    /**

     * Eski PDF metodları (Mevcut yapıyı bozmamak için)
     */
    public function generatePdf(WorkPermitForm $workPermit)
    {
        $this->authorize('view', $workPermit);

        try {
            $workPermit->load(['company', 'creator', 'department', 'position', 'approvals.user']);
            $pdf = PDF::loadView('admin.work-permits.pdf', compact('workPermit'));
            $filename = "is-izni-{$workPermit->permit_code}.pdf";

            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('PDF oluşturma hatası: ' . $e->getMessage());
            return redirect()->back()->with('error', 'PDF oluşturulurken bir hata oluştu.');
        }
    }

    /**
     * Kapatma formu gösterimi
     */
    public function showClosingForm(WorkPermitForm $workPermit)
    {
        $user = Auth::user();

        if (!$this->canAccessClosingForm($workPermit, $user)) {
            return redirect()->back()->with('error', 'Sadece iş iznini açan kişi veya yetkililer kapatma formu görebilir.');
        }

        if ($workPermit->status !== 'approved') {
            return redirect()->back()->with('error', 'Sadece aktif çalışma izinleri kapatılabilir.');
        }

        return view('admin.work-permits.closing-form', compact('workPermit'));
    }

    /**
     * Kapatma sürecini başlat
     */
    public function initiateClosing(Request $request, WorkPermitForm $workPermit)
    {
        Log::info('🔒 Kapatma talebi başlıyor', [
            'work_permit_id' => $workPermit->id,
            'user_id' => Auth::id()
        ]);

        $validator = Validator::make($request->all(), [
            'work_completed' => 'required|accepted',
            'equipment_collected' => 'required|accepted',
            'emergency_equipment_closed' => 'required|accepted',
            'closing_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Lütfen tüm kapatma koşullarını kabul edin.');
        }

        $user = Auth::user();

        if (!$this->canAccessClosingForm($workPermit, $user)) {
            return redirect()->back()->with('error', 'Kapatma talebi gönderme yetkiniz bulunmuyor.');
        }

        if ($workPermit->status !== 'approved') {
            return redirect()->back()->with('error', 'Sadece aktif çalışma izinleri kapatılabilir.');
        }

        return DB::transaction(function () use ($workPermit, $validator) {
            try {
                $validated = $validator->validated();

                $workPermit->update([
                    'work_completed' => true,
                    'equipment_collected' => true,
                    'emergency_equipment_closed' => true,
                    'status' => 'pending_area_close',
                    'closing_notes' => $validated['closing_notes'] ?? null,
                    'closing_requested_at' => now(),
                ]);

                $this->initializeClosingApprovals($workPermit);

                event(new WorkPermitStatusUpdated($workPermit, 'closing_requested', 'Kapatma talebi gönderildi.', Auth::user()));
                $this->sendNextApprovalNotification($workPermit);

                Log::info('✅ Kapatma talebi başarılı', ['work_permit_id' => $workPermit->id]);

                return redirect()->route('company.dashboard')
                    ->with('success', '✅ Kapatma talebi başarıyla gönderildi! Alan Amiri onayı bekleniyor.');
            } catch (\Exception $e) {
                Log::error('💥 Kapatma talebi hatası: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Kapatma talebi gönderilirken hata: ' . $e->getMessage());
            }
        });
    }

    /**
     * İş izni silme (soft delete)
     */
    public function destroy(WorkPermitForm $workPermit)
    {
        $user = Auth::user();

        // Yetki kontrolü
        if (!$user->isAdmin() && $workPermit->created_by !== $user->id) {
            return redirect()->back()->with('error', 'Bu iş iznini silme yetkiniz bulunmuyor.');
        }

        // Onay sürecindeki iş izinleri silinemez
        if (!in_array($workPermit->status, ['rejected', 'closed'])) {
            return redirect()->back()->with('error', 'Onay sürecindeki iş izinleri silinemez.');
        }

        try {
            $workPermit->delete();

            Log::info('🗑️ İş izni silindi', [
                'work_permit_id' => $workPermit->id,
                'user_id' => $user->id
            ]);

            return redirect()->route('admin.work-permits.index')
                ->with('success', 'İş izni başarıyla silindi.');
        } catch (\Exception $e) {
            Log::error('💥 İş izni silme hatası: ' . $e->getMessage());
            return redirect()->back()->with('error', 'İş izni silinirken bir hata oluştu.');
        }
    }

    // ==================== PRIVATE METHODS ====================

    /**
     * Kullanıcı rolünü string olarak al
     */
    private function getUserRoleString(User $user): string
    {
        return $user->role instanceof Role ? $user->role->value : $user->role;
    }

    /**
     * Filtrelenmiş iş izinlerini getir
     */
    private function getFilteredWorkPermits(array $filters, ?int $companyId = null)
    {
        $query = WorkPermitForm::with(['company:id,name', 'creator:id,name', 'department:id,name']);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        // Filtreler
        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['work_type'])) {
            $query->where('work_type', $filters['work_type']);
        }

        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('worker_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('permit_code', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('location', 'LIKE', "%{$searchTerm}%");
            });
        }

        return $query->latest()->paginate(10);
    }

    /**
     * İş izni istatistiklerini getir
     */
    private function getWorkPermitStats(?int $companyId = null): array
    {
        $query = $companyId ?
            WorkPermitForm::where('company_id', $companyId) :
            WorkPermitForm::query();

        $total = $query->count();
        $approved = (clone $query)->where('status', 'approved')->count();
        $pending = (clone $query)->whereIn('status', [
            'pending_unit_approval',
            'pending_area_approval',
            'pending_safety_approval',
            'pending_employer_approval'
        ])->count();
        $overdue = (clone $query)->where('status', 'approved')
            ->where('end_date', '<', now())
            ->count();
        $closed = (clone $query)->where('status', 'closed')->count();
        $rejected = (clone $query)->where('status', 'rejected')->count();

        return [
            'total' => $total,
            'approved' => $approved,
            'pending' => $pending,
            'overdue' => $overdue,
            'closed' => $closed,
            'rejected' => $rejected,
        ];
    }

    /**
     * Departman ve pozisyon validasyonu
     */
    private function validateDepartmentAndPosition(int $departmentId, int $positionId, int $companyId): bool
    {
        $department = CompanyDepartment::where('id', $departmentId)
            ->where('company_id', $companyId)
            ->exists();

        if (!$department) {
            Log::error('❌ Departman bulunamadı', compact('departmentId', 'companyId'));
            return false;
        }

        $position = DepartmentPosition::where('id', $positionId)
            ->where('department_id', $departmentId)
            ->exists();

        if (!$position) {
            Log::error('❌ Pozisyon bulunamadı', compact('positionId', 'departmentId'));
            return false;
        }

        return true;
    }

    /**
     * İş izni verilerini hazırla
     */
    private function prepareWorkPermitData(array $validated, int $companyId, int $userId, int $permitNumber, string $permitCode): array
    {
        $position = DepartmentPosition::find($validated['position_id']);

        return [
            'company_id' => $companyId,
            'department_id' => $validated['department_id'],
            'position_id' => $position->id,
            'created_by' => $userId,
            'title' => $validated['title'],
            'work_type' => $validated['work_type'],
            'work_description' => $validated['work_description'],
            'location' => $validated['location'],
            'worker_name' => $validated['worker_name'],
            'worker_position' => $position->name,
            'risks' => $validated['risks'],
            'control_measures' => $validated['control_measures'],
            'tools_equipment' => $validated['tools_equipment'],
            'emergency_procedures' => $validated['emergency_procedures'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => 'pending_unit_approval',
            'permit_number' => $permitNumber,
            'permit_code' => $permitCode,
        ];
    }

    /**
     * Açılış onay adımlarını oluştur - DÜZELTİLMİŞ
     */
    private function initializeOpeningApprovals(WorkPermitForm $workPermit): void
    {
        $department = CompanyDepartment::find($workPermit->department_id);

        // Sadece bir kere onay kaydı oluştur
        $steps = ['unit_manager', 'area_manager', 'safety_specialist', 'employer_representative'];

        foreach ($steps as $step) {
            // Önce bu step için kayıt var mı kontrol et
            $existingApproval = WorkPermitApproval::where('work_permit_id', $workPermit->id)
                ->where('step', $step)
                ->first();

            if (!$existingApproval) {
                $approver = $department?->getApproverForStep($step) ?? $this->getFallbackApprover($step);

                WorkPermitApproval::create([
                    'work_permit_id' => $workPermit->id,
                    'user_id' => $approver->id,
                    'type' => 'opening',
                    'step' => $step,
                    'status' => 'pending',
                ]);

                Log::info('✅ Onay adımı oluşturuldu', [
                    'work_permit_id' => $workPermit->id,
                    'step' => $step,
                    'approver' => $approver->name
                ]);
            }
        }

        Log::info('🔄 Açılış onay süreci başlatıldı', [
            'work_permit_id' => $workPermit->id,
            'department' => $department?->name
        ]);
    }

    /**
     * Kapanış onay adımları oluştur
     */
    private function initializeClosingApprovals(WorkPermitForm $workPermit): void
    {
        $department = CompanyDepartment::find($workPermit->department_id);

        $steps = ['area_manager', 'safety_specialist', 'employer_representative'];

        foreach ($steps as $step) {
            $approver = $department?->getApproverForStep($step) ?? $this->getFallbackApprover($step);

            WorkPermitApproval::create([
                'work_permit_id' => $workPermit->id,
                'user_id' => $approver->id,
                'type' => 'closing',
                'step' => $step,
                'status' => 'pending',
            ]);
        }

        Log::info('🔄 Kapanış onay süreci başlatıldı', ['work_permit_id' => $workPermit->id]);
    }

    /**
     * Fallback onaycı bul
     */
    private function getFallbackApprover(string $step): User
    {
        $roleMap = [
            'unit_manager' => 'birim_amiri',
            'area_manager' => 'alan_amiri',
            'safety_specialist' => 'isg_uzmani',
            'employer_representative' => 'isveren_vekili',
        ];

        $role = $roleMap[$step] ?? 'admin';

        $approver = User::where('role', $role)->first();

        return $approver ?? User::where('role', 'admin')->first() ?? Auth::user();
    }

    /**
     * İzin kodu oluştur
     */
    private function generatePermitCode(int $companyId, int $permitNumber): string
    {
        $company = Company::find($companyId);
        // Boşlukları kaldır ve özel karakterleri temizle
        $companyCode = $company ? preg_replace('/[^a-zA-Z0-9]/', '', substr($company->name, 0, 3)) : 'COM';
        $date = now()->format('Ymd');

        return strtoupper($companyCode) . '-' . $date . '-' . str_pad($permitNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Kapatma formu erişim kontrolü
     */
    private function canAccessClosingForm(WorkPermitForm $workPermit, User $user): bool
    {
        $userRole = $this->getUserRoleString($user);

        return $workPermit->created_by === $user->id ||
            $user->isAdmin() ||
            $userRole === 'isveren_vekili' ||
            $userRole === 'alan_amiri';
    }

    /**
     * Sonraki onaycıya bildirim gönder
     */
    private function sendNextApprovalNotification(WorkPermitForm $workPermit): void
    {
        try {
            $nextApproval = $workPermit->approvals()
                ->where('status', 'pending')
                ->orderBy('id')
                ->first();

            if ($nextApproval?->user) {
                $nextApproval->user->notify(new WorkPermitApprovalNotification($workPermit, 'pending_approval'));
                event(new NewApprovalAssigned($workPermit, $nextApproval->user, Auth::user()));

                Log::info('📧 Sonraki onaycıya bildirim gönderildi', [
                    'work_permit_id' => $workPermit->id,
                    'user_id' => $nextApproval->user_id,
                    'step' => $nextApproval->step
                ]);
            }
        } catch (\Exception $e) {
            Log::error('❌ Bildirim gönderme hatası: ' . $e->getMessage());
        }
    }

    /**
     * Kullanıcının şirketini al
     */
    private function getUserCompany(User $user): ?Company
    {
        if (session()->has('current_company_id')) {
            return Company::find(session('current_company_id'));
        }

        return $user->company;
    }
}
