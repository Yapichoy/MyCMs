<?php
/**
 * Created by PhpStorm.
 * User: Doctor
 * Date: 19.12.2017
 * Time: 19:30
 */

namespace Engine\Core\Router;


class UrlDispatcher {
    private $methods = [
      'GET','POST'
    ];
    private $routes = [
        'GET' => [],
        'POST'=> []
    ];
    private $patterns = [
        'int' => '[0-9]+',
        'str' => '[a-zA-Z\.\-_%]+',
        'any' => '[a-zA-Z0-9\.\-_%]+'
    ];
    private function routes($method){
        return isset($this->routes[$method])? $this->routes[$method] : [];
    }
    /**
     * @param $key
     * @param $pattern
     */
    public function  register($method,$pattern,$controller){
        $convert = $this->convertPattern($pattern);
        $this->routes[strtoupper($method)][$convert] = $controller;
    }
    private function convertPattern($pattern){
        if(strpos($pattern,'{')===false){
            return $pattern;
        }
        return preg_replace_callback('#\{(\w+):(\w+)\}#',[$this,'replacePattern'],$pattern);

    }
    private  function replacePattern($matches){
        //print_r ($matches);
        return '(?<'.$matches[1].'>'.strtr($matches[2],$this->patterns).')';
    }
    private function processParam($parameters){
        foreach($parameters as $k =>$val){
            if(is_int($k)){
                unset($parameters[$k]);
            }
        }
        return $parameters;
    }
    public function addPattern($key, $pattern){
            $this->patterns[$key]= $pattern;
    }
    private function doDispatch($method,$uri){
        foreach($this->routes(strtoupper($method)) as $route => $controller){
            $pattern = '#^'.$route.'$#s';
            if(preg_match($pattern,$uri,$parameters)){
                return new DispatchedRoute($controller,$this->processParam($parameters));
            }
        }
    }
    public function Dispatch($method,$uri){
        $routes = $this->routes(strtoupper($method));
        if(array_key_exists($uri,$routes)){
            return new DispatchedRoute($routes[$uri]);
        }
        return $this->doDispatch($method,$uri);
    }
}