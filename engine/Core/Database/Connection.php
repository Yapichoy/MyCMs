<?php

namespace Engine\Core\Database;

use PDO;
use Engine\Core\Config\Config;

/**
 * PDO-обёртка.
 * Исправления:
 *  - ERRMODE_EXCEPTION вместо тихих false (ранее ошибки SQL не всплывали);
 *  - отключены emulate prepares — плейсхолдеры реально параметризуются драйвером;
 *  - DSN включает порт из конфига (ранее port игнорировался);
 *  - добавлен fetchOne() для типового «получить одну строку».
 */
class Connection
{
    /** @var PDO */
    private $link;

    public function __construct()
    {
        $this->connect();
    }

    /** @return $this */
    public function connect()
    {
        $config = Config::file('database');

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $config['host'],
            $config['port'] ?? 3306,
            $config['db_name'],
            $config['charset'] ?? 'utf8mb4'
        );

        $this->link = new PDO(
            $dsn,
            $config['user'],
            $config['password'],
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
        return $this;
    }

    /**
     * Выполнить запрос с привязкой параметров. Возвращает число затронутых строк.
     */
    public function execute($sql, array $values = [])
    {
        $sth = $this->link->prepare($sql);
        $sth->execute($values);
        return $sth->rowCount();
    }

    /** @return array[] все строки (массив ассоциативных массивов) */
    public function query($sql, array $values = [])
    {
        $sth = $this->link->prepare($sql);
        $sth->execute($values);
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $result === false ? [] : $result;
    }

    /** @return array|null первая строка или null */
    public function fetchOne($sql, array $values = [])
    {
        $rows = $this->query($sql, $values);
        return $rows ? $rows[0] : null;
    }

    public function lastInsertId()
    {
        return $this->link->lastInsertId();
    }

    /** @return PDO */
    public function pdo()
    {
        return $this->link;
    }
}
