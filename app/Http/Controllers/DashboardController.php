<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WorkPermitForm;
use App\Models\WorkPermitApproval;
use App\Models\CompanyDepartment;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Şirket Dashboard - Genel Bakış
     */
    public function company_index()
    {
        $user = Auth::user();
        $company = $user->company;

        Log::info('🏢 Company Dashboard Çağrıldı', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role
        ]);

        if (!$company) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Aktif bir şirket bulunamadı.');
        }

        // Kullanıcının rolüne göre dashboard'a yönlendir
        return $this->redirectToRoleDashboard($user);
    }

    /**
     * Birim Amiri Dashboard
     */
    public function birimAmiriDashboard()
    {
        $user = Auth::user();

        Log::info('👨‍💼 Birim Amiri Dashboard Çağrıldı', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role
        ]);

        // Rol kontrolü
        if (!$user->isBirimAmiri() && !$user->isAdmin()) {
            Log::warning('❌ Birim Amiri dashboard erişim hatası', [
                'user_id' => $user->id,
                'user_role' => $user->role
            ]);
            return redirect()->route('company.dashboard')
                ->with('error', 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        $company = $user->company;
        $stats = $this->getBirimAmiriStats($user);
        $pendingApprovals = $this->getPendingApprovalsForBirimAmiri($user);

        return view('company.birim-amiri', compact('stats', 'pendingApprovals', 'user'));
    }

    /**
     * Alan Amiri Dashboard
     */
    public function alanAmiriDashboard()
    {
        $user = Auth::user();

        Log::info('👷 Alan Amiri Dashboard Çağrıldı', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role
        ]);

        // Rol kontrolü
        if (!$user->isAlanAmiri() && !$user->isAdmin()) {
            Log::warning('❌ Alan Amiri dashboard erişim hatası', [
                'user_id' => $user->id,
                'user_role' => $user->role
            ]);
            return redirect()->route('company.dashboard')
                ->with('error', 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        $company = $user->company;
        $stats = $this->getAlanAmiriStats($user);
        $pendingApprovals = $this->getPendingApprovalsForAlanAmiri($user);

        return view('company.alan-amiri', compact('stats', 'pendingApprovals', 'user'));
    }

    /**
     * İSG Uzmanı Dashboard
     */
    public function isgUzmaniDashboard()
    {
        $user = Auth::user();

        Log::info('🛡️ İSG Uzmanı Dashboard Çağrıldı', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role
        ]);

        // Rol kontrolü
        if (!$user->isIsgUzmani() && !$user->isAdmin()) {
            Log::warning('❌ İSG Uzmanı dashboard erişim hatası', [
                'user_id' => $user->id,
                'user_role' => $user->role
            ]);
            return redirect()->route('company.dashboard')
                ->with('error', 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        $company = $user->company;
        $stats = $this->getIsgUzmaniStats($user);
        $pendingApprovals = $this->getPendingApprovalsForIsgUzmani($user);

        return view('company.isg-uzmani', compact('stats', 'pendingApprovals', 'user'));
    }

    /**
     * İşveren Vekili Dashboard
     */
    public function isverenVekiliDashboard()
    {
        $user = Auth::user();

        Log::info('💼 İşveren Vekili Dashboard Çağrıldı', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role
        ]);

        // Rol kontrolü
        if (!$user->isIsverenVekili() && !$user->isAdmin()) {
            Log::warning('❌ İşveren Vekili dashboard erişim hatası', [
                'user_id' => $user->id,
                'user_role' => $user->role
            ]);
            return redirect()->route('company.dashboard')
                ->with('error', 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        $company = $user->company;
        $stats = $this->getIsverenVekiliStats($user);
        $pendingApprovals = $this->getPendingApprovalsForIsverenVekili($user);

        return view('company.isveren-vekili', compact('stats', 'pendingApprovals', 'user'));
    }

    /**
     * Çalışan Dashboard
     */
    public function calisanDashboard()
    {
        $user = Auth::user();

        Log::info('👤 Çalışan Dashboard Çağrıldı', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role
        ]);

        $company = $user->company;
        $stats = $this->getCalisanStats($user);
        $myWorkPermits = $this->getMyWorkPermits($user);

        return view('company.calisan', compact('stats', 'myWorkPermits', 'user'));
    }

    // ==================== PRIVATE METHODS ====================

    /**
     * Kullanıcıyı rolüne göre dashboard'a yönlendir
     */
    private function redirectToRoleDashboard($user)
    {
        $userRole = $user->role instanceof \App\Enums\Role ? $user->role->value : $user->role;

        Log::info('🔄 Rol bazlı yönlendirme', ['user_role' => $userRole]);

        return match ($userRole) {
            'birim_amiri' => redirect()->route('company.birim-amiri'),
            'alan_amiri' => redirect()->route('company.alan-amiri'),
            'isg_uzmani' => redirect()->route('company.isg-uzmani'),
            'isveren_vekili' => redirect()->route('company.isveren-vekili'),
            'calisan' => redirect()->route('company.calisan'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('company.calisan'),
        };
    }

    /**
     * Birim Amiri İstatistikleri
     */
    private function getBirimAmiriStats($user)
    {
        $pendingCount = WorkPermitForm::where('status', 'pending_unit_approval')
            ->where('company_id', $user->company_id)
            ->count();

        $approvedCount = WorkPermitForm::where('status', 'approved')
            ->where('company_id', $user->company_id)
            ->count();

        $totalCount = WorkPermitForm::where('company_id', $user->company_id)
            ->count();

        return [
            'pending_approvals' => $pendingCount,
            'approved_permits' => $approvedCount,
            'total_permits' => $totalCount,
        ];
    }

    /**
     * Alan Amiri İstatistikleri
     */
    private function getAlanAmiriStats($user)
    {
        $pendingCount = WorkPermitForm::where('status', 'pending_area_approval')
            ->where('company_id', $user->company_id)
            ->count();

        $closingCount = WorkPermitForm::where('status', 'pending_area_close')
            ->where('company_id', $user->company_id)
            ->count();

        return [
            'pending_approvals' => $pendingCount,
            'pending_closing' => $closingCount,
        ];
    }

    /**
     * İSG Uzmanı İstatistikleri
     */
    private function getIsgUzmaniStats($user)
    {
        $pendingCount = WorkPermitForm::where('status', 'pending_safety_approval')
            ->where('company_id', $user->company_id)
            ->count();

        $closingCount = WorkPermitForm::where('status', 'pending_safety_close')
            ->where('company_id', $user->company_id)
            ->count();

        return [
            'pending_approvals' => $pendingCount,
            'pending_closing' => $closingCount,
        ];
    }

    /**
     * İşveren Vekili İstatistikleri
     */
    private function getIsverenVekiliStats($user)
    {
        $pendingCount = WorkPermitForm::where('status', 'pending_employer_approval')
            ->where('company_id', $user->company_id)
            ->count();

        $closingCount = WorkPermitForm::where('status', 'pending_employer_close')
            ->where('company_id', $user->company_id)
            ->count();

        $totalApproved = WorkPermitForm::where('status', 'approved')
            ->where('company_id', $user->company_id)
            ->count();

        return [
            'pending_approvals' => $pendingCount,
            'pending_closing' => $closingCount,
            'total_approved' => $totalApproved,
        ];
    }

    /**
     * Çalışan İstatistikleri
     */
    private function getCalisanStats($user)
    {
        $myPermits = WorkPermitForm::where('created_by', $user->id)
            ->count();

        $pendingPermits = WorkPermitForm::where('created_by', $user->id)
            ->whereIn('status', ['pending_unit_approval', 'pending_area_approval', 'pending_safety_approval', 'pending_employer_approval'])
            ->count();

        $approvedPermits = WorkPermitForm::where('created_by', $user->id)
            ->where('status', 'approved')
            ->count();

        return [
            'my_permits' => $myPermits,
            'pending_permits' => $pendingPermits,
            'approved_permits' => $approvedPermits,
        ];
    }

    /**
     * Birim Amiri için bekleyen onaylar
     */
    private function getPendingApprovalsForBirimAmiri($user)
    {
        return WorkPermitForm::with(['department', 'creator'])
            ->where('status', 'pending_unit_approval')
            ->where('company_id', $user->company_id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Alan Amiri için bekleyen onaylar
     */
    private function getPendingApprovalsForAlanAmiri($user)
    {
        return WorkPermitForm::with(['department', 'creator'])
            ->whereIn('status', ['pending_area_approval', 'pending_area_close'])
            ->where('company_id', $user->company_id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * İSG Uzmanı için bekleyen onaylar
     */
    private function getPendingApprovalsForIsgUzmani($user)
    {
        return WorkPermitForm::with(['department', 'creator'])
            ->whereIn('status', ['pending_safety_approval', 'pending_safety_close'])
            ->where('company_id', $user->company_id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }
    /**
     * İşveren Vekili için bekleyen onaylar
     */
    private function getPendingApprovalsForIsverenVekili($user)
    {
        return WorkPermitForm::with(['department', 'creator'])
            ->whereIn('status', ['pending_employer_approval', 'pending_employer_close'])
            ->where('company_id', $user->company_id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }
    /**
     * Çalışanın iş izinleri
     */
    private function getMyWorkPermits($user)
    {
        return WorkPermitForm::with(['department', 'position'])
            ->where('created_by', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function quickApprove(Request $request, WorkPermitForm $workPermit)
    {
        $action = $request->input('quick_action'); // 'approve' veya 'reject'
        $comments = $request->input('quick_comments');

        if (!in_array($action, ['approve', 'reject'])) {
            return redirect()->back()->with('error', 'Geçersiz işlem.');
        }

        if (!$this->userCanQuickApprove($workPermit, Auth::user())) {
            return redirect()->back()->with('error', 'Bu işlem için yetkiniz yok.');
        }

        app(WorkPermitController::class)->approveStep($request, $workPermit);

        $message = $action === 'approve' ? 'onaylandı' : 'reddedildi';
        return redirect()->back()->with('success', "İş izni {$message}.");
    }

    private function userCanQuickApprove(WorkPermitForm $workPermit, $user): bool
    {
        $roleMap = [
            'pending_unit_approval'     => 'birim_amiri',
            'pending_area_approval'     => 'alan_amiri',
            'pending_safety_approval'   => 'isg_uzmani',
            'pending_employer_approval' => 'isveren_vekili',
            'pending_area_close'        => 'alan_amiri',
            'pending_safety_close'      => 'isg_uzmani',
            'pending_employer_close'    => 'isveren_vekili',
        ];

        $required = $roleMap[$workPermit->status] ?? null;
        if (!$required) return false;

        $userRole = $user->role instanceof \BackedEnum ? $user->role->value : $user->role;

        return $userRole === $required || $user->isAdmin();
    }
}
