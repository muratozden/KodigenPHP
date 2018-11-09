<?php namespace KodigenPHP\Database\Providers;
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
class Mysql
{
    public $escape = '`';

    public $dsn;
    public $username = null;
    public $password = null;

    function __construct(&$config)
    {
        $this->dsn = "mysql:host={$config["hostname"]};dbname={$config["database"]}";
        $this->username = $config["username"];
        $this->password = $config["password"];
    }
}