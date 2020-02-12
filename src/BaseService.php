<?php


namespace LaravelAssets;


class BaseService
{
    public static function chmodFiles($dir){
        exec("chmod -R 0777 {$dir}");
    }
}
