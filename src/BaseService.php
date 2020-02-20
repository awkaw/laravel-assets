<?php


namespace LaravelAssets;


use Illuminate\Support\Facades\Cache;

class BaseService
{
    public static function chmodFiles($dir){
        exec("chmod -R 0777 {$dir}");
    }

    protected static function getFiles($dir, $ext){

        $files = [];

        foreach(glob($dir."/*.{$ext}") as $file){
            $files[] = $file;
        }

        foreach(glob($dir."/*/*.{$ext}") as $file){
            $files[] = $file;
        }

        foreach(glob($dir."/*/*/*.{$ext}") as $file){
            $files[] = $file;
        }

        return $files;
    }

    protected static function getMaxFileTime($dir, $ext){

        $files = self::getFiles($dir, $ext);
        $times = [];

        if(!empty($files)){

            foreach ($files as $file) {

                $time = filemtime($file);
                $size = filesize($file);

                /* Hack for error set time on save */
                $oldSize = Cache::get($file);

                if(!is_null($oldSize) && $oldSize != $size){
                    $time = time();
                }

                Cache::forever($file, $size);

                $times[] = $time;
            }
        }

        return max($times);
    }
}
