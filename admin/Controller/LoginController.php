<?php
/**
 * Created by PhpStorm.
 * User: Doctor
 * Date: 23.12.2017
 * Time: 18:58
 */

namespace Admin\Controller;
use Engine\Controller;
use Engine\Core\Auth\Auth;

class LoginController extends Controller{

    public function __construct($di){
        parent::__construct($di);
        $this->auth = new Auth();
        if($this->auth->hashUser()!=null) {
            $this->auth->authorize($this->auth->hashUser());
            header("Location: /admin/");
            exit;
        }
    }
    public function form(){

       $this->view->render('login');
    }
    public function auth(){
        $params = $this->request->post;
        $query = $this->db->query('SELECT * FROM `user` WHERE email="'.$params['email'].'" AND password="'.$params['password'].'"');
        if(!empty($query)){
            $user = $query[0];
            if($user['role'] == 'admin'){
                $hash = md5($user['email'].$user['password'].$this->auth->salt());
                $this->db->execute('UPDATE `user` SET hash = "'.$hash.'" WHERE id = '.$user['id']);
                $this->auth->authorize($hash);
                header('Location: /admin/',true,301);
                exit;
            }
        }

    }
}