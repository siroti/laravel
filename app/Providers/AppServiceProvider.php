<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Http\ViewComposer\ConfigComposer;

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
        // Carrega funções utilitárias
        require_once app_path('NewUtils/Utils.php');
        require_once app_path('NewUtils/StringUtils.php');

        // Compartilha $config em todas as views
        View::composer('*', ConfigComposer::class);
    }
}