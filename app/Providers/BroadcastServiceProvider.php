<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Only register broadcasting routes if we're not using the null driver
        if (config('broadcasting.default') !== 'null') {
            Broadcast::routes();

            require base_path('routes/channels.php');
        }
    }
}
