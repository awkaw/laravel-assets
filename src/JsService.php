<?php

namespace LaravelAssets;

use Illuminate\Support\Facades\Log;

class JsService{

    static public function checkFiles(){

		if(!config("assets.scripts.enabled")){
            return false;
        }

        $filesSources = config("assets.scripts.sources");

        foreach(glob($filesSources."/*", GLOB_ONLYDIR) as $dir){

            $spriteFile = self::getJsCompiledFilePath($dir);

            $modified = false;

            foreach(glob($dir."/*.js") as $file){

                if($modified){
                    continue;
                }

                $modified = (!file_exists($spriteFile) || (filemtime($file) > filemtime($spriteFile)));
            }

            if($modified){
                self::compile($dir);
            }
        }
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

		foreach(glob($dir."/*.js") as $file){

			if(!in_array($file, $files)){

				$files[] = $file;

				$content .= file_get_contents($file);
			}
		}

		if(!empty($files) && file_put_contents($jsCompiledFile, $content)){
			Log::debug("{$jsCompiledFile} compiled");
		}else{
			Log::debug("{$jsCompiledFile} error");
		}
	}
}