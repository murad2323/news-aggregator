<?php

namespace App;

use mysqli;

class Database
{
    private static ?Database $instance = null;
    private mysqli $connection;

    private function __construct()
    {
        $this->connection = new mysqli('db', 'news_user', 'news_pass', 'news_aggregator');
        if ($this->connection->connect_error) {
            throw new \RuntimeException('Ошибка подключения к БД: ' . $this->connection->connect_error);
        }
        $this->connection->set_charset('utf8mb4');
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): mysqli
    {
        return $this->connection;
    }

    private function __clone() {}
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
}