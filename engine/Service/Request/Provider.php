<?php
/**
 * Created by PhpStorm.
 * User: Doctor
 * Date: 18.12.2017
 * Time: 21:37
 */

namespace Engine\Service\Request;


use Engine\Core\Request\Request;
use Engine\Service\AbstractProvider;

class Provider extends AbstractProvider{

    public $serviceName = 'request';

    /**
     *
     */
    public function init()
    {
        $request = new Request();
        $this->di->set($this->serviceName, $request);
    }
}