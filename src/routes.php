<?php

$router->get('clear/{mode}', ['uses' => 'CacheController@clear', 'as' => '2cache.clear']);
$router->get('status/{mode}', ['uses' => 'CacheController@status', 'as' => '2cache.status']);
