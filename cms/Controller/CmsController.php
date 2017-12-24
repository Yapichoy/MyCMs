<?php
/**
 * Created by PhpStorm.
 * User: Doctor
 * Date: 21.12.2017
 * Time: 22:22
 */

namespace CMS\Controller;
use Engine\Controller;
use Engine\Di\Di;

class CmsController extends Controller{
    /**
     * @var Di
     */


    public function __construct(Di $di){

        parent::__construct($di);

    }
}