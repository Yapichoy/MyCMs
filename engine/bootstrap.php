<?php
/**
 * Точка инициализации приложения.
 * Исправления:
 *  - убрано «проглатывание» ошибок (было: catch { $e->getMessage(); } — без вывода и лога);
 *  - единый ErrorHandler: в production детали не утекают клиенту, пишутся в logs/error.log;
 *  - безопасные настройки сессий задаются до session_start();
 *  - Di объявляется как единственный источник зависимостей (без global-магии в сервисах).
 */

define('ROOT_PROJECT', dirname(__DIR__));

require_once ROOT_PROJECT . '/vendor/autoload.php';

use Engine\Cms;
use Engine\Core\Error\ErrorHandler;
use Engine\Di\Di;

// --- Ошибки: логируем, наружу ничего не протекает (кроме APP_DEBUG=true) ---
ErrorHandler::init(
    ROOT_PROJECT . '/logs/error.log',
    \Engine\Core\Config\Env::bool('APP_DEBUG', false)
);

// --- Безопасные cookie-параметры сессии (применяются при старте сессии в Auth) ---
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => '',
    'secure'   => !empty($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Lax',
]);
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.use_only_cookies', '1');

error_reporting(E_ALL);

try {
    $di = new Di();

    $services = require __DIR__ . '/Config/Service.php';
    foreach ($services as $service) {
        /** @var \Engine\Service\AbstractProvider $provider */
        $provider = new $service($di);
        $provider->init();
    }

    $cms = new Cms($di);
    $cms->run();
} catch (\Throwable $e) {
    // fallback: если сам ErrorHandler не смог перехватить
    ErrorHandler::handleException($e);
}
