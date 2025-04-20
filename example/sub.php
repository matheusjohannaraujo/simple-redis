<?php

/*
Always run the SUB process before the PUB, as the subscriber listens for messages sent by the publisher.
*/

use MJohann\Packlib\Facades\SimpleRedis;

require_once "../vendor/autoload.php";

// Using a Facade to instantiate and configure an instance of the SimpleRedis class.
SimpleRedis::init();

// Subscribe to the "channel" and define a callback function 
// to handle incoming messages
SimpleRedis::sub("channel", function ($message, $channel) {
    echo "Received message: $message | From channel: $channel" . PHP_EOL;
});

// Wait for incoming messages and keep the callback handler active
// The parameter defines how long to wait (in microseconds)
SimpleRedis::waitCallbacks(1000000); // Waits for 1 second (1,000,000 microseconds)

// Close the Redis connection
SimpleRedis::close();
