<?php
/**
 * Единая обработка ошибок.
 * - production: детали (стектрейсы, SQL) клиенту не показываются, пишутся в лог;
 * - development (APP_DEBUG=1): подробный вывод на экран.
 */

namespace Engine\Core\Error;

class ErrorHandler
{
    /** @var string|null */
    private static $logFile;
    /** @var bool */
    private static $debug = false;

    public static function init(?string $logFile = null, bool $debug = false): void
    {
        self::$logFile = $logFile;
        self::$debug = $debug;

        error_reporting(E_ALL);
        ini_set('display_errors', $debug ? '1' : '0');
        ini_set('log_errors', '1');

        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    /**
     * Преобразует PHP-ошибки в ErrorException.
     */
    public static function handleError(int $severity, string $message, string $file = '', int $line = 0): bool
    {
        if (!(error_reporting() & $severity)) {
            return false;
        }
        throw new \ErrorException($message, 0, $severity, $file, $line);
    }

    public static function handleException(\Throwable $e): void
    {
        self::log($e);
        if (self::$debug) {
            http_response_code(500);
            header('Content-Type: text/plain; charset=utf-8');
            echo sprintf(
                "Uncaught %s: %s in %s:%d\nStack trace:\n%s",
                get_class($e),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $e->getTraceAsString()
            );
        } else {
            http_response_code(500);
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            header('Content-Type: text/html; charset=utf-8');
            echo '<!doctype html><title>500</title>'
                . '<h1>Внутренняя ошибка сервера</h1><p>Попробуйте позже.</p>';
        }
    }

    public static function handleShutdown(): void
    {
        $err = error_get_last();
        if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            self::log(new \ErrorException($err['message'], 0, $err['type'], $err['file'], $err['line']));
            if (!self::$debug) {
                http_response_code(500);
            }
        }
    }

    private static function log(\Throwable $e): void
    {
        $entry = sprintf(
            "[%s] %s: %s in %s:%d\n%s\n",
            date('Y-m-d H:i:s'),
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );

        if (self::$logFile !== null) {
            $dir = dirname(self::$logFile);
            if (!is_dir($dir)) {
                @mkdir($dir, 0750, true);
            }
            @file_put_contents(self::$logFile, $entry, FILE_APPEND | LOCK_EX);
        } else {
            error_log($entry);
        }
    }
}
