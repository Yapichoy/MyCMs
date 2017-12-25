<?php
/**
 * Created by PhpStorm.
 * User: Doctor
 * Date: 25.12.2017
 * Time: 18:05
 */

namespace Admin\Model\User;
use Engine\Model;

class UserRepository extends  Model{
    public  function getUsers(){
        $sql = $this-> qBuilder
            ->select()
            ->from('user')
            ->orderBy('id','DESC')
            ->sql();

        return $this->db->query($sql);
    }
    public function test(){
        $user = new User(1);
        $user->setEmail("admin@admin.ru");
        $user->save();
    }
}