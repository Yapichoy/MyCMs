<?php
/**
 * Created by PhpStorm.
 * User: Doctor
 * Date: 23.12.2017
 * Time: 18:58
 */

namespace Admin\Controller;
use Admin\Model\User\UserRepository;

class DashboardController extends AdminController{


    public function index(){
        $userModel = $this->load->model('user');
        $userModel->repository->test();
        $this->view->render('dashboard');
    }
}