<?php

namespace App\Listeners;

use App\Events\WorkPermitApprovalRequested;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendApprovalRequestEmail
{
    public function handle(WorkPermitApprovalRequested $event): void
    {
        try {
            // Onay adımını bul
            $currentStep = $event->workPermit->approvalSteps()
                ->where('phase', $event->phase)
                ->where('step', $event->step)
                ->where('status', 'pending')
                ->first();

            if (!$currentStep || !$currentStep->assignedUser) {
                Log::warning("Onaylayan bulunamadı - WorkPermit: {$event->workPermit->id}");
                return;
            }

            // Mail gönder - şimdilik log olarak kaydedelim
            Log::info("📧 Onay talebi maili gönderiliyor", [
                'to' => $currentStep->assignedUser->email,
                'workPermit_id' => $event->workPermit->id,
                'phase' => $event->phase,
                'step' => $event->step,
                'recipient' => $currentStep->assignedUser->name
            ]);

            // TODO: Mail sınıfı oluştur ve gönder
            // Mail::to($currentStep->assignedUser->email)
            //     ->send(new \App\Mail\WorkPermitApprovalRequestMail($event->workPermit, $event->phase, $event->step));
        } catch (\Exception $e) {
            Log::error("Mail gönderimi hatası: {$e->getMessage()}");
        }
    }
}
