<?php

namespace LaravelAssets;

use Illuminate\Support\Str;

class LessService extends BaseService {

    const EXT = "less";
    private static $sources = [];

    static public function registerSources($path){

        if(!in_array($path, self::$sources)){
            self::$sources[] = $path;
        }
    }

    static public function checkFiles(){

        if(!config("assets.less.enabled")){
            return false;
        }

        $configSources = config("assets.less.sources");

        if(!in_array($configSources, self::$sources)){
            self::registerSources($configSources);
        }

        if(!empty(self::$sources)){

            foreach (self::$sources as $filesSources) {

                if(file_exists($filesSources)){

                    foreach(glob($filesSources."/*", GLOB_ONLYDIR) as $dir){

                        $cssFile = self::getCssFilePath($dir);

                        $modified = !file_exists($cssFile);

                        if(!$modified){

                            $maxTimeFile = self::getMaxFileTime($dir, self::EXT);

                            $modified = ($maxTimeFile > filemtime($cssFile));
                        }

                        if($modified){
                            self::compile($dir);
                        }
                    }
                }
            }
        }

        return true;
    }

    static private function getCssFilePath($dir){

        $filesCompiled = config("assets.less.compiled");

        return $filesCompiled."/".basename($dir).".css";
    }

    static private function getLessFilePath($dir){
        return $dir."/".basename($dir).".less";
    }

    static public function compile($dir){

        $lessFile = self::getLessFilePath($dir);
        $cssFile = self::getCssFilePath($dir);

        $cssDir = dirname($cssFile);

        if(!file_exists($cssDir)){
        	mkdir($cssDir, 0755, true);
        }

        if(file_exists($lessFile)){

	        if(file_exists($cssFile)){
		        unlink($cssFile);
	        }

	        $minify = "";

	        if(config("assets.less.minify")){
	            $minify = "--clean-css";
            }

            $command = "lessc {$lessFile} {$cssFile} {$minify} 2>&1";

	        Logger::debug($command);

            exec($command, $output);

            if(!is_null($output) && is_array($output) && count($output) > 0){
                Logger::debug($output);
            }

            if(empty($output)){

            	if(file_exists($cssFile)){

            	    $maxTime = self::getMaxFileTime($dir, self::EXT);

            	    touch($cssFile, $maxTime, $maxTime);

                    self::chmodFiles($cssDir);

		            Logger::debug("{$cssFile} compiled");
	            }else{
		            Logger::debug("{$cssFile} error");
	            }

            }else{

            	if(file_exists($cssFile)){
		            unlink($cssFile);
	            }

	            Logger::debug($output);
            }
        }
    }
}
