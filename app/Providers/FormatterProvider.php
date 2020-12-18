<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Formatters\ProductFormatter;

class FormatterProvider extends ServiceProvider{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(){
        
        $this->app->singleton('Formatter/Product', function ($app) {
            return new ProductFormatter();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(){
        //
    }
}