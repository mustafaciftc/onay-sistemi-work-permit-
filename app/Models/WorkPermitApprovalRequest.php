<?php

namespace App\Mail;

use App\Models\WorkPermitForm;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WorkPermitApprovalRequest extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public WorkPermitForm $workPermit,
        public User $approver,
        public string $step
    ) {}

    public function build()
    {
        return $this->subject("Onay Bekleyen İş İzni: {$this->workPermit->permit_code}")
            ->markdown('emails.work-permits.approval-request', [
                'workPermit' => $this->workPermit,
                'approver' => $this->approver,
                'step' => $this->step,
                'url' => route('admin.work-permits.show', $this->workPermit),
            ]);
    }
}
