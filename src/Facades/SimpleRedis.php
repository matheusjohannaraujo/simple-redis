<?php

/*
	GitHub: https://github.com/matheusjohannaraujo/simple-redis
	Country: Brasil
	State: Pernambuco
	Developer: Matheus Johann Araujo
	Date: 2025-04-20
*/

namespace MJohann\Packlib\Facades;

use MJohann\Packlib\SimpleRedis as SimpleRedisClass;

class SimpleRedis
{
    protected static ?SimpleRedisClass $instance = null;

    /**
     * Configures Redis connection parameters.
     *
     * @param array{
     *     host: string,
     *     port: int,
     *     password: string,
     *     username: string,
     *     scheme: string,
     *     read_write_timeout: int
     * } $args
     * @return void
     */
    public static function init(array $args = []): void
    {
        if (is_null(self::$instance)) {
            $reflection = new \ReflectionClass(SimpleRedisClass::class);
            self::$instance = $reflection->newInstanceArgs($args);
            self::$instance->open();
        }
    }

    protected static function getInstance(): SimpleRedisClass
    {
        if (is_null(self::$instance)) {
            throw new \Exception("Facade not initialized. Use \MJohann\Packlib\Facades\SimpleRedis::init([...]) first.");
        }

        return self::$instance;
    }

    public static function __callStatic($method, $arguments)
    {
        $instance = self::getInstance();

        if (!method_exists($instance, $method)) {
            throw new \BadMethodCallException("Method {$method} not exist in SimpleRedis.");
        }

        return $instance->$method(...$arguments);
    }
}
