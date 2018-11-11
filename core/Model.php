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
class Model extends \KodigenPHP\Database\QueryBuilder
{
    public $table;
    public $primary = "id";
    public $fetch_as = \PDO::FETCH_ASSOC;

    public $db;

    function __construct(\KodigenPHP\Database &$db = null)
    {
        parent::__construct();

        if ($db === null) {
            $this->db = \KodigenPHP\Database::getInstance();
        } else {
            $this->db = &$db;
        }
    }

    public function get($where = null) {
        if ($where) $this->where($where);

        $this->limit(1);

        $query = $this->getQuery(\KodigenPHP\Database\QueryBuilder::TYPE_SELECT, $this->table);
        $state = $this->db->prepare($query);
        $state->execute($this->getPrepareData());
        $this->resetBuilder();
        return $state->fetchAll($this->fetch_as);
    }

    public function getAll($where = null) {
        if ($where) $this->where($where);

        $query = $this->getQuery(\KodigenPHP\Database\QueryBuilder::TYPE_SELECT, $this->table);
        $state = $this->db->prepare($query);
        $state->execute($this->getPrepareData());
        $this->resetBuilder();
        return $state->fetchAll($this->fetch_as);
    }
}