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
class Config
{
    public $routes = [];
    public $application = [];
    public $database = [];
    public $session = [];

    private static $instance;

    function __construct()
    {
        $this->routes = new \App\Config\Routes();
        $this->application = new \App\Config\Application();
        $this->database = new \App\Config\Database();
        $this->session = new \App\Config\Sessions();

        if ($this->application->autoload_dotenv) {
            $ini = parse_ini_file(ROOT . "/.env", true);
            foreach ($ini as $section => $values) {
                foreach ($values as $key => $val) {
                    $keys = explode(".", $key);
                    if (isset($keys[1])) {
                        $this->{$section}->{$keys[0]}[$keys[1]] = $val;
                    } else {
                        $this->{$section}->{$key} = $val;
                    }
                }
            }
        }
    }

    public static function getInstance()
    {
        if (! self::$instance) {
            self::$instance = new Config();
        }

        return self::$instance;
    }
}