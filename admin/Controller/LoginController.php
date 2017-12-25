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
use Engine\Core\Database\QueryBuilder;

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
        $qBuilder = new QueryBuilder();
        $sql = $qBuilder
            ->select()
            ->from('user')
            ->where('email',$params['email'])
            ->where('password',$params['password'])
            ->limit(1)->sql();
        $query = $this->db->query($sql,$qBuilder->values);
        if(!empty($query)){
            $user = $query[0];
            if($user['role'] == 'admin'){
                $hash = md5($user['email'].$user['password'].$this->auth->salt());
               // $hash = $user['hash'];
                $sql = $qBuilder
                    ->update('user')
                    ->set(['hash'=> $hash])
                    ->where('id',$user['id'])->sql();
                $this->db->execute($sql,$qBuilder->values);
                $this->auth->authorize($hash);
                header('Location: /admin/',true,301);
                exit;
            }
        }
        header('Location: /admin/login',true,301);
        exit;
    }
}