<?php

namespace Engine\Service\Load;

use Engine\Load;
use Engine\Service\AbstractProvider;

class Provider extends AbstractProvider
{
    public $serviceName = 'load';

    public function init()
    {
        // Di передаётся явно вместо global $di
        $load = new Load($this->di);
        $this->di->set($this->serviceName, $load);
    }
}
