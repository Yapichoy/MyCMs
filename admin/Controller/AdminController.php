<?php

namespace Admin\Controller;
use Engine\Controller;
use Engine\Di\Di;
use Engine\Core\Auth\Auth;
class AdminController extends Controller{
    /**
     * @var Di
     */
    protected $auth;

    public function __construct(Di $di){

        parent::__construct($di);

        $this->auth = new Auth();
        if($this->auth->hashUser()==null){
            header("Location: /admin/login");
            exit;
        }

    }
    public function logout(){
        $this->auth->unAuthorize();
        header("Location: /admin/login/");
        exit;
    }
}