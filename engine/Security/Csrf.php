<?php
/**
 * CSRF-защита: токен хранится на сервере в сессии, сверяется на каждый unsafe-запрос.
 */

namespace Engine\Security;

class Csrf
{
    const SESSION_KEY = '_csrf_token';
    const FIELD_NAME  = '_csrf';

    public static function token(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return '';
        }
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::SESSION_KEY];
    }

    /**
     * Скрытое поле для вставки в формы.
     */
    public static function field(): string
    {
        return sprintf(
            '<input type="hidden" name="%s" value="%s">',
            self::FIELD_NAME,
            htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8')
        );
    }

    public static function validate(?string $token): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE || empty($_SESSION[self::SESSION_KEY])) {
            return false;
        }
        return is_string($token) && hash_equals($_SESSION[self::SESSION_KEY], $token);
    }

    /**
     * Проверка POST-запроса; при неудаче — 419 и остановка выполнения.
     */
    public static function verifyRequest(array $post): void
    {
        if (!self::validate($post[self::FIELD_NAME] ?? null)) {
            http_response_code(419);
            exit('CSRF validation failed.');
        }
    }
}
