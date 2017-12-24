<?php
$this->router->add('login', '/admin/login', 'LoginController:form');
$this->router->add('admin', '/admin', 'DashboardController:index');