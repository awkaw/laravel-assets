<?php

namespace LaravelAssets;

class JsService extends BaseService {

    const EXT = "js";
    private static $sources = [];

    static public function registerSources($path){

        if(!in_array($path, self::$sources)){
            self::$sources[] = $path;
        }
    }

    static public function checkFiles(){

		if(!config("assets.scripts.enabled")){
            return false;
        }

        $configSources = config("assets.scripts.sources");

        if(!in_array($configSources, self::$sources)){
            self::registerSources($configSources);
        }

        if(!empty(self::$sources)) {

            foreach (self::$sources as $filesSources) {

                if (file_exists($filesSources)) {

                    foreach(glob($filesSources."/*", GLOB_ONLYDIR) as $dir){

                        $compileFile = self::getJsCompiledFilePath($dir);

                        $modified = !file_exists($compileFile);

                        if(!$modified){

                            $maxTimeFile = self::getMaxFileTime($dir, self::EXT);

                            $modified = ($maxTimeFile > filemtime($compileFile));
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

	static private function getJsCompiledFilePath($dir){

		$filesCompiled = config("assets.scripts.compiled");

		return $filesCompiled."/".basename($dir).".js";
	}

	static private function getJsFilePath($dir){
		return $dir."/".basename($dir).".js";
	}

	static public function compile($dir){

		$jsCompiledFile = self::getJsCompiledFilePath($dir);
		$jsFile = self::getJsFilePath($dir);

		$filesAdded = [];
		$content = "";

		if(file_exists($jsFile)){

            $filesAdded[] = $jsFile;

            Logger::debug("{$jsFile} added");

			$content .= file_get_contents($jsFile);
		}

        $files = self::getFiles($dir, self::EXT);

        Logger::debug("List files");
        Logger::debug($files);

		if(!empty($files)){

            foreach($files as $file){

                if(!in_array($file, $filesAdded) && !preg_match('#init\.'.self::EXT.'#', $file)){

                    $filesAdded[] = $file;

                    Logger::debug("{$jsFile} added");

                    $content .= file_get_contents($file);
                }
            }

            // init.js last include
            $jsInitFile = $dir."/init.".self::EXT;

            if(file_exists($jsInitFile)){

                Logger::debug("{$jsInitFile} added");

                $content .= file_get_contents($jsInitFile);
            }

            $jsDir = dirname($jsCompiledFile);

            if(!file_exists($jsDir)){
                mkdir($jsDir, 0755, true);
            }

            if(file_put_contents($jsCompiledFile, $content)){

                $maxTime = self::getMaxFileTime($dir, self::EXT);

                touch($jsCompiledFile, $maxTime, $maxTime);

                self::chmodFiles($jsDir);

                Logger::debug("{$jsCompiledFile} compiled");
            }else{
                Logger::debug("{$jsCompiledFile} error");
            }
        }
	}
}
