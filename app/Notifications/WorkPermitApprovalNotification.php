<?php

namespace App\Notifications;

use App\Models\WorkPermitForm;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkPermitApprovalNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    public function __construct(public WorkPermitForm $workPermit, public string $actionType) {}

    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast'];
    }


    public function toMail($notifiable)
    {
        $subject = $this->getSubject();
        $greeting = $this->getGreeting();
        $actionText = $this->getActionText();
        $actionUrl = $this->getActionUrl();

        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($this->getMessage())
            ->action($actionText, $actionUrl)
            ->line('Bu işlem için zamanında yanıt vermeniz önemlidir.')
            ->salutation('Saygılarımızla, İş İzni Sistemi');
    }

    public function toArray($notifiable)
    {
        return [
            'work_permit_id' => $this->workPermit->id,
            'title' => $this->workPermit->title,
            'action_type' => $this->actionType,
            'message' => $this->getMessage(),
            'url' => $this->getActionUrl(),
        ];
    }

    private function getSubject(): string
    {
        return match ($this->actionType) {
            'pending_approval' => 'Yeni İş İzni Onay Bekliyor - ' . $this->workPermit->title,
            'approved' => 'İş İzni Onaylandı - ' . $this->workPermit->title,
            'rejected' => 'İş İzni Reddedildi - ' . $this->workPermit->title,
            'closing_request' => 'İş İzni Kapatma Talebi - ' . $this->workPermit->title,
            default => 'İş İzni Güncellemesi - ' . $this->workPermit->title,
        };
    }

    private function getGreeting(): string
    {
        return match ($this->actionType) {
            'pending_approval' => 'Yeni Onay Talebi!',
            'approved' => 'İş İzni Onaylandı!',
            'rejected' => 'İş İzni Reddedildi!',
            'closing_request' => 'Kapatma Talebi!',
            default => 'İş İzni Güncellemesi!',
        };
    }

    private function getMessage(): string
    {
        return match ($this->actionType) {
            'pending_approval' => "{$this->workPermit->title} başlıklı iş izni onayınızı bekliyor. Lütfen iş iznini inceleyip onaylayın veya reddedin.",
            'approved' => "{$this->workPermit->title} başlıklı iş izni onaylandı. Çalışma başlayabilir.",
            'rejected' => "{$this->workPermit->title} başlıklı iş izni reddedildi. Lütfen detayları inceleyin.",
            'closing_request' => "{$this->workPermit->title} başlıklı iş izni için kapatma talebi gönderildi. Lütfen kapatma işlemlerini tamamlayın.",
            default => "{$this->workPermit->title} başlıklı iş izninde güncelleme var.",
        };
    }

    private function getActionText(): string
    {
        return match ($this->actionType) {
            'pending_approval' => 'İş İznini İncele',
            'approved' => 'İş İznini Görüntüle',
            'rejected' => 'İş İznini Görüntüle',
            'closing_request' => 'Kapatma İşlemini Tamamla',
            default => 'İş İznini Görüntüle',
        };
    }

    private function getActionUrl(): string
    {
        return route('admin.work-permits.show', $this->workPermit);
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'work_permit_id' => $this->workPermit->id,
            'title' => $this->workPermit->title,
            'action_type' => $this->actionType,
            'message' => $this->getMessage(),
            'url' => $this->getActionUrl(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    public function broadcastType()
    {
        return 'admin.work-permit.notification';
    }
}
