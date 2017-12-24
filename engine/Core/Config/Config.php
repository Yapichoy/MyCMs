<?php
/**
 * Created by PhpStorm.
 * User: Doctor
 * Date: 23.12.2017
 * Time: 21:30
 */

namespace Engine\Core\Config;


class Config {
    public static function item($key, $group='main'){
        $groupItems = static::file($group);
        return isset($groupItems[$key])? $groupItems[$key]:null;
    }
    public static function file($group){
        $path = $_SERVER['DOCUMENT_ROOT'].'/'.mb_strtolower(ENV).'/Config/'.$group.'.php';
        if(file_exists($path)){
            $items = require $path;
            if(!empty($items)){
                return $items;
            }
            else throw new \Exception(sprintf('%s',$path));
        }
        else throw new \Exception(sprintf(
            'Can`t see fail %s',$path));
        return false;
    }
}