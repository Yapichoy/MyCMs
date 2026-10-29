<?php

namespace Admin\Controller;

use Admin\Model\User\User;
use Engine\Controller;
use Engine\Core\Auth\Auth;
use Engine\Di\Di;
use Engine\Security\Csrf;

/**
 * Вход в админку.
 * Исправления безопасности:
 *  - НЕ наследуется от AdminController (иначе получали редирект-цикл login→login);
 *  - пароль больше не участвует в WHERE — сравнивается через password_verify();
 *  - CSRF-проверка POST; троттлинг неудачных попыток (brute force);
 *  - одинаковое сообщение об ошибке (нет user enumeration);
 *  - прозрачная ротация хеша при апгрейде алгоритма.
 */
class LoginController extends Controller
{
    const MAX_ATTEMPTS   = 5;
    const LOCK_SECONDS   = 300;

    public function __construct(Di $di)
    {
        parent::__construct($di);
    }

    /** GET /admin/login — форма (или редирект в дашборд, если уже вошёл) */
    public function form()
    {
        if ($this->auth()->authorized()) {
            $this->redirect('/admin/');
        }
        $this->view->render('login', [
            'error' => $this->sessionFlash('login_error'),
        ]);
    }

    /** POST /admin/auth — обработка учётных данных */
    public function auth()
    {
        if (!$this->request->isPost()) {
            $this->redirect('/admin/login');
        }

        Csrf::verifyRequest($this->request->post(null) ?: []);

        if ($this->isLockedOut()) {
            $this->flash('login_error', 'Слишком много попыток. Подождите несколько минут.');
            $this->redirect('/admin/login');
        }

        $email    = trim((string)$this->request->post('email', ''));
        $password = (string)$this->request->post('password', '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '' || mb_strlen($password) > 1024) {
            $this->registerFailedAttempt();
            $this->flash('login_error', 'Неверный email или пароль.');
            $this->redirect('/admin/login');
        }

        $userModel = $this->load->model('user');
        $userRow   = $userModel->repository->findByEmail($email);

        $hash = $userRow['password_hash'] ?? null;
        // фиктивное сравнение при отсутствии пользователя — против timing/user-enumeration
        $ok = password_verify($password, $hash ?? '$2y$10$invalidinvalidinvalidinvalidinvalidinvalida');

        if (!$userRow || !$ok) {
            $this->registerFailedAttempt();
            $this->flash('login_error', 'Неверный email или пароль.');
            $this->redirect('/admin/login');
        }

        // прозрачная модернизация хеша, если поменялся алгоритм/стоимость
        if (Auth::needsRehash($hash)) {
            $userModel->repository->updatePasswordHash((int)$userRow['id'], Auth::hashPassword($password));
        }

        $this->resetAttempts();
        $this->auth()->login((int)$userRow['id'], (string)($userRow['role'] ?? 'editor'));
        $this->redirect('/admin/');
    }

    /* ---------- троттлинг на уровне сессии ---------- */

    private function isLockedOut(): bool
    {
        $a = $_SESSION['_login_attempts'] ?? null;
        if (!$a) {
            return false;
        }
        if (($a['count'] ?? 0) >= self::MAX_ATTEMPTS) {
            if (time() - ($a['last'] ?? 0) < self::LOCK_SECONDS) {
                return true;
            }
            unset($_SESSION['_login_attempts']);
        }
        return false;
    }

    private function registerFailedAttempt(): void
    {
        $a = $_SESSION['_login_attempts'] ?? ['count' => 0, 'last' => 0];
        $a['count']++;
        $a['last'] = time();
        $_SESSION['_login_attempts'] = $a;
    }

    private function resetAttempts(): void
    {
        unset($_SESSION['_login_attempts']);
    }

    private function flash(string $key, string $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    private function sessionFlash(string $key): ?string
    {
        $v = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $v;
    }
}
