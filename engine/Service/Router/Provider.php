<?php
/**
 * Created by PhpStorm.
 * User: Doctor
 * Date: 18.12.2017
 * Time: 21:37
 */

namespace Engine\Service\Router;


use Engine\Core\Router\Router;
use Engine\Service\AbstractProvider;

class Provider extends AbstractProvider{

    public $serviceName = 'router';

    /**
     *
     */
    public function init()
    {
        $router = new Router('http://mycms2/');
        $this->di->set($this->serviceName, $router);
    }
}