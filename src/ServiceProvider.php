<?php

namespace LaravelAssets;

use Illuminate\Support\ServiceProvider as Provider;

class ServiceProvider extends Provider{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if(config("app.env") !== "production" || (config("app.env") == "production" && config("assets.watch_files_when_production"))){
            
            LessService::checkFiles();
            JsService::checkFiles();
            SvgService::checkFiles();
        }
    }
}