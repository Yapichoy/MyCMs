<?php
/**
 * Created by PhpStorm.
 * User: Doctor
 * Date: 23.12.2017
 * Time: 18:58
 */

namespace Admin\Controller;


class LoginController extends AdminController{

    public function form(){
       $this->view->render('login');
    }
}