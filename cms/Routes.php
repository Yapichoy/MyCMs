<?php
/**
 * Маршруты публичной части.
 * Переменная $router внедряется ядром Engine\Cms::run() (ранее использовалась
 * хрупкая магия $this из require внутри метода).
 *
 * @var \Engine\Core\Router\Router $router
 */
$router->add('home', '/', 'HomeController:index');
$router->add('news', '/news/{id:int}', 'HomeController:news');
