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
class Autoload
{
    private $classes = [
        "core" => [
            "Config",
            "Database/QueryBuilder",
            "Database/Providers/Mysql",
            "Database/Providers/Postgres",
            "Database",
            "Request",
            "Session",
            "Common",
            "KodigenPHP",
            "Model",
            "View",
            "Controller"
        ],
        "application" => [
            "Config/Constants",
            "Config/Routes",
            "Config/Application",
            "Config/Database",
            "Config/Sessions"
        ],
    ];

    public function init()
    {
        foreach ($this->classes as $folder => $classes) {
            foreach ($classes as $class) {
                require_once ROOT . "/{$folder}/{$class}.php";
            }
        }
    }

    public function register(string $class_name)
    {
        $filename = APP . "/" . str_replace("\\", "/", substr($class_name, 4)) . ".php";

        if (file_exists($filename)) {
            require_once $filename;
        }
    }
}