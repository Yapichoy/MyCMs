<?php

namespace Admin\Controller;
use Engine\Controller;
use Engine\Di\Di;

class AdminController extends Controller{
    /**
     * @var Di
     */
    protected $view;

    public function __construct(Di $di){

        parent::__construct($di);
        $this->view = $this->di->get('view');
    }
}