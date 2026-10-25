<?php

namespace App\Providers;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

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
        View::composer('partials.topbar-admin', function ($view) {
        if (Auth::guard('admin')->check()) {
            $unreadCount = \App\Models\Notification::where('admin_id', Auth::guard('admin')->id())
                ->where('is_read', false)
                ->count();
            $view->with('unreadCount', $unreadCount);
        }
    });
    }
}
