<?php
/**
 * Created by PhpStorm.
 * User: Doctor
 * Date: 26.12.2017
 * Time: 17:59
 */

namespace Admin\Model\Page;


use Engine\Model;

class PageRepository extends Model{
    public function getPages(){
        $sql = $this->qBuilder->select()
            ->from('page')
            ->orderBy('id','DESC')
            ->sql();
        return $this->db->query($sql);
    }
    public function createPage($params){
        //print_r($params);
        $page = new Page();
        $page->setTitle($params['title']);
        $page->setContent($params['content']);
        $pageId = $page->save();
        return $pageId;
    }
}