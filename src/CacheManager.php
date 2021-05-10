<?php

namespace Lysice\Cache;

use Lysice\Cache\APCu;
use Lysice\Cache\Constants;
use Lysice\Cache\RedisInstance;
use Lysice\Cache\RedisSyncJob;
use Lysice\Cache\YacInstance;

/**
 * Class CacheManager
 * @package Lysice\APCu
 */
class CacheManager {
    protected $memoryCache = null;
    protected $redisInstance = null;
    protected $config = null;
    public function __construct(RedisInstance $redisInstance)
    {
        $config = config('2cache');
        if (empty($config)) {
            throw new \Exception("2cache config file not found,please publish first!");
        }
        $this->config = $config;
        $type = config('2cache.cache_type');
        if ($type == Constants::CACHE_TYPE_APCU) {
            $memoryCache = new APCu();
        } else {
            $memoryCache = new YacInstance();
        }

        $this->memoryCache = $memoryCache;
        $this->redisInstance = $redisInstance;
    }

    /**
     * * remember:
     * 1.get key from apcu
     * 2.if step 1 return null, get key from redis.
     * 3.if step 2 return null get key from default callback and cache it!
     * @param string $key
     * @param int $ttl
     * @param callable $callback
     * @return array|mixed
     */
    public function remember(string $key, int $ttl, Callable $callback) {
        $result = $this->memoryCache->get($key);
        if (empty($result)) {
            $result = $this->redisInstance->get($key);

            if (empty($result)) {
                $result = $callback();
                $this->cache($key, $ttl, $result);
                return $result;
            }
            // if redis cache not miss write it to memoryCache
            $this->memoryCache->set($key, $result, $ttl);
            return $result;
        }

        return $result;
    }

    /**
     * cache:
     * 1.first we cache it to memoryCache.
     * 2.then we cache it to redis.
     * @param string $key
     * @param int $ttl
     * @param string|array $value
     */
    public function cache(string $key, int $ttl, $value) {
        $this->memoryCache->set($key, $value, $ttl);
        // mode sync
        switch ($this->config['sync_mode']) {
            case Constants::SYNC_MODE_PUBSUB:
                $this->modePubSub($key, $ttl, $value);
                break;
            case Constants::SYNC_MODE_JOB:
                $this->modeJob($key, $ttl, $value);
                break;
            default:
                $this->redisInstance->set($key, $value, $ttl);
        }
    }

    /**
     * queue sync
     * @param $key
     * @param $ttl
     * @param $value
     */
    private function modeJob($key, $ttl, $value)
    {
        dispatch(new RedisSyncJob($key, $ttl, $value));
    }

    /**
     * redis sync
     * @param $key
     * @param $ttl
     * @param $value
     */
    private function modePubSub($key, $ttl, $value)
    {
        $value = [
            'k' => $key,
            't' => $ttl,
            'v' => $value
        ];

        $this->redisInstance->publish($this->config['redis_channel'], json_encode($value));
    }
}
