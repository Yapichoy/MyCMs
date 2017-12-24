<?php
/**
 * Created by PhpStorm.
 * User: Doctor
 * Date: 24.12.2017
 * Time: 12:03
 */

namespace Engine\Core\Auth;
use Engine\Helper\Cookie;

class Auth implements AuthInterface {
    protected $authorized = false;
    protected $hash_user;
    public function autorized(){
        return $this->authorized;
    }
    public function hashUser(){
        return Cookie::get('auth_user');
    }
    public function authorize($hash_user){
        Cookie::set('auth_authorized',true);
        Cookie::set('auth_user',$hash_user);

    }
    public function unAuthorize(){
        Cookie::delete('auth_authorized');
        Cookie::delete('auth_user');
        setcookie('auth_authorized','',-3600,'/');
        setcookie('auth_user','',-3600,'/');
        unset($_COOKIE['auth_authorized']);
        unset($_COOKIE['auth_user']);
        print $_COOKIE['auth_authorized'];

    }
    public static function salt(){
        return (string) rand(10000000,99999999);
    }
    public static function encryptPassword($password, $salt = ''){
        return hash('sha256',$password,$salt);
    }
}