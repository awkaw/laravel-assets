<?php

namespace LaravelAssets;

class ServiceProvider extends \Illuminate\Support\ServiceProvider{

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
        if(in_array(ServiceProvider::class, config("app.providers")) && (config("app.env") !== "production" || (config("app.env") == "production" && config("assets.watch_files_when_production")))){
            
            LessService::checkFiles();
            JsService::checkFiles();
            SvgService::checkFiles();
        }
    }
}