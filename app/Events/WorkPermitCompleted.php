<?php

namespace App\Events;

use App\Models\WorkPermitForm;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkPermitCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public WorkPermitForm $workPermit
    ) {}
}
