<?php

namespace Admin\Model\Page;

use Engine\Core\Database\ActiveRecord;

/**
 * Сущность страницы. Поле `slug` добавлено для ЧПУ-ссылк (модель + схема).
 */
class Page
{
    use ActiveRecord;

    protected $table = 'page';

    public $id;
    public $title;
    public $content;
    public $slug;
    public $date;

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = (int)$id; }

    public function getTitle() { return $this->title; }
    public function setTitle($title) { $this->title = trim((string)$title); }

    public function getContent() { return $this->content; }
    public function setContent($content) { $this->content = $content; }

    public function getDate() { return $this->date; }
    public function setDate($date) { $this->date = $date; }

    public function getSlug() { return $this->slug; }

    /** Генерируем безопасный slug из заголовка (не из пользовательского ввода как есть) */
    public function generateSlug(string $source): void
    {
        $map = ['а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'zh','з'=>'z',
                'и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r',
                'с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'sch',
                'ъ'=>'','ы'=>'y','ь'=>'','э'=>'e','ю'=>'yu','я'=>'ya'];
        $s = strtolower($source);
        $s = strtr($s, $map);
        $s = preg_replace('/[^a-z0-9]+/', '-', $s);
        $s = trim($s, '-');
        $this->slug = substr($s !== '' ? $s : 'page', 0, 100);
    }
}
