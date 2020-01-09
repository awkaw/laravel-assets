<?php

namespace LaravelAssets;

use Illuminate\Support\Facades\Log;

class LessService{

    /*
     * apt-get install node-less
     * npm install -g less
     * */

    static public function checkFiles(){

        if(!config("assets.less.enabled")){
            return false;
        }

        $filesSources = config("assets.less.sources");

        foreach(glob($filesSources."/*", GLOB_ONLYDIR) as $dir){

            $cssFile = self::getCssFilePath($dir);

            $modified = false;

            foreach(glob($dir."/*.less") as $file){

                if($modified){
                    continue;
                }

                $modified = (!file_exists($cssFile) || (filemtime($file) > filemtime($cssFile)));
            }

            if($modified){
                self::compile($dir);
            }
        }
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

        if(file_exists($lessFile)){

	        if(file_exists($cssFile)){
		        unlink($cssFile);
	        }

            $command = "lessc {$lessFile} {$cssFile} 2>&1";

            exec($command, $output);

            if(empty($output)){

                Log::debug("{$cssFile} compiled");

            }else{

            	if(file_exists($cssFile)){
		            unlink($cssFile);
	            }

                Log::debug($output);
            }
        }
    }
}
