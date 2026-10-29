<?php

namespace Admin\Model\Page;

use Engine\Model;

class PageRepository extends Model
{
    public function getPages()
    {
        $sql = $this->qBuilder
            ->select('id, title, slug, date') // content не тянем в листинг
            ->from('page')
            ->orderBy('id', 'DESC')
            ->sql();
        return $this->db->query($sql, $this->qBuilder->values);
    }

    /** @return Page|null */
    public function getById($id)
    {
        return Page::find($this->db, (int)$id);
    }

    /**
     * Создать страницу.
     * Исправления: валидация входных данных, лимиты длины, дата ставится сервером
     * (раньше $params['date'] из POST уходил в БД как есть — и поля date в форме не было).
     */
    public function createPage(array $params)
    {
        $title   = trim((string)($params['title'] ?? ''));
        $content = (string)($params['content'] ?? '');

        if ($title === '' || mb_strlen($title) > 255) {
            throw new \InvalidArgumentException('Title is required and must be <= 255 chars');
        }
        if (mb_strlen($content) > 65535) {
            throw new \InvalidArgumentException('Content too long');
        }

        $page = new Page($this->db);
        $page->setTitle($title);
        $page->setContent($content);
        $page->generateSlug($title);
        $page->setDate(date('Y-m-d H:i:s'));

        return $page->save(); // id вставленной строки
    }
}
