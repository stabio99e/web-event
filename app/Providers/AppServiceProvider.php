<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use App\Models\Pages;
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
        Schema::defaultStringLength(191);

        $webConfig = DB::table('web_config')->first();
        View::share('webConfig', $webConfig);

        View::composer('*', function ($view) {
            $footerPages = Pages::where('is_published', true)->orderBy('order')->get();
            $view->with('footerPages', $footerPages);
        });
        Paginator::useBootstrap();
    }
}
