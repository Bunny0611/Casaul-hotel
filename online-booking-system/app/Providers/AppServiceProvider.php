<?

namespace App\Providers;

use App\Mail\CloudflareTransport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Mail::extend('cloudflare', function (array $config) {
            return new CloudflareTransport(
                config('services.cloudflare.account_id'),
                config('services.cloudflare.api_token')
            );
        });
    }
}