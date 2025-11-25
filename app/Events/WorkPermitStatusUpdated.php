<?php

namespace App\Events;

use App\Models\WorkPermitForm;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkPermitStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public WorkPermitForm $workPermit,
        public string $action,
        public string $message,
        public ?User $updatedBy = null
    ) {}

    public function broadcastOn()
    {
        // Şirket bazlı private channel
        return new PrivateChannel('company.' . $this->workPermit->company_id);
    }

    public function broadcastAs()
    {
        return 'admin.work-permit.updated';
    }

    public function broadcastWith()
    {
        return [
            'work_permit_id' => $this->workPermit->id,
            'title' => $this->workPermit->title,
            'status' => $this->workPermit->status,
            'status_text' => $this->workPermit->status_text,
            'action' => $this->action,
            'message' => $this->message,
            'updated_by' => $this->updatedBy?->name ?? 'Sistem',
            'timestamp' => now()->toISOString(),
        ];
    }
}
