<?php

namespace Engine\Core\Database;

use ReflectionClass;
use ReflectionProperty;

/**
 * Микротрейт ActiveRecord.
 * Исправления:
 *  - убран `global $di`: сущность принимает Connection явно;
 *  - save() больше не возвращает lastInsertId при UPDATE и не «печатает» ошибку
 *    клиенту (утечка internals); исключения идут в ErrorHandler;
 *  - setId приводит к int (защита от протаскивания мусора в WHERE id).
 */
trait ActiveRecord
{
    /** @var Connection */
    protected $db;
    /** @var QueryBuilder */
    protected $queryBuilder;

    public function __construct(Connection $db = null, $id = 0)
    {
        $this->db           = $db;
        $this->queryBuilder = new QueryBuilder();

        if ($id) {
            $this->setId((int)$id);
        }
    }

    public function getTable()
    {
        return $this->table;
    }

    /**
     * Сохранить сущность (INSERT без id / UPDATE с id).
     * @return string|int id вставленной строки либо число обновлённых строк
     */
    public function save()
    {
        $properties = $this->getIssetProperties();
        if (isset($properties['id'])) {
            unset($properties['id']); // id не обновляется через SET
        }
        if (empty($properties)) {
            return 0;
        }

        if (isset($this->id)) {
            $sql = $this->queryBuilder
                ->update($this->getTable())
                ->set($properties)
                ->where('id', (int)$this->id)
                ->sql();
            return $this->db->execute($sql, $this->queryBuilder->values);
        }

        $sql = $this->queryBuilder
            ->insert($this->getTable())
            ->set($properties)
            ->sql();
        $this->db->execute($sql, $this->queryBuilder->values);
        return $this->db->lastInsertId();
    }

    /** Найти сущность по id; null если не найдена */
    public static function find(Connection $db, $id)
    {
        $id = (int)$id;
        if (!$id) {
            return null;
        }
        $instance = new static($db);
        $qb = new QueryBuilder();
        $row = $db->fetchOne(
            $qb->select()->from($instance->getTable())->where('id', $id)->limit(1)->sql(),
            $qb->values
        );
        if (!$row) {
            return null;
        }
        foreach ($row as $key => $value) {
            $instance->{$key} = $value;
        }
        return $instance;
    }

    private function getIssetProperties()
    {
        $properties = [];
        foreach ($this->getProperties() as $property) {
            $name = $property->getName();
            if (isset($this->{$name})) {
                $properties[$name] = $this->{$name};
            }
        }
        return $properties;
    }

    /** @return ReflectionProperty[] */
    private function getProperties()
    {
        $reflection = new ReflectionClass($this);
        return $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
    }
}
