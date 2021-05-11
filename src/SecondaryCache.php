<?php

namespace Lysice\Cache;

use Illuminate\Support\Facades\Facade;

class SecondaryCache extends Facade {
    protected static function getFacadeAccessor()
    {
        return CacheManager::class;
    }
}
