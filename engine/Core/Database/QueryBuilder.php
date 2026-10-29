<?php

namespace Engine\Core\Database;

/**
 * Построитель SQL.
 * Исправления безопасности и корректности:
 *  - все идентификаторы (таблицы/колонки/порядок сортировки) проходят whitelist
 *    валидацию `^[A-Za-z_][A-Za-z0-9_]*$` и экранируются бэктиками — закрыты
 *    векторы SQL-инъекций через select()/from()/orderBy()/set();
 *  - секции собираются в фиксированном порядке (SELECT ... FROM ... SET ... WHERE ...
 *    ORDER BY ... LIMIT), а не по порядку ключей массива;
 *  - set() более не конкатенирует по несуществующему ключу и корректно расставляет
 *    запятые (prev/next-магия давала лишние/недостающие разделители);
 *  - limit() принимает только int.
 * Значения по-прежнему передаются позиционными плейсхолдерами (?, bind через PDO).
 */
class QueryBuilder
{
    /** @var array<string, mixed> */
    protected $sql = [];
    /** @var array<int, scalar> */
    public $values = [];

    public function select($fields = '*')
    {
        $this->reset();
        if ($fields === '*') {
            $this->sql['select'] = 'SELECT * ';
        } else {
            $list = array_map([$this, 'fieldRef'], is_array($fields) ? $fields : explode(',', $fields));
            $this->sql['select'] = 'SELECT ' . implode(', ', $list) . ' ';
        }
        return $this;
    }

    public function from($table)
    {
        $this->sql['from'] = 'FROM ' . $this->identifier($table) . ' ';
        return $this;
    }

    public function where($column, $value, $operator = '=')
    {
        $operator = $this->operator($operator);
        $this->sql['where'][] = $this->identifier($column) . " {$operator} ?";
        $this->values[] = $value;
        return $this;
    }

    public function orderBy($field, $order = 'ASC')
    {
        $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
        $this->sql['order_by'] = 'ORDER BY ' . $this->identifier($field) . " {$order} ";
        return $this;
    }

    public function limit($num)
    {
        $this->sql['limit'] = 'LIMIT ' . (int)$num . ' ';
        return $this;
    }

    public function update($table)
    {
        $this->reset();
        $this->sql['update'] = 'UPDATE ' . $this->identifier($table) . ' ';
        return $this;
    }

    public function insert($table)
    {
        $this->reset();
        $this->sql['insert'] = 'INSERT INTO ' . $this->identifier($table) . ' ';
        return $this;
    }

    /**
     * Колонки SET для INSERT/UPDATE. Ключи валидируются, значения — плейсхолдеры.
     */
    public function set(array $data = [])
    {
        $parts = [];
        foreach ($data as $key => $value) {
            $parts[] = $this->identifier($key) . ' = ?';
            $this->values[] = $value;
        }
        if ($parts) {
            $this->sql['set'] = 'SET ' . implode(', ', $parts) . ' ';
        }
        return $this;
    }

    public function sql()
    {
        $out = '';
        // фиксированный порядок SQL-секций
        foreach (['select', 'insert', 'update', 'set', 'from', 'where', 'order_by', 'limit'] as $key) {
            if (!isset($this->sql[$key])) {
                continue;
            }
            if ($key === 'where') {
                $out .= 'WHERE ' . implode(' AND ', $this->sql['where']) . ' ';
            } else {
                $out .= $this->sql[$key];
            }
        }
        return trim($out);
    }

    public function reset()
    {
        $this->sql = [];
        $this->values = [];
        return $this;
    }

    /**
     * Whitelist идентификатор + экранирование бэктиками.
     * @throws \InvalidArgumentException при попытке передать мусор (потенциальная инъекция)
     */
    protected function identifier($name)
    {
        $name = trim((string)$name);
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name)) {
            throw new \InvalidArgumentException(sprintf('Invalid SQL identifier: "%s"', $name));
        }
        return '`' . $name . '`';
    }

    /**
     * Ссылка на поле: `col`, `table.col`, `*`, `table.*` — каждый фрагмент
     * проходит whitelist-валидацию.
     */
    protected function fieldRef($ref)
    {
        $ref = trim((string)$ref);
        if ($ref === '*') {
            return '*';
        }
        if (strpos($ref, '.') !== false) {
            $parts = explode('.', $ref);
            foreach ($parts as $p) {
                if ($p !== '*' && !preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $p)) {
                    throw new \InvalidArgumentException(sprintf('Invalid SQL identifier: \"%s\"', $ref));
                }
            }
            return implode('.', array_map(function ($p) { return $p === '*' ? '*' : '`' . $p . '`'; }, $parts));
        }
        return $this->identifier($ref);
    }

    protected function operator($operator)
    {
        $allowed = ['=', '!=', '<>', '<', '>', '<=', '>=', 'LIKE'];
        $operator = strtoupper(trim((string)$operator));
        if (!in_array($operator, $allowed, true)) {
            throw new \InvalidArgumentException(sprintf('Unsupported operator: "%s"', $operator));
        }
        return $operator;
    }
}
