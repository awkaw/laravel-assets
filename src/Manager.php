<?php


namespace LaravelAssets;


class Manager
{
    public static function config($key = "assets"){

        $config = [
            "assets" => array_merge(require __DIR__."/../config/assets.php", config("assets"))
        ];

        return array_get($config, $key);
    }
}
