<?php

namespace Engine\Core\Auth;

/**
 * Контракт аутентификации (раньше интерфейс был пустым).
 */
interface AuthInterface
{
    /** Авторизован ли текущий субъект */
    public function authorized(): bool;

    /** Войти по id пользователя (после проверки пароля) */
    public function login(int $userId, string $role): void;

    /** Выйти */
    public function logout(): void;

    /** Id текущего пользователя или null */
    public function userId();

    /** Роль текущего пользователя или null */
    public function role();
}
