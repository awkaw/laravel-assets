<?php


namespace LaravelAssets;

use Illuminate\Support\Facades\Log;

class Logger{

	public static function debug($data){

		if(Manager::config("assets.debug")){
			Log::debug($data);
		}
	}

	public static function info($data){

		if(Manager::config("assets.debug")){
			Log::info($data);
		}
	}

	public static function error($data){
		Log::error($data);
	}

	public static function warning($data){
		Log::warning($data);
	}
}
