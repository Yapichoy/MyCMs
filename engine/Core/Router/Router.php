<?php

namespace Engine\Core\Router;

/**
 * Исправлен fatal error: `$this->host = host;` (неопределённая константа).
 */
class Router
{
    private $routes = [];
    private $host;
    /** @var UrlDispatcher|null */
    private $dispatcher;

    public function __construct($host = null)
    {
        $this->host = $host;
    }

    public function add($key, $pattern, $controller, $method = 'GET')
    {
        $this->routes[$key] = [
            'pattern'    => $pattern,
            'controller' => $controller,
            'method'     => strtoupper($method),
        ];
    }

    public function dispatch($method, $uri)
    {
        return $this->getDispatcher()->dispatch($method, $uri);
    }

    /** @return UrlDispatcher */
    public function getDispatcher()
    {
        if ($this->dispatcher === null) {
            $this->dispatcher = new UrlDispatcher();
            foreach ($this->routes as $route) {
                $this->dispatcher->register($route['method'], $route['pattern'], $route['controller']);
            }
        }
        return $this->dispatcher;
    }

    public function getHost()
    {
        return $this->host;
    }
}
