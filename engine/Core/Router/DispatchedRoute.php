<?php
/**
 * Created by PhpStorm.
 * User: Doctor
 * Date: 19.12.2017
 * Time: 19:44
 */

namespace Engine\Core\Router;


class DispatchedRoute {
    private $controller;
    private $parameters;

    /**
     * @param $controller
     * @param array $parameters
     */
    public function __construct($controller,$parameters=[]){
        $this->controller = $controller;
        $this->parameters = $parameters;
    }

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param mixed $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return mixed
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param mixed $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }
}