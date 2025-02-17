<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\Approval;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;



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
      if (env('APP_ENV') === 'production') {
          $this->app['request']->server->set('HTTPS', true);
      }
        Schema::defaultStringLength(191);

        View::composer('*', function ($view) {
            $pendingApprovalsCount = Approval::count();
            $view->with('pendingApprovalsCount', $pendingApprovalsCount);
        });

        Paginator::defaultView('custom-pagination');

    }
}
