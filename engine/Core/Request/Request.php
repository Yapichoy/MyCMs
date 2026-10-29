<?php

namespace Engine\Core\Request;

/**
 * Инкапсуляция суперглобальных.
 * Доработано: типизированные геттеры post()/get() с приведением к скалярам —
 * контроллерам не нужно лазить в $_POST напрямую.
 */
class Request
{
    public $query = [];
    public $body = [];
    public $request = [];
    public $cookies = [];
    public $files = [];
    public $server = [];

    public function __construct()
    {
        $this->query   = $_GET;
        $this->body    = $_POST;
        $this->request = $_REQUEST;
        $this->cookies = $_COOKIE;
        $this->files   = $_FILES;
        $this->server  = $_SERVER;
    }

    /** Значение POST по ключу (string|null) */
    public function post($key = null, $default = null)
    {
        if ($key === null) {
            return $this->body;
        }
        $v = $this->body[$key] ?? $default;
        return is_array($v) ? $v : ($v === null ? null : (string)$v);
    }

    /** Значение GET по ключу */
    public function query($key = null, $default = null)
    {
        if ($key === null) {
            return $this->query;
        }
        $v = $this->query[$key] ?? $default;
        return is_array($v) ? $v : ($v === null ? null : (string)$v);
    }

    public function isPost(): bool
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }
}
