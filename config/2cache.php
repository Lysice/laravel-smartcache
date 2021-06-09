<?php

return [
    'data_connection' => 'default',
    'pub_connection' => '',
    'cache_type' => \Lysice\Cache\Constants::CACHE_TYPE_YAC,
    'sync_mode' => \Lysice\Cache\Constants::SYNC_MODE_SYNC,
    'redis_channel' => env('PUB_CHANNEL', 'default'),
    'log' => true,
    'url' => env('SMARTCACHE_URL', config('app.url')),
    'prefix' => '2cache-api',
    'verify_ssl' => false,
    'verify_host' => 2, // 0 for disabled
    'headers' => [],
    'key' => '2cache'
];
