<?php

namespace App\Http\Controllers;

use App\Models\WorkPermitForm;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use App\Models\WorkPermitApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $company = $user->companies->first();

        if (!$company) {
            return redirect()->route('admin.dashboard')->with('error', 'Şirket bulunamadı.');
        }

        $stats = $this->getCompanyStats($company);
        $charts = $this->getChartData($company);

        return view('reports.index', compact('stats', 'charts', 'company'));
    }

    public function workPermits(Request $request)
    {
        $user = Auth::user();
        $company = $user->companies->first();

        $query = WorkPermitForm::where('company_id', $company->id);

        // Filtreleme
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('work_type')) {
            $query->where('work_type', $request->work_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $workPermits = $query->with(['creator', 'areaManager', 'safetySpecialist'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $statuses = WorkPermitForm::select('status')
            ->where('company_id', $company->id)
            ->distinct()
            ->pluck('status');

        $workTypes = WorkPermitForm::select('work_type')
            ->where('company_id', $company->id)
            ->distinct()
            ->pluck('work_type');

        return view('admin.work-permits', compact('workPermits', 'statuses', 'workTypes'));
    }

    public function approvals(Request $request)
    {
        $user = Auth::user();
        $company = $user->companies->first();

        $query = DB::table('work_permit_approvals')
            ->join('work_permit_forms', 'work_permit_approvals.work_permit_id', '=', 'work_permit_forms.id')
            ->join('users', 'work_permit_approvals.user_id', '=', 'users.id')
            ->where('work_permit_forms.company_id', $company->id)
            ->select(
                'work_permit_approvals.*',
                'work_permit_forms.title',
                'work_permit_forms.status as permit_status',
                'users.name as user_name'
            );

        if ($request->filled('status')) {
            $query->where('work_permit_approvals.status', $request->status);
        }

        if ($request->filled('step')) {
            $query->where('work_permit_approvals.step', $request->step);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('work_permit_approvals.created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('work_permit_approvals.created_at', '<=', $request->date_to);
        }

        $approvals = $query->orderBy('work_permit_approvals.created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('reports.approvals', compact('approvals'));
    }

    public function exportWorkPermits(Request $request)
    {
        $user = Auth::user();
        $company = $user->companies->first();

        $query = WorkPermitForm::where('company_id', $company->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $workPermits = $query->with(['creator', 'company'])->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="is_izinleri_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($workPermits) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID',
                'Başlık',
                'İş Türü',
                'Durum',
                'Oluşturan',
                'Başlangıç Tarihi',
                'Bitiş Tarihi',
                'Lokasyon',
                'Oluşturulma Tarihi'
            ]);

            foreach ($workPermits as $permit) {
                fputcsv($file, [
                    $permit->id,
                    $permit->title,
                    $permit->work_type,
                    $permit->status_text,
                    $permit->creator->name,
                    $permit->start_date->format('d.m.Y'),
                    $permit->end_date->format('d.m.Y'),
                    $permit->location,
                    $permit->created_at->format('d.m.Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function dashboardStats()
    {
        $user = Auth::user();
        $company = $user->companies->first();

        if (!$company) {
            return response()->json([]);
        }

        // Temel sayılar
        $totalPermits = WorkPermitForm::where('company_id', $company->id)->count();
        $activePermits = WorkPermitForm::where('company_id', $company->id)
            ->where('status', 'approved')
            ->whereBetween('start_date', [now()->subDays(30), now()->addDays(30)])
            ->count();

        $pendingApprovals = WorkPermitForm::where('company_id', $company->id)
            ->whereIn('status', [
                'pending_unit_approval',
                'pending_area_approval',
                'pending_safety_approval'
            ])
            ->count();

        $completedThisMonth = WorkPermitForm::where('company_id', $company->id)
            ->where('status', 'completed')
            ->whereMonth('closed_at', now()->month)
            ->count();

        // Kullanıcıya özel bekleyen onaylar
        $myPendingApprovals = WorkPermitApproval::whereHas('workPermit', function ($q) use ($company) {
            $q->where('company_id', $company->id);
        })
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        // İş türü dağılımı
        $workTypeDistribution = WorkPermitForm::where('company_id', $company->id)
            ->select('work_type', DB::raw('count(*) as count'))
            ->groupBy('work_type')
            ->get()
            ->pluck('count', 'work_type');

        // Son 7 günlük trend
        $weeklyTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = WorkPermitForm::where('company_id', $company->id)
                ->whereDate('created_at', $date)
                ->count();

            $weeklyTrend[] = [
                'date' => $date->format('d.m'),
                'count' => $count
            ];
        }

        // Ortalama onay süresi (gün)
        $avgApprovalTime = WorkPermitForm::where('company_id', $company->id)
            ->where('status', 'approved')
            ->whereNotNull('area_manager_approved_at')
            ->avg(DB::raw('DATEDIFF(area_manager_approved_at, created_at)'));

        // Departman performansı
        $departmentStats = Department::where('company_id', $company->id)
            ->withCount([
                'workPermits as total_permits',
                'workPermits as completed_permits' => function ($q) {
                    $q->where('status', 'completed');
                },
                'workPermits as pending_permits' => function ($q) {
                    $q->whereIn('status', [
                        'pending_unit_approval',
                        'pending_area_approval',
                        'pending_safety_approval'
                    ]);
                }
            ])
            ->get();

        return response()->json([
            'summary' => [
                'total_permits' => $totalPermits,
                'active_permits' => $activePermits,
                'pending_approvals' => $pendingApprovals,
                'completed_this_month' => $completedThisMonth,
                'my_pending_approvals' => $myPendingApprovals,
            ],
            'work_type_distribution' => $workTypeDistribution,
            'weekly_trend' => $weeklyTrend,
            'avg_approval_time_days' => round($avgApprovalTime ?? 0, 1),
            'department_stats' => $departmentStats,
            'alerts' => [
                'overdue_permits' => WorkPermitForm::where('company_id', $company->id)
                    ->where('status', 'approved')
                    ->where('end_date', '<', now())
                    ->where('status', '!=', 'completed')
                    ->count(),
                'expiring_soon' => WorkPermitForm::where('company_id', $company->id)
                    ->where('status', 'approved')
                    ->whereBetween('end_date', [now(), now()->addDays(3)])
                    ->count(),
            ]
        ]);
    }

    private function getCompanyStats(Company $company)
    {
        $total = WorkPermitForm::where('company_id', $company->id)->count();

        $statusCounts = WorkPermitForm::where('company_id', $company->id)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $workTypeCounts = WorkPermitForm::where('company_id', $company->id)
            ->select('work_type', DB::raw('count(*) as count'))
            ->groupBy('work_type')
            ->pluck('count', 'work_type');

        $monthlyCounts = WorkPermitForm::where('company_id', $company->id)
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as count'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        $approvalStats = DB::table('work_permit_approvals')
            ->join('work_permit_forms', 'work_permit_approvals.work_permit_id', '=', 'work_permit_forms.id')
            ->where('work_permit_forms.company_id', $company->id)
            ->select('work_permit_approvals.step', 'work_permit_approvals.status', DB::raw('count(*) as count'))
            ->groupBy('work_permit_approvals.step', 'work_permit_approvals.status')
            ->get();

        return [
            'total' => $total,
            'status_counts' => $statusCounts,
            'work_type_counts' => $workTypeCounts,
            'monthly_counts' => $monthlyCounts,
            'approval_stats' => $approvalStats,
            'pending_approvals' => $statusCounts->filter(fn($v, $k) => in_array($k, [
                'pending_unit_approval',
                'pending_area_approval',
                'pending_safety_approval'
            ]))->sum(),
            'completion_rate' => $total > 0 ? round(($statusCounts['completed'] ?? 0) / $total * 100, 2) : 0,
        ];
    }

    private function getChartData(Company $company)
    {
        // Son 6 aylık veri
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push(now()->subMonths($i)->format('Y-m'));
        }

        $monthlyData = WorkPermitForm::where('company_id', $company->id)
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as total'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $chartData = $months->map(function ($month) use ($monthlyData) {
            $data = $monthlyData->get($month);
            return [
                'month' => $month,
                'total' => $data ? $data->total : 0,
                'completed' => $data ? $data->completed : 0,
            ];
        });

        return [
            'monthly' => $chartData,
            'work_types' => $this->getCompanyStats($company)['work_type_counts'],
            'statuses' => $this->getCompanyStats($company)['status_counts'],
        ];
    }
}
