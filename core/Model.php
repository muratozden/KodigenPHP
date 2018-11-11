<?php namespace KodigenPHP;
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
class Model
{
    public $table;
    public $primary = "id";
    public $fetch_as = \PDO::FETCH_ASSOC;

    private $db;
    private $escape;
    private $table_escaped;

    private $select;

    function __construct(\KodigenPHP\Database &$db = null)
    {
        if ($db === null) {
            $this->db = \KodigenPHP\Database::getInstance();
        } else {
            $this->db = &$db;
        }

        $this->escape = $this->db->provider->escape;
        $this->table_escaped = "{$this->escape}{$this->table}{$this->escape}";
    }

    public function select($select) {
        $this->select = $select;
        return $this;
    }

    public function get($where = null)
    {
        list($data, $where_str, $settings_str, $select_str) = $this->prepareData($where, null, $this->select);
        $this->resetBuilder();

        $query = $this->db->prepare("SELECT {$select_str} FROM {$this->table_escaped} {$where_str} LIMIT 1");
        return $query->execute($data) ? $query->fetch($this->fetch_as) : false;
    }

    public function getAll($where = null)
    {
        list($data, $where_str, $settings_str, $select_str) = $this->prepareData($where, null, $this->select);
        $this->resetBuilder();

        $query = $this->db->prepare("SELECT {$select_str} FROM {$this->table_escaped} {$where_str}");
        return $query->execute($data) ? $query->fetchAll($this->fetch_as) : false;
    }

    public function insert(array $data): ?int
    {
        $columns = $values = $comma = "";

        foreach ($data as $key => $val) {
            $columns .= "{$comma}{$this->escape}{$key}{$this->escape}";
            $values .= "{$comma}:{$key}";
            $comma = ", ";
        }

        $query = $this->db->prepare("INSERT INTO {$this->table_escaped} ({$columns}) VALUES ({$values})");
        if ($query->execute($data)) {
            return $this->db->lastInsertId();
        } else {
            return null;
        }
    }

    public function update(array $where, array $settings = []): bool
    {
        list($data, $where_str, $settings_str) = $this->prepareData($where, $settings);

        $query = $this->db->prepare("UPDATE {$this->table_escaped} {$settings_str} {$where_str}");
        return $query->execute($data);
    }

    public function delete($where): bool
    {
        list($data, $where_str) = $this->prepareData($where);

        $query = $this->db->prepare("DELETE FROM {$this->table_escaped} {$where_str}");
        return $query->execute($data);
    }

    public function resetBuilder() {
        $this->select = "*";
        $this->where = null;
    }

    private function prepareData($where, $settings = [], $select = []): ?array
    {
        $where_str = $settings_str = $select_str = "";
        $data = [];

        if (is_array($select)) {
            $comma = "";
            foreach ($select as $col) {
                $select_str .= "{$comma}{$this->escape}{$col}{$this->escape}";
                $comma = ", ";
            }
        } else {
            $select_str = $select;
        }

        if (is_array($settings)) {
            $settings_str = "SET ";
            $comma = "";
            foreach ($settings as $col => $val) {
                $settings_str .= "{$comma}{$this->escape}{$col}{$this->escape}=:set_{$col}";
                $data["set_{$col}"] = $val;
                $comma = ", ";
            }
        }

        if (is_array($where)) {
            $where_str = "WHERE ";
            $comma = "";
            $where = is_numeric($where) ? ["id" => $where] : $where;
            foreach ($where as $col => $val) {
                $where_str .= "{$comma}{$this->escape}{$col}{$this->escape}=:exp_{$col}";
                $data["exp_{$col}"] = $val;
                $comma = ", ";
            }
        }

        return [$data, $where_str, $settings_str, $select_str];
    }
}