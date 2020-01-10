<?php


namespace LaravelAssets;

use Illuminate\Support\Facades\Log;

class Logger{

	public static function debug($data){

		if(config("assets.debug")){
			Log::debug($data);
		}
	}

	public static function error($data){
		Log::error($data);
	}

	public static function warning($data){
		Log::warning($data);
	}
}