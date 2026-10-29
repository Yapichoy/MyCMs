<?php

namespace Admin\Controller;

/**
 * Дашборд.
 * Исправлен баг: index() вызывал UserRepository::test(), который при каждом
 * заходе перезаписывал email пользователя #1 на admin@admin.ru. Удалено.
 */
class DashboardController extends AdminController
{
    public function index()
    {
        $this->view->render('dashboard', [
            'user' => $this->auth()->userId(),
            'role' => $this->auth()->role(),
        ]);
    }
}
