<?php

require_once "../vendor/autoload.php";

use MJohann\Packlib\SimpleRedis;

// Instantiate and configure Redis connection
$redis = new SimpleRedis();
$redis->config();
$redis->open();

// SET: store the key "name" with value "Matheus" and a TTL of 60 seconds
echo "Set: ", $redis->set("name", "Matheus", 60), PHP_EOL, PHP_EOL;

// GET: retrieve the value of the key "name"
echo "Get: ", $redis->get("name"), PHP_EOL, PHP_EOL;

// Wait 10 seconds before deleting the key
sleep(10);

// DEL: delete the key "name"
echo "Del: ", $redis->del("name"), PHP_EOL, PHP_EOL;

// Define list key
$keyList = "list:names";

// LPUSH: add elements to the list
echo "List push: ",
$redis->listPush("Matheus", $keyList), " | ",
$redis->listPush("Johann", $keyList), " | ",
$redis->listPush("Araújo", $keyList), PHP_EOL, PHP_EOL;

// LLEN: get the size of the list
echo "List size: ", $redis->listSize($keyList), PHP_EOL, PHP_EOL;

// LINDEX: access the first element (index 0) of the list
echo "List index [0]: ", $redis->listIndex(0, $keyList), PHP_EOL, PHP_EOL;

// LRANGE: retrieve all elements from the list
echo "List all: ";
var_dump($redis->listAll($keyList));
echo PHP_EOL;

// LPOP: pop and print each element from the list every 5 seconds
echo "Popping list items..." . PHP_EOL;
while (($value = $redis->listPop($keyList)) !== null) {
    echo "Name: ", $value, PHP_EOL;
    // Wait 5 seconds
    sleep(5);
}

// Close Redis connection
$redis->close();
