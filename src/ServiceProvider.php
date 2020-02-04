<?php

namespace LaravelAssets;

use LaravelAssets\Commands\CompileCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            CompileCommand::class,
        ]);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/assets.php', 'assets'
        );

        if(in_array(ServiceProvider::class, config("app.providers")) && (config("app.env") !== "production" || (config("app.env") == "production" && config("assets.watch_files_when_production")))){

            LessService::checkFiles();
            JsService::checkFiles();
            SvgService::checkFiles();
        }
    }
}
