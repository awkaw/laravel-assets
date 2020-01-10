<?php


namespace LaravelAssets;

use Illuminate\Support\Facades\Log;

class Logger{

	public static function debug($data){

		if(config("assets.debug")){
			Log::debug($data);
		}
	}
}