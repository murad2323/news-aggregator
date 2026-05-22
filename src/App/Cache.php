<?php

namespace App;

use Memcached;

class Cache
{
    private Memcached $memcached;
    private static ?Cache $instance = null;

    private function __construct()
    {
        $this->memcached = new Memcached();
        $this->memcached->addServer('cache', 11211);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get(string $key): mixed
    {
        return $this->memcached->get($key);
    }

    public function set(string $key, mixed $value, int $expiration = 300): bool
    {
        return $this->memcached->set($key, $value, $expiration);
    }

    private function __clone() {}
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
}