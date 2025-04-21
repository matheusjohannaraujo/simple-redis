<?php

/*
	GitHub: https://github.com/matheusjohannaraujo/simple-redis
	Country: Brasil
	State: Pernambuco
	Developer: Matheus Johann Araujo
	Date: 2025-04-21
*/

namespace MJohann\Packlib\Facades;

use MJohann\Packlib\SimpleRedis as SimpleRedisClass;

/**
 * Facade for the SimpleRedis providing static access to Redis operations.
 *
 * @method static void init(string $host = "localhost", int $port = 6379, string $password = "password", string $username = "", string $scheme = "tcp", int $read_write_timeout = 0) Initializes a new Redis connection.
 * @method static SimpleRedis getInstance() Retrieves the current Redis connection instance.
 * @method static mixed __callStatic(string $method, array $arguments) Dynamically calls a method on the SimpleRedis instance.
 */
class SimpleRedis
{

    protected static ?SimpleRedisClass $instance = null;

    /**
     * Initializes a new Redis connection configuration.
     *
     * @param string $host Redis server hostname or IP address (default: "localhost")
     * @param int $port Redis server port number (default: 6379)
     * @param string $password Password used for authentication (default: "password")
     * @param string $username Username used for authentication, if applicable (default: "")
     * @param string $scheme Connection scheme, e.g., "tcp" or "unix" (default: "tcp")
     * @param int $read_write_timeout Timeout in seconds for read/write operations (default: 0, which means no timeout)
     *
     * @return void
     */
    public static function init(): void
    {
        if (is_null(self::$instance)) {
            $reflection = new \ReflectionClass(SimpleRedisClass::class);
            self::$instance = $reflection->newInstanceArgs(func_get_args());
            self::$instance->open();
        }
    }

    /**
     * Returns the singleton instance of SimpleRedis.
     * Throws an exception if the instance has not been initialized.
     *
     * @throws \Exception
     * @return SimpleRedis
     */
    public static function getInstance(): SimpleRedisClass
    {
        if (is_null(self::$instance)) {
            throw new \Exception("Facade not initialized. Use \MJohann\Packlib\Facades\SimpleRedis::init([...]) first.");
        }

        return self::$instance;
    }

    /**
     * Magic method to forward static calls to the underlying SimpleRedis instance.
     * If the method does not exist on the instance, a BadMethodCallException is thrown.
     *
     * @param string $method
     * @param array $arguments
     * @throws \BadMethodCallException
     * @return mixed
     */
    public static function __callStatic($method, $arguments)
    {
        $instance = self::getInstance();

        if (!method_exists($instance, $method)) {
            throw new \BadMethodCallException("Method {$method} not exist in SimpleRedis.");
        }

        return $instance->$method(...$arguments);
    }
}
