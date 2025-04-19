<?php

/*
	GitHub: https://github.com/matheusjohannaraujo/simple-redis
	Country: Brasil
	State: Pernambuco
	Developer: Matheus Johann Araujo
	Date: 2025-04-19
*/

namespace MJohann\Packlib;

use MJohann\Packlib\SimpleRedis;

class SimpleRedisFacade
{
    protected static ?SimpleRedis $instance = null;

    public static function init(array $args = []): void
    {
        if (is_null(self::$instance)) {
            $reflection = new \ReflectionClass(SimpleRedis::class);
            self::$instance = $reflection->newInstanceArgs($args);
            self::$instance->open();
        }
    }

    protected static function getInstance(): SimpleRedis
    {
        if (is_null(self::$instance)) {
            throw new \Exception("SimpleRedisFacade not initialized. Use SimpleRedisFacade::init([...]) first.");
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
