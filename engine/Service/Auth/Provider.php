<?php

namespace Engine\Service\Auth;

use Engine\Core\Auth\Auth;
use Engine\Service\AbstractProvider;

/**
 * Провайдер аутентификации: один Auth-инстанс на запрос (shared).
 */
class Provider extends AbstractProvider
{
    public $serviceName = 'auth';

    public function init()
    {
        $this->di->share($this->serviceName, function () {
            return new Auth();
        });
    }
}
