<?php

namespace Engine;

use Engine\Di\Di;
use Engine\Core\Database\QueryBuilder;

/**
 * Базовый репозиторий.
 * Доработано: newQueryBuilder() — отдельный чистый построитель для параллельных
 * запросов (прежний общий $qBuilder затирался при повторном использовании в
 * рамках одного контроллера).
 */
abstract class Model
{
    /** @var Di */
    protected $di;
    /** @var \Engine\Core\Database\Connection */
    protected $db;
    protected $config;
    /** @var QueryBuilder */
    protected $qBuilder;

    public function __construct(Di $di)
    {
        $this->di       = $di;
        $this->db       = $di->get('db');
        $this->config   = $di->get('config');
        $this->qBuilder = new QueryBuilder();
    }

    protected function newQueryBuilder(): QueryBuilder
    {
        return new QueryBuilder();
    }
}
