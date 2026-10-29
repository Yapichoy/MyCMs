<?php

namespace Engine\Helper;

/**
 * Работа с cookie.
 * Исправления безопасности: раньше setcookie вызывался без флагов — куки были
 * доступны JavaScript (XSS-кража сессии) и по HTTP. Теперь по умолчанию:
 * HttpOnly + SameSite=Lax, Secure — при HTTPS.
 */
class Cookie
{
    /**
     * @param int $time срок жизни в секундах (0 — до конца сессии)
     */
    public static function set($key, $value, $time = 31536000)
    {
        setcookie($key, $value, [
            'expires'  => $time > 0 ? time() + $time : 0,
            'path'     => '/',
            'secure'   => !empty($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    public static function get($key)
    {
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
    }

    public static function delete($key)
    {
        setcookie($key, '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'secure'   => !empty($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        unset($_COOKIE[$key]);
    }
}
