<?php
/**
 * Created by PhpStorm.
 * User: Doctor
 * Date: 23.12.2017
 * Time: 18:51
 */

namespace Admin\Controller;


class PageController extends AdminController {
    public function listing(){
        $tableModel = $this->load->model('page');
        $pages = $tableModel->repository->getPages();
        //print_r($data);
        $this->view->render('pages/list',['pages'=>$pages]);
    }
    public function create(){
        $pageModel = $this->load->model('Page');

        $this->view->render('pages/create');
    }
    public function add(){
       $params = $this->request->post;
        $pageModel = $this->load->model('page');
        if(isset($params['title'])) {
            $pageId = $pageModel->repository->createPage($params);
        }
        print_r($pageId);
    }
}