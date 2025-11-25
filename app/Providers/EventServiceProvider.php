<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\WorkPermitApprovalRequested;
use App\Events\WorkPermitCompleted;
use App\Listeners\SendApprovalRequestEmail;
use App\Listeners\SendCompletionEmail;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        WorkPermitApprovalRequested::class => [
            SendApprovalRequestEmail::class,
        ],
        WorkPermitCompleted::class => [
            SendCompletionEmail::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}

