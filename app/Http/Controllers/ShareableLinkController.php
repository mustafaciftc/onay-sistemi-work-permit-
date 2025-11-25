<?php

namespace App\Http\Controllers;

use App\Models\ShareableLink;
use App\Models\WorkPermitForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ShareableLinkController extends Controller
{
    public function index(WorkPermitForm $workPermit)
    {
        $this->authorize('view', $workPermit);

        $links = $workPermit->shareableLinks()->latest()->get();

        return view('admin.shareable-links.index', compact('workPermit', 'links'));
    }

    public function create(WorkPermitForm $workPermit)
    {
        $this->authorize('update', $workPermit);

        return view('admin.shareable-links.create', compact('workPermit'));
    }

    public function store(Request $request, WorkPermitForm $workPermit)
    {
        $this->authorize('update', $workPermit);

        $validated = $request->validate([
            'password' => 'nullable|string|min:3',
            'expires_at' => 'nullable|date|after:now',
            'max_views' => 'nullable|integer|min:1',
            'permissions' => 'nullable|array',
            'permissions.view' => 'boolean',
            'permissions.download_pdf' => 'boolean',
            'permissions.view_attachments' => 'boolean',
        ]);

        $link = ShareableLink::create([
            'work_permit_id' => $workPermit->id,
            'password' => $validated['password'] ? bcrypt($validated['password']) : null,
            'expires_at' => $validated['expires_at'],
            'max_views' => $validated['max_views'],
            'permissions' => $validated['permissions'] ?? (new ShareableLink())->getDefaultPermissions(),
        ]);

        return redirect()->route('admin.work-permits.shareable-links.index', $workPermit)
            ->with('success', 'Paylaşım linki başarıyla oluşturuldu.')
            ->with('new_link_url', $link->getShareUrl());
    }

    public function show($token, Request $request)
    {
        $link = ShareableLink::where('token', $token)->firstOrFail();

        if (!$link->canBeAccessed()) {
            abort(404, 'Bu link artık geçerli değil.');
        }

        // Şifre kontrolü
        if ($link->password && !$request->session()->get('shareable_link_authenticated_' . $link->id)) {
            if ($request->isMethod('post')) {
                if (password_verify($request->password, $link->password)) {
                    $request->session()->put('shareable_link_authenticated_' . $link->id, true);
                } else {
                    return back()->with('error', 'Geçersiz şifre.');
                }
            } else {
                return view('admin.shareable-links.password', compact('link'));
            }
        }

        // Görüntüleme sayısını artır
        if (!$request->session()->get('shareable_link_viewed_' . $link->id)) {
            $link->incrementViewCount();
            $request->session()->put('shareable_link_viewed_' . $link->id, true);
        }

        $workPermit = $link->workPermit;
        $isSharedView = true;

        return view('admin.work-permits.show', compact('workPermit', 'isSharedView', 'link'));
    }

    public function downloadPdf($token)
    {
        $link = ShareableLink::where('token', $token)->firstOrFail();

        if (!$link->canBeAccessed() || !$link->hasPermission('download_pdf')) {
            abort(403, 'PDF indirme izniniz bulunmuyor.');
        }

        $workPermit = $link->workPermit;
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.work-permits.pdf', compact('workPermit'));

        return $pdf->download("is-izni-{$workPermit->id}.pdf");
    }

    public function destroy(ShareableLink $shareableLink)
    {
        $this->authorize('update', $shareableLink->workPermit);

        $shareableLink->delete();

        return redirect()->back()->with('success', 'Paylaşım linki başarıyla silindi.');
    }

    public function toggle(ShareableLink $shareableLink)
    {
        $this->authorize('update', $shareableLink->workPermit);

        $shareableLink->update([
            'is_active' => !$shareableLink->is_active
        ]);

        $status = $shareableLink->is_active ? 'aktif' : 'pasif';

        return redirect()->back()->with('success', "Paylaşım linki {$status} duruma getirildi.");
    }
}
