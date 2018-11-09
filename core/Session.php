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
class Session
{
    private static $instance;

    function __construct()
    {
        $config = \KodigenPHP\Config::getInstance();

        session_start($config->session->options);
    }

    public function set(string $key, $val) {
        $_SESSION[$key] = $val;
    }

    public function get(string $key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public function delete(string $key) {
        unset($_SESSION[$key]);
    }

    public function destroy(): bool {
        return session_destroy();
    }

    public static function getInstance()
    {
        if (! self::$instance) {
            self::$instance = new Session();
        }

        return self::$instance;
    }
}
