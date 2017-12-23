<?php
/**
 * Created by PhpStorm.
 * User: Doctor
 * Date: 22.12.2017
 * Time: 20:17
 */

namespace Engine\Service\View;
use Engine\Service\AbstractProvider;
use Engine\Core\Template\View;

class Provider extends AbstractProvider{

    public $serviceName = 'view';

    public function init()
    {
        $view = new View();
        $this->di->set($this->serviceName, $view);
    }
}