<?php

namespace Admin\Controller;

/**
 * Ошибки админки. Не наследуем гостевой запрос от AdminController-защиты:
 * 404 показываем до редиректа на логин только для несуществующих путей.
 */
class ErrorController extends \Engine\Controller
{
    public function page404()
    {
        http_response_code(404);
        echo '<h1>404 — страница не найдена</h1>';
    }
}
