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
    public $db;
    public $table;
    public $primary = "id";
    public $last_sql = "";

    function __construct(\KodigenPHP\Database &$db = null)
    {
        if ($db === null) {
            $this->db = \KodigenPHP\Database::getInstance();
        } else {
            $this->db = &$db;
        }
    }

    public function insert(array $data): ?int
    {
        $columns = $values = $comma = "";

        foreach ($data as $key => $val) {
            $columns .= "{$comma}{$this->db->provider->escape}{$key}{$this->db->provider->escape}";
            $values .= "{$comma}:{$key}";
            $comma = ", ";
        }

        $query = $this->db->prepare("INSERT INTO {$this->db->provider->escape}{$this->table}{$this->db->provider->escape} ({$columns}) VALUES ({$values})");
        if ($query->execute($data)) {
            return $this->db->lastInsertId();
        } else {
            return null;
        }
    }

    public function update($where, array $settings = []): bool
    {
        list($data, $where_str, $settings_str) = $this->prepareData($where, $settings);

        $query = $this->db->prepare("UPDATE {$this->db->provider->escape}{$this->table}{$this->db->provider->escape} {$settings_str} {$where_str}");
        return $query->execute($data);
    }

    public function delete($where): bool
    {
        list($data, $where_str) = $this->prepareData($where);

        $query = $this->db->prepare("DELETE FROM {$this->db->provider->escape}{$this->table}{$this->db->provider->escape} {$where_str}");
        return $query->execute($data);
    }

    private function prepareData($where, array $settings = []): ?array
    {
        $where_str = $settings_str = "";
        $data = [];

        if (is_numeric($where)) {
            $where = ["id" => $where];
        }

        if ($settings) {
            $settings_str = "SET ";
            $comma = "";
            foreach ($settings as $key => $val) {
                $settings_str .= "{$comma}{$this->db->provider->escape}{$key}{$this->db->provider->escape}=:__SET{$key}";
                $data["__SET{$key}"] = $val;
                $comma = ", ";
            }
        }

        if ($where) {
            $where_str = "WHERE ";
            $comma = "";
            foreach ($where as $key => $val) {
                $where_str .= "{$comma}{$this->db->provider->escape}{$key}{$this->db->provider->escape}=:__EXP{$key}";
                $data["__EXP{$key}"] = $val;
                $comma = ", ";
            }
        }

        return [$data, $where_str, $settings_str];
    }
}