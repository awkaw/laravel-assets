<?php

namespace LaravelAssets;

use Illuminate\Support\ServiceProvider;

class LaravelAssetsProvider extends ServiceProvider{

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
        LessService::checkFiles();
        //JsService::checkFiles();
        //SvgService::checkFiles();
    }
}