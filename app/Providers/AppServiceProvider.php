<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Em produção (Hostinger) força https nos links gerados.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Formulário de orçamento: no máximo 3 envios por minuto e 10 por dia por IP.
        RateLimiter::for('leads', fn (Request $r) => [
            Limit::perMinute(3)->by($r->ip()),
            Limit::perDay(10)->by($r->ip()),
        ]);
    }
}
