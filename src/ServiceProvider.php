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
	    if ($this->app->runningInConsole()) {
		    $this->commands([
			    CompileCommand::class,
		    ]);
	    }
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