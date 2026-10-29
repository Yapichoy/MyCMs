<?php
/**
 * Точка входа публичной части CMS.
 * Исправлен синтаксический баг: после require_once стояла «висячая» `{`.
 */
define('ROOT_PROJECT', dirname(__DIR__));
define('ROOT_DIR', __DIR__);
define('ENV', 'CMS');
require_once __DIR__ . '/engine/bootstrap.php';
