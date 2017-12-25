<?php
/**
 * Created by PhpStorm.
 * User: Doctor
 * Date: 25.12.2017
 * Time: 18:12
 */

namespace Engine;


use Engine\Di\Di;
use Engine\Core\Database\QueryBuilder;
abstract class Model {
    protected  $di;
    protected $db;
    protected $config;
    protected $qBuilder;
    public function __construct(Di $di){
        $this->di = $di;
        $this->db = $this->di->get('db');
        $this->config = $this->di->get('config');
        $this->qBuilder = new QueryBuilder();
    }
}