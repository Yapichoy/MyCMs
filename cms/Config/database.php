<?php
/**
 * Конфигурация подключения к БД (публичная часть CMS).
 * Исправлен баг: ранее отсутствовали ключи db_name/charset, что вызывало fatal error.
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
