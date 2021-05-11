<?php

namespace Lysice\Cache;

use Illuminate\Support\Facades\Facade;

/**
 * Class SecondaryCache
 * @package Lysice\Cache
 * @method static remember(string $key, int $ttl, Callable $callback)
 */
class SecondaryCache extends Facade {
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return CacheManager::class;
    }
}
