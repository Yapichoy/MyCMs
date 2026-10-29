<?php

namespace Admin\Model\User;

use Engine\Core\Database\ActiveRecord;

/**
 * Сущность пользователя.
 * Миграция безопасности: вместо `password` (открытый текст/sha256) и `hash`
 * (токен автологина из cookie) — только `password_hash` (bcrypt/argon2).
 */
class User
{
    use ActiveRecord;

    protected $table = 'user';

    public $id;
    public $email;
    public $password_hash;
    public $role;
    public $created_at;

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = (int)$id; }

    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = filter_var($email, FILTER_SANITIZE_EMAIL); }

    public function getPasswordHash() { return $this->password_hash; }

    /** Установить пароль: храним ТОЛЬКО надёжный хеш, открытый текст не сохраняется */
    public function setPassword(string $plain): void
    {
        $this->password_hash = password_hash($plain, PASSWORD_DEFAULT);
    }

    public function verifyPassword(string $plain): bool
    {
        return $this->password_hash !== null
            && password_verify($plain, $this->password_hash);
    }

    public function getRole() { return $this->role; }
    public function setRole($role)
    {
        if (!in_array($role, ['admin', 'editor'], true)) {
            throw new \InvalidArgumentException('Unknown role');
        }
        $this->role = $role;
    }
}
