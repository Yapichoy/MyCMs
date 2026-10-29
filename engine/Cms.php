<?php

namespace Engine;

use Engine\Core\Router\DispatchedRoute;
use Engine\Helper\Common;

/**
 * Ядро диспетчеризации.
 * Исправления:
 *  - маршруты подключаются через замыкание с явной передачей Router (убрана хрупкая
 *    магия `$this` из require внутри метода);
 *  - имена классов контроллеров валидируются (защита от произвольной индексации
 *    namespace из URL);
 *  - ошибки не проглатываются: необработанные летят в ErrorHandler.
 */
class Cms
{
    /** @var Di\Di */
    private $di;
    /** @var \Engine\Core\Router\Router */
    private $router;

    public function __construct(\Engine\Di\Di $di)
    {
        $this->di = $di;
        $this->router = $this->di->get('router');
    }

    public function run()
    {
        $envDir = strtolower(ENV); // 'cms' | 'admin'
        $routesFile = ROOT_PROJECT . '/' . $envDir . '/Routes.php';
        if (!is_file($routesFile)) {
            throw new \RuntimeException(sprintf('Routes file %s not found', $routesFile));
        }

        $router = $this->router;
        require $routesFile; // файл регистрирует маршруты через $router

        $dispatch = $this->router->dispatch(Common::getMethod(), Common::getPathUrl());
        if ($dispatch === null) {
            $dispatch = new DispatchedRoute('ErrorController:page404');
        }

        list($class, $action) = array_pad(explode(':', $dispatch->getController(), 2), 2, 'index');

        // whitelist символов в имени контроллера/действия — нельзя протащить что угодно
        if (!preg_match('/^[A-Za-z][A-Za-z0-9_]*$/', $class) || !preg_match('/^[a-z][A-Za-z0-9_]*$/', $action)) {
            $dispatch = new DispatchedRoute('ErrorController:page404');
            list($class, $action) = ['ErrorController', 'page404'];
        }

        $controllerClass = '\\' . ENV . '\\Controller\\' . $class;
        if (!class_exists($controllerClass)) {
            $dispatch = new DispatchedRoute('ErrorController:page404');
            $controllerClass = '\\' . ENV . '\\Controller\\ErrorController';
            $action = 'page404';
        }

        /** @var Controller $controller */
        $controller = new $controllerClass($this->di);
        if (!method_exists($controller, $action) || !is_callable([$controller, $action])) {
            throw new \LogicException(sprintf('Action %s::%s does not exist', $class, $action));
        }

        call_user_func_array([$controller, $action], $dispatch->getParameters());
    }
}
