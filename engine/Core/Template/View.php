<?php

namespace Engine\Core\Template;

/**
 * Рендеринг шаблонов.
 * Исправления:
 *  - require_once -> require (повторный рендер одного шаблона теперь работает;
 *    require_once молча возвращала true при повторном рендере того же файла);
 *  - сравнение ENV приведено к нижнему регистру;
 *  - добавлен e() — экранирование вывода против XSS (используется в шаблонах).
 */
class View
{
    /** @var Theme */
    protected $theme;

    public function __construct()
    {
        $this->theme = new Theme();
    }

    public function render($template, array $vars = [])
    {
        $templatePath = $this->getTemplatePath($template, strtolower(ENV));
        if (!is_file($templatePath)) {
            throw new \InvalidArgumentException(sprintf('Template "%s" not found in "%s"', $template, $templatePath));
        }

        $this->theme->setData($vars);
        extract($vars, EXTR_SKIP);

        ob_start();
        try {
            require $templatePath;
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
        echo (string)ob_get_clean();
    }

    /** @var Theme */
    public function theme()
    {
        return $this->theme;
    }

    private function getTemplatePath($template, $env = null)
    {
        if ($env === 'cms') {
            return ROOT_DIR . '/content/themes/default/' . $template . '.php';
        }
        return ROOT_DIR . '/View/' . $template . '.php';
    }

    /** Экранирование для шаблонов: <?= $view::e($x) ?> или e($x) через import. */
    public static function e($value)
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
