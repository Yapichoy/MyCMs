<?php
/**
 * Created by PhpStorm.
 * User: Doctor
 * Date: 17.12.2017
 * Time: 19:09
 */

namespace Engine\Core\Database;
use \PDO;
use Engine\Core\Config\Config;

class Connection {
    private $link;
    public function __construct(){
        $this->connect();
    }
    public function connect(){
        $config = Config::file('database');

       $dsn = 'mysql:host='.$config["host"].';dbname='.$config['db_name'].';charset='.$config['charset'];
        $this->link = new PDO($dsn,$config['user'],$config['password']);
        return $this;
    }
    public function execute($sql,$values =[]){
        //echo $sql;
        $sth = $this->link->prepare($sql);
        return $sth->execute($values);
    }
    public function query($sql, $values=[])
    {

        $sth = $this->link->prepare($sql);
        $sth -> execute($values);
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        if($result == false)return [];
        return $result;
    }
    public function lastInsertId(){
        return $this->link->lastInsertId();
    }
}