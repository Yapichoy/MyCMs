<?php

namespace Engine\Core\Auth;

/**
 * Аутентификация на серверных PHP-сессиях.
 *
 * ЗАМЕНА (безопасность): прежняя схема «md5(email+password+rand-salt) в cookie
 * auth_user + такой же хеш в колонке user.hash» полностью удалена:
 *  - токен жил в куке и был уязвим к XSS-краже (cookie без HttpOnly/Secure);
 *  - соль генерировалась некриптографическим rand();
 *  - hash('sha256', $pass, $salt) вообще интерпретировал третий аргумент как
 *    raw-output flag, соль не участвовала.
 * Теперь идентичность хранится только на сервере ($_SESSION), кука — лишь
 * идентификатор сессии с флагами HttpOnly/SameSite/Secure (см. bootstrap).
 */
class Auth implements AuthInterface
{
    const SESSION_USER = '_auth_user_id';
    const SESSION_ROLE = '_auth_role';

    public function __construct()
    {
        $this->startSession();
    }

    protected function startSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            // Параметры куки выставлены в bootstrap до первого запуска сессии.
            session_start();
        }
    }

    public function authorized(): bool
    {
        return !empty($_SESSION[self::SESSION_USER]);
    }

    /** Совместимость со старым (опечаточным) именем метода */
    public function autorized(): bool
    {
        return $this->authorized();
    }

    public function login(int $userId, string $role): void
    {
        session_regenerate_id(true); // новый id сессии — против session fixation
        $_SESSION[self::SESSION_USER] = $userId;
        $_SESSION[self::SESSION_ROLE] = $role;
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $p['path'],
                $p['domain'],
                $p['secure'],
                $p['httponly']
            );
        }
        session_destroy();
    }

    /** @return int|null */
    public function userId()
    {
        return isset($_SESSION[self::SESSION_USER]) ? (int)$_SESSION[self::SESSION_USER] : null;
    }

    /** @return string|null */
    public function role()
    {
        return isset($_SESSION[self::SESSION_ROLE]) ? (string)$_SESSION[self::SESSION_ROLE] : null;
    }

    /* ---------- Хэширование паролей ---------- */

    /**
     * Надёжный хеш пароля (bcrypt/argon, соль из CSPRNG).
     * Заменяет сломанный encryptPassword($pass,$salt) = hash('sha256',$pass,$salt).
     */
    public static function hashPassword(string $plain): string
    {
        return password_hash($plain, PASSWORD_DEFAULT);
    }

    public static function verifyPassword(string $plain, string $hash): bool
    {
        return password_verify($plain, $hash);
    }

    /** Нужен ли апгрейд алгоритма хеша (для прозрачной ротации при логине) */
    public static function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_DEFAULT);
    }
}
