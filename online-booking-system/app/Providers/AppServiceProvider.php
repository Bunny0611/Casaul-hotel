<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Mail\CloudflareTransport;
use Illuminate\Support\Facades\Mail;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Mail::extend('cloudflare', function () {
            return new CloudflareTransport(
                config('services.cloudflare.account_id'),
                config('services.cloudflare.api_token')
            );
        });
    }

    
}
