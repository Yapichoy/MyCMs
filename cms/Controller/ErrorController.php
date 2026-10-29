<?php

namespace CMS\Controller;

/**
 * Публичная 404. Исправление: убран вывод ENV (утечка internals).
 */
class ErrorController extends CmsController
{
    public function page404()
    {
        http_response_code(404);
        echo '<h1>404 — страница не найдена</h1>';
    }
}
