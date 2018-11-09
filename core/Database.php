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
class Database extends \PDO
{
    public $config = [];
    public $provider = null;
    public $dsn = null;
    public $username = null;
    public $password = null;

    private static $instance;

    function __construct(string $config_name = null)
    {
        $config_name = $config_name ? $config_name : "default";
        $this->config = \KodigenPHP\Config::getInstance()->database->{$config_name};

        $provider_class = "\\KodigenPHP\\Database\\Providers\\" . $this->config["provider"];
        $this->provider = new $provider_class($this->config);

        parent::__construct($this->provider->dsn, $this->provider->username, $this->provider->password);
        parent::setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public static function getInstance()
    {
        if (! self::$instance) {
            self::$instance = new Database();
        }

        return self::$instance;
    }
}