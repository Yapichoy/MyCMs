<?php
/**
 * Конфигурация подключения к БД.
 * Секретов здесь нет — всё берётся из окружения (.env / переменные сервера).
 */

use Engine\Core\Config\Env;

return [
    'host'     => Env::get('DB_HOST', '127.0.0.1'),
    'port'     => Env::get('DB_PORT', '3306'),
    'db_name'  => Env::get('DB_NAME', 'mycms'),
    'user'     => Env::get('DB_USER', ''),
    'password' => Env::get('DB_PASSWORD', ''),
    'charset'  => Env::get('DB_CHARSET', 'utf8mb4'),
];
