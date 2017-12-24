<?php
/**
 * Created by PhpStorm.
 * User: Doctor
 * Date: 24.12.2017
 * Time: 11:59
 */

namespace Engine\Helper;


class Cookie {
    /**
     * @param $key
     * @param $value
     * @param int $time
     */
    public static function set($key,$value,$time=31536000){
        setcookie($key,$value,time()+$time,'/');
    }
    public static function get($key){
        if(isset($_COOKIE[$key])){
            return $_COOKIE[$key];
        }
        return null;
    }
    public static function delete($key){

        if(isset($_COOKIE[$key])){
            self::set($key,'',-3600);
            unset($_COOKIE[$key]);
            //print $_COOKIE[$key];
        }
    }
}