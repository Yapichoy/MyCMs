<?php

namespace Admin\Controller;

use Engine\Security\Csrf;

/**
 * Управление страницами.
 * Исправления:
 *  - add() был без CSRF и печатал print_r($pageId) вместо редиректа;
 *  - валидация ошибок показывается пользователю, а не утечкой stack-trace.
 */
class PageController extends AdminController
{
    public function listing()
    {
        $tableModel = $this->load->model('page');
        $pages = $tableModel->repository->getPages();
        $this->view->render('pages/list', ['pages' => $pages]);
    }

    public function create()
    {
        $this->view->render('pages/create', ['error' => null]);
    }

    public function add()
    {
        if (!$this->request->isPost()) {
            $this->redirect('/admin/pages/create');
        }
        Csrf::verifyRequest($this->request->post(null) ?: []);

        $params = $this->request->post(null) ?: [];
        $pageModel = $this->load->model('page');

        try {
            $pageModel->repository->createPage($params);
        } catch (\InvalidArgumentException $e) {
            // пользовательская ошибка валидации — возвращаем на форму
            $this->view->render('pages/create', ['error' => $e->getMessage()]);
            return;
        }

        $this->redirect('/admin/pages/');
    }

    public function view($id)
    {
        $pageModel = $this->load->model('page');
        $page = $pageModel->repository->getById((int)$id);
        if ($page === null) {
            $this->redirect('/admin/pages/');
        }
        $this->view->render('pages/view', ['page' => $page]);
    }
}
