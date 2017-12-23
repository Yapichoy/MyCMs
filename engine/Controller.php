<?php
/**
 * Created by PhpStorm.
 * User: Doctor
 * Date: 19.12.2017
 * Time: 19:24
 */

namespace Engine;


abstract class Controller {
    /**
     * @param Di\Di $di
     */
    protected  $di;
    protected $db;
    public function __construct(\Engine\Di\Di $di){
        $this->di = $di;
    }
}