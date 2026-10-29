<?php
/**
 * Маршруты админ-панели.
 *
 * @var \Engine\Core\Router\Router $router
 */
$router->add('login', '/admin/login', 'LoginController:form');
$router->add('admin', '/admin/', 'DashboardController:index');
$router->add('admin-auth', '/admin/auth/', 'LoginController:auth', 'POST');
$router->add('admin-logout', '/admin/logout/', 'AdminController:logout');
$router->add('pages', '/admin/pages/', 'PageController:listing');
$router->add('page-create', '/admin/pages/create/', 'PageController:create');
$router->add('page-add', '/admin/page/add/', 'PageController:add', 'POST');
