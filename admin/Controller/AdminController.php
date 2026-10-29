<?php

namespace Admin\Controller;

use Engine\Controller;
use Engine\Di\Di;

/**
 * Базовый контроллер защищённой части админки.
 * Исправления:
 *  - проверка авторизации через серверную сессию (Auth), а не cookie-хеш;
 *  - redirect со статусом 302 вместо «header()+exit» с дефолтным 301-кэшированием;
 *  - единая точка входа для всех админ-контроллеров.
 */
class AdminController extends Controller
{
    public function __construct(Di $di)
    {
        parent::__construct($di);

        if (!$this->auth()->authorized()) {
            $this->redirect('/admin/login');
        }
    }

    public function logout()
    {
        $this->auth()->logout();
        $this->redirect('/admin/login');
    }
}
