<?php

namespace LaravelAssets;

use Illuminate\Support\Str;

class SvgService extends BaseService {

	const PREFIX = "symbol_";
    private static $sources = [];

    static public function registerSources($path){

        if(!in_array($path, self::$sources)){
            self::$sources[] = $path;
        }
    }

	static public function checkFiles(){

		if(!config("assets.svg.enabled")){
            return false;
        }

        $configSources = config("assets.svg.sources");

        if(!in_array($configSources, self::$sources)){
            self::registerSources($configSources);
        }

        if(!empty(self::$sources)) {

            foreach (self::$sources as $filesSources) {

                if (file_exists($filesSources)) {

                    foreach(glob($filesSources."/*", GLOB_ONLYDIR) as $dir){

                        $spriteFile = self::getSpriteFilePath($dir);

                        $modified = false;

                        foreach(glob($dir."/*.svg") as $file){

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
            }
        }
	}

	static private function getSpriteFilePath($dir){

		$filesCompiled = config("assets.svg.compiled");

		return $filesCompiled."/".basename($dir).".svg";
	}

	static private function getSvgFilePath($dir){
		return $dir."/".basename($dir).".svg";
	}

	static public function compile($dir){

        if(config("assets.svg.compiler") == "mix"){

            if(Str::lower(config("app.env")) == "production"){
                $command = "cd ".base_path("/")." && npm run prod";
            }else{
                $command = "cd ".base_path("/")." && npm run dev";
            }

            exec($command, $output);

            if(!is_null($output) && is_array($output) && count($output) > 0){
                Logger::debug($output);
            }

        }else{

            $spriteFile = self::getSpriteFilePath($dir);

            $baseDir = dirname($spriteFile);

            if (!file_exists($baseDir)) {
                mkdir($baseDir, 0755, true);
            }

            $files = glob($dir . "/*.svg");

            if (!empty($files)) {

                $dom = new \DOMDocument();

                $root = $dom->createElementNS('http://www.w3.org/2000/svg', 'svg');

                $dom->appendChild($root);

                $root->setAttribute('xmlns', 'http://www.w3.org/2000/svg');

                foreach ($files as $index => $file) {

                    $fileInfo = pathinfo($file);

                    $name = $fileInfo['filename'];

                    $symbol = $dom->createElement('symbol');

                    $doc = new \DOMDocument();
                    $doc->loadXML(file_get_contents($file));
                    $element = $doc->documentElement;

                    $symbol->setAttribute('id', self::PREFIX . "{$name}");
                    $symbol->setAttribute('class', "svg_image");
                    $symbol->setAttribute('viewBox', $element->attributes["viewBox"]->value);

                    if (count($element->childNodes) > 0) {

                        foreach ($element->childNodes as $child_node) {

                            $node = self::cloneNode($child_node, $dom);

                            if (!is_null($node)) {
                                $symbol->appendChild($node);
                            }
                        }
                    }

                    $root->appendChild($symbol);
                }

                $spriteDir = dirname($spriteFile);

                if (!file_exists($spriteDir)) {
                    mkdir($spriteDir, 0755, true);
                }

                $dom->save($spriteFile);

                self::chmodFiles($spriteDir);

                Logger::debug("{$spriteFile} compiled");
            }
        }
	}

	private static function cloneNode(\DOMNode $node, \DOMDocument $doc){

		if(!in_array($node->nodeName, ["g","path","rect","line"])){
			return null;
		}

		if($node->nodeName == "#text"){

			Logger::debug($node->nodeValue);

			return $doc->createTextNode($node->nodeValue);
		}

		$nd = $doc->createElement($node->nodeName);

		foreach($node->attributes as $value)
			$nd->setAttribute($value->nodeName,$value->value);

		if(!$node->childNodes)
			return $nd;

		foreach($node->childNodes as $child) {

			$result = self::cloneNode($child,$doc);

			if(!is_null($result)){
				$nd->appendChild($result);
			}
		}

		return $nd;
	}

	public static function svgFromFile($path){

		if(file_exists($path)){
			return self::generateIDs(file_get_contents($path));
		}

		return null;
	}

	private static function generateIDs($content){

	    if(preg_match_all('#\{(.*?)\}#', $content, $matches)){

	        $ids = [];

	        if(isset($matches[1]) && !empty($matches[1])){

                foreach ($matches[1] as $match) {
                    $ids[] = $match;
	            }

                if(!empty($ids)){
                    $ids = array_unique($ids);
                }

                foreach ($ids as $id) {

                    $rnd = "svg_".Str::random(16);

                    $content = preg_replace('#\{'.$id.'\}#', $rnd, $content);
                }
            }
        }

	    return $content;
    }

	public static function getUrlSymbol($symbol, $symbols = "icons"){
		return config("assets.svg.http_path")."/{$symbols}.svg?t=".@filemtime(config("assets.svg.compiled")."/{$symbols}.svg")."#".self::PREFIX."{$symbol}";
	}

	public static function printSvg($symbol){
		return "<svg role=\"img\" class='{$symbol}' aria-hidden=\"true\"><use xlink:href=\"".self::getUrlSymbol($symbol)."\"></use></svg>";
	}
}
