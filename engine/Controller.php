<?php
/**
 * Created by PhpStorm.
 * User: Doctor
 * Date: 19.12.2017
 * Time: 19:24
 */

namespace Engine;


abstract class Controller {
    /**
     * @param Di\Di $di
     */
    protected  $di;
    protected $db;
    protected $view;
    protected $config;
    protected $request;
    public function __construct(\Engine\Di\Di $di){
        $this->di = $di;
        $this->view = $this->di->get('view');
        $this->db = $this->di->get('db');
        $this->config = $this->di->get('config');
        $this->request = $this->di->get('request');
    }
}