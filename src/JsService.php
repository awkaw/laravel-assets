<?php

namespace LaravelAssets;

class JsService extends BaseService {

    const EXT = "js";

    static public function checkFiles(){

		if(!config("assets.scripts.enabled")){
            return false;
        }

        $filesSources = config("assets.scripts.sources");

        foreach(glob($filesSources."/*", GLOB_ONLYDIR) as $dir){

            $compileFile = self::getJsCompiledFilePath($dir);

            $modified = !file_exists($compileFile);

            if(!$modified){

                $maxTimeFile = self::getMaxFileTime($dir, self::EXT);

                $modified = ($maxTimeFile > filemtime($compileFile));

                if($modified){
                    self::compile($dir);
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

		$files = [];
		$content = "";

		if(file_exists($jsFile)){

			$files[] = $jsFile;

			$content .= file_get_contents($jsFile);
		}

        $files = self::getFiles($dir, self::EXT);

		if(!empty($files)){

            foreach($files as $file){

                if(!in_array($file, $files) && !preg_match('#init\.'.self::EXT.'#', $file)){

                    $files[] = $file;

                    $content .= file_get_contents($file);
                }
            }

            // init.js last include
            $jsInitFile = $dir."/init.".self::EXT;

            if(file_exists($jsInitFile)){
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
