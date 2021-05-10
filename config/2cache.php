<?php

return [
    'data_connection' => 'default',
    'pub_connection' => '',
    'cache_type' => \Lysice\Cache\Constants::CACHE_TYPE_YAC,
    'sync_mode' => \Lysice\Cache\Constants::SYNC_MODE_PUBSUB,
    'redis_channel' => env('PUB_CHANNEL', 'default'),
    'log' => true
];
