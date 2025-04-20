<?php

use MJohann\Packlib\SimpleRedis;

require_once "../vendor/autoload.php";

// Create and configure a Redis connection
$redis = new SimpleRedis();
$redis->open();

// SET: Store the key "key" with the value "value" and a TTL (Time To Live) of 60 seconds
echo "Set: ";
var_dump($redis->set("key", "value", 60));
echo PHP_EOL, PHP_EOL;

// GET: Retrieve the value associated with the key "key"
echo "Get: ";
var_dump($redis->get("key"));
echo PHP_EOL, PHP_EOL;

// Wait 10 seconds before deleting the key
sleep(10);

// DEL: Delete the key "key" from Redis
echo "Del: ";
var_dump($redis->del("key"));
echo PHP_EOL, PHP_EOL;

// Define the key for the RPUSH list
$keyListRPUSH = "list:names_RPUSH";

// RPUSH: Add elements to the end (right) of the list
echo "List RPUSH: ";
var_dump($redis->listPush($keyListRPUSH, "Matheus", "r"));
echo PHP_EOL, PHP_EOL;

echo "List RPUSH: ";
var_dump($redis->listPush($keyListRPUSH, "Johann", "r"));
echo PHP_EOL, PHP_EOL;

echo "List RPUSH: ";
var_dump($redis->listPush($keyListRPUSH, "Araújo", "r"));
echo PHP_EOL, PHP_EOL;

// Update the value at index 0 in the list
echo "List set: ";
var_dump($redis->listSet($keyListRPUSH, 0, "Matheus Johann"));
echo PHP_EOL, PHP_EOL;

// Wait 5 seconds
sleep(5);

// Remove the element "Johann" from the list
echo "List remove: ";
var_dump($redis->listRemove($keyListRPUSH, "Johann"));
echo PHP_EOL;

// Define the key for the LPUSH list
$keyList = "list:names_LPUSH";

// LPUSH: Add elements to the beginning (left) of the list
echo "List LPUSH: ",
var_dump($redis->listPush($keyList, "Matheus"));
echo PHP_EOL, PHP_EOL;

echo "List LPUSH: ",
var_dump($redis->listPush($keyList, "Johann"));
echo PHP_EOL, PHP_EOL;

echo "List LPUSH: ",
var_dump($redis->listPush($keyList, "Araújo"));
echo PHP_EOL, PHP_EOL;

// LLEN: Get the total number of elements in the list
echo "List size: ";
var_dump($redis->listSize($keyList));
echo PHP_EOL, PHP_EOL;

// LINDEX: Retrieve the element at index 0
echo "List index [0]: ";
var_dump($redis->listIndex($keyList, 0));
echo PHP_EOL, PHP_EOL;

// LRANGE: Retrieve all elements from the list
echo "List all: ";
var_dump($redis->listAll($keyList));
echo PHP_EOL;

// Wait 5 seconds
sleep(5);

// LPOP: Remove and print one element from the beginning of the list every 5 seconds
echo "Popping list items..." . PHP_EOL;
while (($value = $redis->listPop($keyList)) !== null) {
    var_dump($value);
    echo PHP_EOL, PHP_EOL;
    sleep(5);
}

// Delete the entire list
echo "Del: ";
var_dump($redis->del($keyListRPUSH));
echo PHP_EOL, PHP_EOL;

// Close the Redis connection
$redis->close();
