<?php
/**
 * Created by PhpStorm.
 * User: Doctor
 * Date: 20.12.2017
 * Time: 18:46
 */

namespace Engine\Helper;


class Common {
    public function isPost(){
        return ($_SERVER['REQUEST_METHOD'] == 'POST');
    }
    public function getMethod(){
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @return string
     */
    public function getPathUrl(){
        $pathUrl = $_SERVER['REQUEST_URI'];
        if($position = strpos($pathUrl,'?')){
            $pathUrl = substr($pathUrl,0,$position);
        }
        return $pathUrl;
    }
}