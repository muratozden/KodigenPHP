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
class KodigenPHP
{
    public $config;
    public $request;
    public $view;
    public $db;
    public $session;

    public function init()
    {
        $this->config = \KodigenPHP\Config::getInstance();
        $this->request = \KodigenPHP\Request::getInstance();
        $this->view = \KodigenPHP\View::getInstance();
        if ($this->config->database->auto_connect) {
            $this->db = \KodigenPHP\Database::getInstance();
        }
        if ($this->config->session->auto_start) {
            $this->session = \KodigenPHP\Session::getInstance();
        }
        if ($this->config->application->autoload_composer) {
            require ROOT . "/vendor/autoload.php";
        }
    }

    public function run()
    {
        if (file_exists("{$this->request->controller_absolute_path}.php")) {
            require_once "{$this->request->controller_absolute_path}.php";
            $object = new $this->request->controller_object_name();
            if (method_exists($object, $this->request->method_name)) {
                $this->loadVariables($object);
                try {
                    eval("\$object->{$this->request->method_name}({$this->request->arguments});");
                    return;
                } catch (\Throwable $e) {
                    $this->setError(500, "{$e->getMessage()} on {$e->getFile()} line {$e->getLine()}");
                }
            }
        }

        # Unhandled request's being the 404.
        $this->setError(404);
    }

    private function loadVariables(&$object)
    {
        $object->config = &$this->config;
        $object->request = &$this->request;
        $object->view = &$this->view;
        $object->db = &$this->db;
        $object->session = &$this->session;
    }

    private function setError(int $code, string $message = null)
    {
        require APP . "/Controllers/Errors.php";
        $errors = new \App\Controllers\Errors();
        $this->loadVariables($errors);
        if ($code == 404) {
            $errors->error_404();
        } else {
            $errors->error_500($message);
        }
    }
}