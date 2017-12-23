<?php
/**
 * Created by PhpStorm.
 * User: Doctor
 * Date: 17.12.2017
 * Time: 17:59
 */

namespace Engine;
use Engine\Core\Router\DispatchedRoute;
use Engine\Helper\Common;

class Cms {
    private $di;
    private $router;
    /**
     * @param $di
     */
    public function __construct(\Engine\Di\Di $di){
        $this->di = $di;
        $this->router = $this->di->get('router');
    }
    public function run(){
        try {
            $this->router->add('home', '/', 'HomeController:index');
            $this->router->add('news', '/news/{id:int}', 'HomeController:news');
            $routerDispatch = $this->router->dispatch(Common::getMethod(), Common::getPathUrl());
            if ($routerDispatch == null) {
                $routerDispatch = new DispatchedRoute('ErrorController:page404');
            }
            list($class, $action) = explode(':', $routerDispatch->getController(), 2);
            $controller = '\\CMS\\Controller\\' . $class . '';
            call_user_func_array([new $controller($this->di),$action],$routerDispatch->getParameters());

        }catch (\Exception $e){
                $e->getMessage();
            exit();
        }
    }
}