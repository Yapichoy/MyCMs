<?php
/**
 * Created by PhpStorm.
 * User: Doctor
 * Date: 23.12.2017
 * Time: 18:30
 */

namespace Admin\Controller;


class ErrorController extends AdminController{

    public function page404(){
        echo '404 '.ENV;
    }
}