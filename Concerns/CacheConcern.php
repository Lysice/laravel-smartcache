<?php

namespace Lysice\Cache\Concerns;

/**
 * Interface CacheConcern
 * @package Lysice\Concerns
 */
interface CacheConcern {
    /**
     * set values/value
     * @param $key array|string
     * @param $value
     * @param $ttl int
     * @return mixed
     */
    public function set($key, $value, $ttl);

    /**
     * @param $key string
     * @param $old int
     * @param $new int
     * @return mixed
     */
    public function update($key, $old, $new);

    /**
     * clear cache
     * @return mixed
     */
    public function clear();

    /**
     * @param string $key
     * @param int $step
     * @param int $ttl
     * @return mixed
     */
    public function decrease($key = '', $step = 1, $ttl = 0);

    /**
     * @param $keys array | string
     * @return mixed
     */
    public function delete($keys);

    /**
     * @param $keys array | string
     * @return mixed
     */
    public function exists($keys);

    /**
     * get values/values
     * @param $key array | string
     * @return mixed
     */
    public function get($key);

    /**
     * @param $key
     * @param int $step
     * @param $ttl
     * @return mixed
     */
    public function increase($key = '', $step = 1, $ttl = 0);

    /**
     * @return mixed
     */
    public function info();
}
