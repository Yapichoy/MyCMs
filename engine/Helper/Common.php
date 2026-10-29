<?php

namespace Engine\Helper;

/**
 * Хелперы HTTP-запроса.
 * Исправления:
 *  - getPathUrl(): `if ($pos = strpos(...))` терял обрезку, когда '?' на позиции 0,
 *    и не учитывал базовый префикс каталога (поддиректорные инсталляции);
 *  - статические методы: раньше вызывались статически, хотя были нестатическими
 *    (Deprecation в PHP 8).
 */
class Common
{
    public static function isPost()
    {
        return self::method() === 'POST';
    }

    public static function method()
    {
        return isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';
    }

    /** Совместимость с прежним именем метода */
    public static function getMethod()
    {
        return self::method();
    }

    /**
     * Путь текущего запроса без query-строки, относительно корня приложения.
     */
    public static function getPathUrl()
    {
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
        $qpos = strpos($uri, '?');
        if ($qpos !== false) {           // было: if ($position = strpos(...)) — false-bug на позиции 0
            $uri = substr($uri, 0, $qpos);
        }
        $uri = rawurldecode($uri);
        $uri = '/' . trim($uri, '/');

        // Отрезаем базовый префикс, если приложение живёт в подкаталоге
        $base = self::basePath();
        if ($base !== '' && strpos($uri, $base) === 0) {
            $uri = substr($uri, strlen($base));
        }
        return '/' . ltrim($uri, '/');
    }

    /**
     * Базовый URL-префикс из SCRIPT_NAME (например '/admin' для админ-панели).
     */
    public static function basePath()
    {
        $script = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
        $dir = rtrim(str_replace('\\', '/', dirname($script)), '/');
        // для root index.php dir == '' или '/'
        return ($dir === '/' || $dir === '.') ? '' : $dir;
    }
}
