<?php namespace KodigenPHP\Database;
/**
 * KodigenPHP - The open-source application development framework.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package KodigenPHP
 * @author Ekin Karadeniz <ekin@kodigen.com>
 * @copyright 2018-2019 Kodigen
 * @license Apache License 2.0
 */
class QueryBuilder
{
    const TYPE_SELECT = 1;
    const TYPE_INSERT = 2;
    const TYPE_UPDATE = 3;
    const TYPE_DELETE = 4;

    private $escape;

    private $builder_type;
    private $builder_select;
    private $builder_set;
    private $builder_table;
    private $builder_where;
    private $builder_from;
    private $builder_join;
    private $builder_limit;
    private $builder_offset;
    private $builder_prepare_data = [];

    function __construct()
    {
        $this->escape = $this->db->provider->escape ?? '`';
    }

    public function resetBuilder()
    {
        $this->builder_type = 0;
        $this->builder_select = null;
        $this->builder_set = null;
        $this->builder_table = null;
        $this->builder_where = null;
        $this->builder_from = null;
        $this->builder_join = null;
        $this->builder_limit = null;
        $this->builder_offset = null;
        $this->builder_prepare_data = [];
    }

    public function escapeKey(?string $key): string
    {
        return $this->escape . $key . $this->escape;
    }

    public function select($select)
    {
        $this->builder_select = $this->seperateColumns($select);
        return $this;
    }

    public function where($where, $type = "AND")
    {
        if (is_numeric($where)) {
            $where = ["id" => $where];
        }
        $this->builder_where = "WHERE " . $this->assignedValue($where, "exp_", " {$type} ") . " ";
        return $this;
    }

    public function limit(int $limit)
    {
        $this->builder_limit = "LIMIT {$limit} ";
        return $this;
    }

    public function offset(int $offset)
    {
        $this->builder_offset = "OFFSET {$offset} ";
        return $this;
    }

    public function set($set)
    {
        $this->builder_set = $this->assignedValue($set, "set_") . " ";
        return $this;
    }

    public function from($table)
    {
        $this->builder_table = $this->escapeKey($table) . " ";
        return $this;
    }

    public function join($table, $cond, $type = "LEFT")
    {
        $this->builder_join = "{$type} JOIN " . $this->escapeKey($table) . " ON {$cond} ";
        return $this;
    }

    public function seperateColumns($columns): string
    {
        $temp = "";
        if (is_array($columns)) {
            $comma = "";
            foreach ($columns as $col) {
                $temp .= $comma . $this->escapeKey($col);
                $comma = ", ";
            }
        } else {
            $temp = $columns;
        }
        return $temp;
    }

    public function getPrepareData(): array
    {
        return $this->builder_prepare_data;
    }

    public function assignedValue(array $assigns, string $key = "", string $seperator = ", "): string
    {
        $temp = $comma = "";
        foreach ($assigns as $col => $val) {
            $temp .= "{$comma}{$this->escape}{$col}{$this->escape}=:{$key}{$col}";
            $comma = $seperator;
            $this->builder_prepare_data["{$key}{$col}"] = $val;
        }
        return $temp;
    }

    public function getQuery(int $type, $table)
    {
        $query = "";

        switch ($type) {
            case self::TYPE_SELECT:
                $query .= "SELECT " . ($this->builder_select ? $this->builder_select : "*") . " ";
                $query .= "FROM " . ($this->builder_table ? $this->builder_table : $this->escapeKey($table)) . " ";
                $query .= $this->builder_where ? $this->builder_where : "";
                $query .= $this->builder_join ? $this->builder_join : "";
                $query .= $this->builder_limit ? $this->builder_limit : "";
                $query .= $this->builder_offset ? $this->builder_offset : "";
                break;
            case self::TYPE_UPDATE:
                $query .= "UPDATE " . ($this->builder_table ? $this->builder_table : $this->escapeKey($table));
                $query .= " SET {$this->builder_set}";
                $query .= $this->builder_where ? $this->builder_where : "";
                $query .= $this->builder_limit ? $this->builder_limit : "";
                $query .= $this->builder_offset ? $this->builder_offset : "";
                break;
        }

        return $query;
    }
}