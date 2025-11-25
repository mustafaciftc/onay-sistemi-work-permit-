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

class NewApprovalAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public WorkPermitForm $workPermit,
        public User $assignedUser,
        public ?User $assignedBy = null
    ) {}

    public function broadcastOn()
    {
        // Sadece atanan kullanıcıya özel channel
        return new PrivateChannel('user.' . $this->assignedUser->id);
    }

    public function broadcastAs()
    {
        return 'approval.assigned';
    }

    public function broadcastWith()
    {
        return [
            'work_permit_id' => $this->workPermit->id,
            'title' => $this->workPermit->title,
            'company_name' => $this->workPermit->company->name,
            'assigned_to' => $this->assignedUser->name,
            'assigned_by' => $this->assignedBy?->name ?? 'Sistem',
            'current_step' => $this->workPermit->currentStep->step ?? 'unknown',
            'url' => route('admin.work-permits.show', $this->workPermit),
            'timestamp' => now()->toISOString(),
        ];
    }
}
