<?php


namespace LaravelAssets;


class Manager
{
    public static function config($key = "assets"){

    	$configAssets = config("assets");

    	if(!is_array($configAssets)){
		    $configAssets = [];
	    }

        $config = [
            "assets" => array_merge(require __DIR__."/../config/assets.php", $configAssets)
        ];

        return array_get($config, $key);
    }
}
