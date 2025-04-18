<?php

/*
	GitHub: https://github.com/matheusjohannaraujo/simple-redis
	Country: Brasil
	State: Pernambuco
	Developer: Matheus Johann Araujo
	Date: 2025-04-18
*/

namespace MJohann\Packlib;

class SimpleRedis
{

    private static $host = null;
    private static $port = null;
    private static $password = null;
    private static $username = null;
    private static $scheme = null;
    private static $read_write_timeout = null;
    public static $redis = null;
    public $debug = false;
    private $callbacks = [];
    private $pubsub = null;

    public static function config(string $host = "localhost", string $port = "6379", string $password = "password", string $username = "", string $scheme = "tcp", int $read_write_timeout = 0): void
    {
        self::$host = $host;
        self::$port = $port;
        self::$password = $password;
        self::$username = $username;
        self::$scheme = $scheme;
        self::$read_write_timeout = $read_write_timeout;
    }

    public static function open(): ?\Predis\Client
    {
        if (self::$redis === null) {
            try {
                self::$redis = new \Predis\Client([
                    'scheme' => self::$scheme,
                    'host' => self::$host,
                    'port' => self::$port,
                    'username' => self::$username,
                    'password' => self::$password,
                    'read_write_timeout' => self::$read_write_timeout
                ]);
            } catch (\Throwable $th) {
                throw new \RuntimeException('Unable to connect to Redis: ' . $th->getMessage());
            }
        }
        return self::$redis;
    }

    public static function close(): void
    {
        if (self::$redis !== null) {
            self::$redis = null;
        }
    }

    public function get(string $key): mixed
    {
        if (self::$redis !== null) {
            return self::$redis?->get($key);
        }
        return null;
    }

    public function set(string $key, $value, int $time = 0): bool
    {
        if (self::$redis !== null) {
            if ($time > 0) {
                self::$redis?->setex($key, $time, $value); //seg
                //return self::$redis->psetex($key, $time, $value);//ms
            } else {
                self::$redis->set($key, $value);
            }
            return true;
        }
        return false;
    }

    public function del(string $key): bool
    {
        if (self::$redis !== null) {
            self::$redis?->del($key);
            return true;
        }
        return false;
    }

    public function pub(string $channel, string $message): bool
    {
        if (self::$redis !== null) {
            return self::$redis?->publish($channel, $message) === 1;
        }
        return false;
    }

    public function sub(string $channel, callable $callback): ?array
    {
        if (self::$redis !== null) {
            return [$channel => $this->callbacks[$channel] = $callback];
        }
        return null;
    }

    public function waitCallbacks(int $sleep = 0): bool
    {
        if (self::$redis !== null) {
            $this->pubsub = self::$redis->pubSubLoop();
            $this->callbacks["channel_break"] = function () {};
            $this->pubsub->subscribe(array_keys($this->callbacks));
            foreach ($this->pubsub as $message) {
                if ($this->debug) {
                    echo  "Kind: ", $message->kind, " | Channel: ", $message->channel, " | Payload: ", $message->payload, PHP_EOL;
                }
                if ($message->kind === "message" && in_array($message->channel, array_keys($this->callbacks))) {
                    $this->callbacks[$message->channel]($message->payload, $message->channel);
                }
                if ($message->kind === "message" && $message->channel === "channel_break" && $message->payload === "channel_break") {
                    $this->pubsub->unsubscribe();
                    $this->callbacks = [];
                    break;
                }
                if ($sleep > 0) {
                    usleep($sleep);
                }
            }
            unset($this->pubsub);
            return true;
        }
        return false;
    }

    private function listMode(string $mode): string
    {
        $mode = strtolower($mode);
        if ($mode !== "l" && $mode !== "r") {
            $mode = "l";
        }
        return $mode;
    }

    public function listPush(string $list, mixed $message, string $mode = "l"): bool
    {
        $mode = $this->listMode($mode);
        if (self::$redis !== null && $mode !== null) {
            if ($mode === "l") {
                self::$redis?->lpush($list, $message);
            } else if ($mode === "r") {
                self::$redis?->rpush($list, $message);
            }
            return true;
        }
        return false;
    }

    public function listPop(string $list, string $mode = "l"): mixed
    {
        $mode = $this->listMode($mode);
        if (self::$redis !== null && $mode !== null) {
            if ($mode === "l") {
                return self::$redis?->lpop($list);
            } else if ($mode === "r") {
                return self::$redis?->rpop($list);
            }
        }
        return null;
    }

    public function listSize(string $list): int
    {
        if (self::$redis !== null) {
            return self::$redis?->llen($list) ?? -1;
        }
        return -1;
    }

    public function listIndex(string $list, int $index): mixed
    {
        if (self::$redis !== null) {
            return self::$redis?->lindex($list, $index);
        }
        return null;
    }

    public function listRange(string $list, int $start = 0, int $stop = -1, bool $reverse = true): array
    {
        if (self::$redis !== null) {
            $array = self::$redis?->lrange($list, $start, $stop) ?? [];
        }
        return $reverse ? array_reverse($array) : $array;
    }

    public function listAll(string $list, bool $reverse = true)
    {
        if (self::$redis !== null) {
            return $this->listRange($list, 0, -1, $reverse);
        }
        return [];
    }

    public function listTrim(string $list, int $start, int $stop): bool
    {
        if (self::$redis !== null) {
            self::$redis?->ltrim($list, $start, $stop);
            return true;
        }
        return false;
    }

    public function listRemove(string $list, mixed $value, int $count = 0): int
    {
        if (self::$redis !== null) {
            return self::$redis?->lrem($list, $count, $value) ?? 0;
        }
        return 0;
    }

    public function listSet(string $list, int $index, mixed $value): bool
    {
        if (self::$redis !== null) {
            self::$redis?->lset($list, $index, $value);
            return true;
        }
        return false;
    }
}
