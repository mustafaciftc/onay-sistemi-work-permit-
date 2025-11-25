<?php

namespace App\Listeners;

use App\Events\WorkPermitCompleted;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCompletionEmail
{
    public function handle(WorkPermitCompleted $event): void
    {
        try {
            Log::info("📧 İş izni tamamlanma maili gönderiliyor", [
                'workPermit_id' => $event->workPermit->id,
                'creator' => $event->workPermit->creator->email ?? 'N/A'
            ]);

            // TODO: Mail sınıfı oluştur ve gönder
            // Mail::to($event->workPermit->creator->email)
            //     ->send(new \App\Mail\WorkPermitCompletedMail($event->workPermit));
        } catch (\Exception $e) {
            Log::error("Tamamlanma maili gönderimi hatası: {$e->getMessage()}");
        }
    }
}
