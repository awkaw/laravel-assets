<?php


namespace LaravelAssets;


class Manager
{
    public static function checkFiles(){
        LessService::checkFiles();
        JsService::checkFiles();
        SvgService::checkFiles();
    }
}
