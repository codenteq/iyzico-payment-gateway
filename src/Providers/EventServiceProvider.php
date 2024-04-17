<?php

namespace Webkul\Iyzico\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        Event::listen('sales.invoice.save.after', 'Webkul\Iyzico\Listeners\Transaction@saveTransaction');
    }
}
