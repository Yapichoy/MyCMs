<?php
$this->router->add('login', '/admin/login', 'LoginController:form');
$this->router->add('admin', '/admin/', 'DashboardController:index');
$this->router->add('admin-auth', '/admin/auth/', 'LoginController:auth','POST');
$this->router->add('admin-logout', '/admin/logout/', 'AdminController:logout');
$this->router->add('pages', '/admin/pages/', 'PageController:listing');
$this->router->add('page-create', '/admin/pages/create/', 'PageController:create');
$this->router->add('page-add', '/admin/page/add/', 'PageController:add','POST');