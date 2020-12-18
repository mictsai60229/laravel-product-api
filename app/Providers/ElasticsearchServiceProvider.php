<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Elasticsearch\Indices;

class ElasticsearchServiceProvider extends ServiceProvider{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(){
        
        $this->app->singleton('Service\Elasticsearch\Indices', function ($app) {
            return new Indices();
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