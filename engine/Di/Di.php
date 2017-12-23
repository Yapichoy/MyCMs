<?php

namespace Engine\Di;


class Di {
    private $container = [];

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function set($key,$value){
        /** @var TYPE_NAME $container */
        $this->container[$key] = $value;
        return $this;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key){
        if(isset($this->container[$key]))
            return $this->container[$key];
        return null;
    }
}