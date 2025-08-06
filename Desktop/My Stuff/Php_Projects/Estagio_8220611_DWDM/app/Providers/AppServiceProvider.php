<?php

namespace App\Providers;

use App\Models\Domain;
use App\Models\FinancialMovement;
use App\Models\Hosting;
use App\Models\Invoice;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Observers\DomainObserver;
use App\Observers\FinancialMovementObserver;
use App\Observers\HostingObserver;
use App\Observers\InvoiceObserver;
use App\Observers\SupportTicketObserver;
use App\Observers\TicketReplyObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Invoice::observe(InvoiceObserver::class);
        Hosting::observe(HostingObserver::class);
        SupportTicket::observe(SupportTicketObserver::class);
        TicketReply::observe(TicketReplyObserver::class);
        Domain::observe(DomainObserver::class);
        FinancialMovement::observe(FinancialMovementObserver::class);
    }
}
