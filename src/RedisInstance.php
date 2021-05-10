<?php

namespace Lysice\Cache;

use Illuminate\Support\Facades\Redis;
use Lysice\Cache\Concerns\CacheConcern;

/**
 * Class RedisInstance
 * @package Lysice\APCu
 */
class RedisInstance implements CacheConcern {
    /**
     * @var Redis
     */
    protected $redis = null;

    /**
     * @return \Illuminate\Redis\Connections\Connection|null
     */
    public function __construct()
    {
        $config = config('2cache') ?: [];
        if (empty($config)) {
            throw new \InvalidArgumentException("config file not found! please publish first!");
        }
        if ($this->redis == null) {
            $this->redis = Redis::connection($config['data_connection']);
        }

        return $this->redis;
    }

    /**
     * @param callable $callback
     * @return bool
     */
    public function transaction(Callable $callback)
    {
        $this->redis->multi();
        $callback($this);
        $this->redis->exec();
        return true;
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value, $ttl = 0)
    {
        if (is_array($key)) {
            $this->transaction(function ($instance) use ($key, $value, $ttl){
                if (empty($ttl)) {
                    foreach ($key as $k => $v) {
                        $instance->setExist($k, $v);
                    }
                    return true;
                }

                foreach ($key as $k => $v) {
                    $instance->setExist($k, $v, $ttl);
                }

                return true;
            });
        } else {
            return $this->setExist($key, $value, $ttl);
        }
    }

    /**
     * @param $key
     * @param $value
     * @param int $ttl
     * @return mixed
     */
    private function setExist($key, $value, $ttl = 0)
    {
        if ($ttl == 0) {
            return $this->redis->set($key, $value);
        } else {
            if ($this->redis->exists($key)) {
                return $this->redis->set($key, $value, 'ex', $ttl);
            } else {
                return $this->redis->set($key, $value, 'nx', $ttl);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function keyInfo(string $key)
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function update($key, $old, $new)
    {
        return $this->redis->set($key, $new);
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        return $this->redis->flushDB();
    }

    /**
     * @inheritDoc
     */
    public function decrease($key = '', $step = 1, $ttl = 0)
    {
        $this->redis->decr($key, $step);
        if (!empty($ttl)) {
            $this->redis->expire($key, $ttl);
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function delete($keys)
    {
        if (is_array($keys)) {
            return $this->transaction(function ($instance) use ($keys) {
                foreach ($keys as $key) {
                    $instance->redis->del($key);
                }
                return true;
            });
        }

        return $this->redis->del($keys);
    }

    /**
     * @inheritDoc
     */
    public function exists($keys)
    {
        if(is_array($keys)) {
            $arr = [];
            foreach ($keys as $key) {
                $arr[$key] = $this->redis->exists($key);
            }

            return $arr;
        }

        return $this->redis->exists($keys);
    }

    /**
     * @inheritDoc
     */
    public function get($key)
    {
        if(is_array($key)) {
            $arr = [];
            foreach ($key as $item) {
                $arr[$item] = $this->redis->get($item);
            }
            return $arr;
        }

        return $this->redis->get($key);
    }

    /**
     * @inheritDoc
     */
    public function increase($key = '', $step = 1, $ttl = 0)
    {
        $this->redis->incr($key, $step);
        if (!empty($ttl)) {
            $this->redis->expire($key, $ttl);
        }

        return true;
    }

    public function info()
    {
        return $this->redis->info();
    }
}
