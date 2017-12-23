<?php


namespace Engine\Service;


abstract class AbstractProvider {
    protected $di;

    /**
     * @param \Engine\Di\Di $di
     */
    public function __construct(\Engine\Di\Di $di){
        $this->di = $di;
    }

    /**
     * @return mixed
     */
    abstract function init();
}