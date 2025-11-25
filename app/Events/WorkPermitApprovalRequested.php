<?php

namespace App\Events;

use App\Models\WorkPermitForm;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkPermitApprovalRequested
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public WorkPermitForm $workPermit,
        public string $phase,
        public string $step
    ) {}
}
