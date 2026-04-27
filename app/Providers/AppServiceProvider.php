<?php

namespace App\Providers;

use App\Models\ClientLog;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Inject pending count into the admin layout for the sidebar badge and nav bell
        View::composer('layouts.admin', function ($view) {
            $view->with('pendingCount', ClientLog::pending()->count());
        });
    }
}
