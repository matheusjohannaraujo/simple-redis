<?php

/*
Always run the SUB process before the PUB, as the subscriber listens for messages sent by the publisher.
*/

require_once "../vendor/autoload.php";

use MJohann\Packlib\SimpleRedis;

// Instantiate and configure Redis connection
$redis = new SimpleRedis();
$redis->open();

// Subscribe to the "channel" and define a callback function 
// to handle incoming messages
$redis->sub("channel", function ($message, $channel) {
    echo "Received message: $message | From channel: $channel" . PHP_EOL;
});

// Wait for incoming messages and keep the callback handler active
// The parameter defines how long to wait (in microseconds)
$redis->waitCallbacks(1000000); // Waits for 1 second (1,000,000 microseconds)

// Close the Redis connection
$redis->close();
