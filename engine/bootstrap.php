<?php
require_once __DIR__.'/../vendor/autoload.php';
use Engine\Cms;
use Engine\Di\Di;
try{
    $di = new Di();
    $services = require __DIR__.'/Config/Service.php';
    foreach($services as $service){
        $provider = new $service($di);
        $provider->init();
    }
    $cms = new Cms($di);
    $cms ->run();
}catch (\ErrorException $e){
    $e->getMessage();
}