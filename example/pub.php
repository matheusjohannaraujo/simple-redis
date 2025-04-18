<?php

/*
Always run the SUB process before the PUB, as the subscriber listens for messages sent by the publisher.
*/

require_once "../vendor/autoload.php";

use MJohann\Packlib\SimpleRedis;

// Instantiate and configure Redis connection
$redis = new SimpleRedis();
$redis->config();
$redis->open();

// Publish 10 messages to the "channel"
for ($i = 1; $i <= 10; $i++) {
    $message = "Test " . $i;
    $status = $redis->pub("channel", $message);
    echo "Message: ", $message, " | ", ($status ? "Published" : "Not published"), PHP_EOL;
}

// Optional delay to allow time for subscribers to process the messages
sleep(5);

// Publish a special message to signal the end of communication
$status = $redis->pub("channel_break", "channel_break");
echo "Message: channel_break | ", ($status ? "Published" : "Not published"), PHP_EOL;

// Close the Redis connection
$redis->close();
