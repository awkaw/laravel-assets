<?php

namespace LaravelAssets;

class LessService{

    /*
     * apt-get install node-less npm
     * curl -sL https://deb.nodesource.com/setup_13.x | bash -
     * apt-get install -y nodejs
     * ln -s /usr/bin/nodejs /usr/sbin/node
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

            $command = "lessc {$lessFile} {$cssFile} 2>&1";

	        Logger::debug($command);

            exec($command, $output);

            if(empty($output)){

            	if(file_exists($cssFile)){
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
