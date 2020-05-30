<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class JwtAuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Añadir la ruta del Helper
        require_once app_path().'/Helpers/JwtAuth.php';
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
