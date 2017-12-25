<?php
/**
 * Created by PhpStorm.
 * User: Doctor
 * Date: 18.12.2017
 * Time: 21:37
 */

namespace Engine\Service\Load;


use Engine\Load;
use Engine\Service\AbstractProvider;

class Provider extends AbstractProvider{

    public $serviceName = 'load';
    public function init()
    {
        $load = new Load();
        $this->di->set($this->serviceName, $load);
    }
}