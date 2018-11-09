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
class Request
{
    public $segments = [];
    public $total_segments = 0;
    public $request_uri;
    public $namespace = null;
    public $controller_dir = null;
    public $controller_name = null;
    public $controller_absolute_path = null;
    public $controller_object_name = null;
    public $method_name = null;
    public $arguments = null;

    private static $instance;

    function __construct()
    {
        $config = \KodigenPHP\Config::getInstance();

        $this->request_uri = $_SERVER["REQUEST_URI"];
        $this->segments = explode("/", $this->request_uri);
        $this->segments = array_values(array_filter($this->segments));
        $this->total_segments = count($this->segments);

        if ($this->total_segments == 0) {
            $this->controller_name = $config->routes->default_controller;
            $this->method_name = "index";
        } else {
            foreach ($config->routes->rules as $rule => $handler) {
                if (preg_match("#^\/{$rule}$#", $this->request_uri, $args)) {

                    $handler_splitted = explode(":", $handler);
                    $controller_side = $handler_splitted[0];
                    $controller_splitted = explode("/", $controller_side);
                    $total_controller_split = count($controller_splitted);

                    if ($total_controller_split > 1) {
                        # Seems the controller side has more than one path
                        for ($i = 0; $i < $total_controller_split; $i++) {
                            switch ($total_controller_split) {
                                case $i + 1: # last index
                                    $this->controller_name = $controller_splitted[$i];
                                    break;
                                default:
                                    $this->namespace .= $controller_splitted[$i] . "\\";
                                    $this->controller_dir .= $controller_splitted[$i] . "/";
                            }
                        }
                    } else {
                        # Otherwise, it just a controller name
                        $this->controller_name = $controller_splitted[0];
                    }

                    if (isset($handler_splitted[1])) {
                        # Seems the method side is exists
                        $method_side = $handler_splitted[1];
                        $method_splitted = explode("(", $method_side);
                        $total_method_split = count($method_splitted);
                        if ($total_method_split > 1 && $args) {
                            # Seems arguments are exists
                            $total_args = count($args);
                            $arguments_side = rtrim($method_splitted[1], ")");
                            $this->arguments = $arguments_side;
                            for ($i = 1; $i < $total_args; $i++) {
                                $this->arguments = str_replace('$' . $i, '"' . addslashes(urldecode($args[$i])) . '"', $this->arguments);
                            }
                        }
                        $this->method_name = $method_splitted[0];
                    } else {
                        # Otherwise, set the default method name
                        $this->method_name = "index";
                    }

                    break; # Break the loop if rule matches
                }
            }
        }

        if ($config->routes->auto_routing && $this->controller_name === null) {
            if ($this->total_segments == 1) {
                $this->controller_name = ucfirst($this->segments[0]);
                $this->method_name = "index";
            } else {
                for ($i = 0; $i < $this->total_segments; $i++) {
                    switch ($this->total_segments) {
                        case $i + 1: # last index
                            $this->method_name = $this->segments[$i];
                            break;
                        case $i + 2: # last 2nd index
                            $this->controller_name = ucfirst($this->segments[$i]);
                            break;
                        default:
                            $segment_uc = ucfirst($this->segments[$i]);
                            $this->namespace .= "{$segment_uc}\\";
                            $this->controller_dir .= "{$segment_uc}/";
                    }
                }
            }
        }

        if ($this->controller_name) {
            $this->controller_absolute_path = APP . "/Controllers/" . $this->controller_dir . $this->controller_name;
            $this->controller_object_name = "\\App\\Controllers\\" . $this->namespace . $this->controller_name;
        }

        if ($config->routes->auto_routing && !file_exists($this->controller_absolute_path . ".php")) {
            $method_name_uc = ucfirst($this->method_name);
            $this->controller_absolute_path .= "/{$method_name_uc}";
            $this->controller_object_name .= "\\{$method_name_uc}";
            $this->method_name = "index";
        }
    }

    public function post(string $key, string $default = null)
    {
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }

    public function get(string $key, string $default = null)
    {
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }

    public function segment(int $number): string
    {
        return isset($this->segments[$number]) ? $this->segments[$number] : null;
    }

    public function server(string $key): string
    {
        return isset($_SERVER[$key]) ? $_SERVER[$key] : null;
    }

    public function userAgent(): string
    {
        return $_SERVER["HTTP_USER_AGENT"] ?? null;
    }

    public function ip(): string
    {
        return $_SERVER["REMOTE_ADDR"];
    }

    public function method(bool $lowercase = false): string
    {
        return $lowercase ? strtolower($_SERVER["REQUEST_METHOD"]) : $_SERVER["REQUEST_METHOD"];
    }

    public function isPost()
    {
        return $_SERVER["REQUEST_METHOD"] === "POST";
    }

    public function isGet()
    {
        return $_SERVER["REQUEST_METHOD"] === "GET";
    }

    public static function getInstance()
    {
        if (! self::$instance) {
            self::$instance = new Request();
        }

        return self::$instance;
    }
}