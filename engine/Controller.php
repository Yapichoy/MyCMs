<?php

namespace Engine;

use Engine\Core\Auth\Auth;
use Engine\Di\Di;

/**
 * Базовый контроллер.
 * Доработано: единая точка доступа к Auth (shared-сервис контейнера), хелпер
 * redirect() со статус-кодом 302 (был 301 — кэш браузера ломал logout/login-циклы).
 */
abstract class Controller
{
    /** @var Di */
    protected $di;
    /** @var \Engine\Core\Database\Connection */
    protected $db;
    /** @var \Engine\Core\Template\View */
    protected $view;
    protected $config;
    /** @var \Engine\Core\Request\Request */
    protected $request;
    /** @var Load */
    protected $load;

    public function __construct(Di $di)
    {
        $this->di      = $di;
        $this->view    = $di->get('view');
        $this->db      = $di->get('db');
        $this->config  = $di->get('config');
        $this->request = $di->get('request');
        $this->load    = $di->get('load');
    }

    /** @return Auth */
    protected function auth()
    {
        return $this->di->get('auth');
    }

    protected function redirect($url, int $status = 302): void
    {
        header('Location: ' . $url, true, $status);
        exit;
    }
}
