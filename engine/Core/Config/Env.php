<?php

namespace Engine\Core\Config;

/**
 * Доступ к конфигурации окружения.
 *
 * Приоритет источников:
 *   1) реальные переменные окружения (getenv / $_ENV / $_SERVER);
 *   2) файл .env в корне проекта (парсится встроенным лёгким парсером —
 *      без обязательной зависимости от vendor);
 *   3) значения по умолчанию.
 *
 * Секретов в коде нет: учётные данные БД приходят только из окружения.
 */
class Env
{
    /** @var array<string,string>|null */
    private static $vars;

    /**
     * Лень: читаем и кэшируем .env один раз за запрос.
     *
     * @param string|null $overrideFile путь к файлу (для тестов)
     */
    public static function load(?string $overrideFile = null): void
    {
        if (self::$vars !== null && $overrideFile === null) {
            return;
        }

        $vars = [];
        $file = $overrideFile ?? self::rootDir() . '/.env';
        if (is_readable($file)) {
            foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                $line = trim($line);
                if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
                    continue;
                }
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                // снять кавычки "value" / 'value'
                if (strlen($value) > 1
                    && (($value[0] === '"' && substr($value, -1) === '"')
                     || ($value[0] === "'" && substr($value, -1) === "'"))) {
                    $value = substr($value, 1, -1);
                }
                if ($key !== '') {
                    $vars[$key] = $value;
                }
            }
        }
        self::$vars = $vars;
    }

    /**
     * Ищет переменную: getenv -> $_ENV -> $_SERVER -> .env -> default.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        self::load();

        $fromEnv = getenv($key);
        if ($fromEnv !== false && $fromEnv !== '') {
            return $fromEnv;
        }
        if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
            return (string)$_ENV[$key];
        }
        if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
            return (string)$_SERVER[$key];
        }
        if (isset(self::$vars[$key]) && self::$vars[$key] !== '') {
            return self::$vars[$key];
        }
        return $default;
    }

    public static function bool(string $key, bool $default = false): bool
    {
        $v = self::get($key);
        if ($v === null) {
            return $default;
        }
        return in_array(strtolower($v), ['1', 'true', 'yes', 'on'], true);
    }

    /** Корень проекта (папка composer.json). */
    public static function rootDir(): string
    {
        return defined('ROOT_PROJECT') ? ROOT_PROJECT : dirname(__DIR__, 3);
    }
}
