<?php

namespace Admin\Model\User;

use Engine\Model;

/**
 * Репозиторий пользователей.
 * Исправления: убран метод test() с деструктивным сайд-эффектом (его вызывал
 * DashboardController при каждом заходе на главную админки — подменялся email
 * пользователя #1).
 */
class UserRepository extends Model
{
    public function getUsers()
    {
        $sql = $this->qBuilder
            ->select('id, email, role, created_at') // без хешей паролей!
            ->from('user')
            ->orderBy('id', 'DESC')
            ->sql();
        return $this->db->query($sql, $this->qBuilder->values);
    }

    /** Найти пользователя по email (для аутентификации) */
    public function findByEmail($email)
    {
        $qb = $this->newQueryBuilder();
        return $this->db->fetchOne(
            $qb->select()->from('user')->where('email', $email)->limit(1)->sql(),
            $qb->values
        );
    }

    /** Обновить хеш пароля (ротация алгоритма при успешном логине) */
    public function updatePasswordHash(int $userId, string $hash): void
    {
        $qb = $this->newQueryBuilder();
        $this->db->execute(
            $qb->update('user')->set(['password_hash' => $hash])->where('id', $userId)->sql(),
            $qb->values
        );
    }
}
