<?php
/**
 * Created by PhpStorm.
 * User: Doctor
 * Date: 24.12.2017
 * Time: 13:04
 */
namespace Engine\Core\Request;
class Request {
    public $get = [];

    public $post = [];

    public $request = [];

    public $cookie = [];

    public $file = [];

    public $server = [];

    public function __construct(){
        $this -> get     = $_GET;
        $this -> post    = $_POST;
        $this -> request = $_REQUEST;
        $this -> cookie  = $_COOKIE;
        $this -> file    = $_FILES;
        $this -> server  = $_SERVER;
    }

}