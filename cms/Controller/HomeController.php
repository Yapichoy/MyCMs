<?php
/**
 * Created by PhpStorm.
 * User: Doctor
 * Date: 21.12.2017
 * Time: 19:50
 */

namespace CMS\Controller;

class HomeController extends CmsController{

    public function index(){
        $this->view->render('index',['name'=>'Vlad', 'family'=>'Kuch']);
    }
    public function news($id){
        //echo 'news' . $id;

    }
}