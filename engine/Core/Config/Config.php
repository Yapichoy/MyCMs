<?php

namespace Engine\Core\Config;

/**
 * Конфигурация ENV-пакета (cms|admin).
 * Исправления:
 *  - путь строится от ROOT_PROJECT (надёжнее DOCUMENT_ROOT при CLI/симлинках);
 *  - файлы кэшируются в статическом реестре (нет повторных require на каждый вызов);
 *  - сравнение ENV регистронезависимо (был баг 'CMS' vs 'cms').
 */
class Config
{
    /** @var array<string, array> */
    private static $items = [];

    public static function item($key, $group = 'main')
    {
        $groupItems = static::file($group);
        return isset($groupItems[$key]) ? $groupItems[$key] : null;
    }

    public static function file($group)
    {
        if (isset(self::$items[$group])) {
            return self::$items[$group];
        }

        $root = defined('ROOT_PROJECT') ? ROOT_PROJECT : dirname(__DIR__, 3);
        $env  = strtolower(ENV); // 'cms' | 'admin'
        $path = sprintf('%s/%s/Config/%s.php', $root, $env, $group);

        if (!is_file($path)) {
            throw new \RuntimeException(sprintf('Config file not found: %s', $path));
        }
        $items = require $path;
        if (empty($items) && $group !== 'main') {
            throw new \RuntimeException(sprintf('Config file is empty: %s', $path));
        }
        self::$items[$group] = is_array($items) ? $items : [];
        return self::$items[$group];
    }

    /** Сброс кэша (для тестов) */
    public static function reset()
    {
        self::$items = [];
    }
}
