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
class View
{
    private $params = [];

    private static $instance;

    public function __set(string $key, string $val)
    {
        $this->params[$key] = $val;
    }

    public function __get(string $key): string
    {
        return isset($this->params[$key]) ? $this->params[$key] : null;
    }

    public function __isset(string $key): bool
    {
        return isset($this->params[$key]);
    }

    public function __unset(string $key)
    {
        unset($this->params[$key]);
    }

    public function load(string $name)
    {
        extract($this->params);
        require APP . "/Views/{$name}.php";
    }

    public function layout(string $view_name, string $layout_name = "default")
    {
        extract($this->params);
        $view = APP . "/Views/{$view_name}.php";
        require APP . "/Views/layouts/{$layout_name}.php";
    }

    public function params()
    {
        return $this->params;
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new View();
        }

        return self::$instance;
    }
}
