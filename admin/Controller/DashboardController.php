<?php
/**
 * Created by PhpStorm.
 * User: Doctor
 * Date: 23.12.2017
 * Time: 18:58
 */

namespace Admin\Controller;


class DashboardController extends AdminController{


    public function index(){
        // print_r($this->di->get('config'));
        $this->view->render('dashboard');
    }
}