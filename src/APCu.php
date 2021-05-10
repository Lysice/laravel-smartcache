<?php

namespace Lysice\Cache;

use Lysice\Cache\Concerns\CacheConcern;

/**
 * Class APCu
 * @package Lysice\APCu
 */
class APCu implements CacheConcern{

    /**
     * whether the extension is loaded
     * @return bool
     */
    public function loaded()
    {
        return extension_loaded('apcu') && apcu_enabled();
    }

    /**
     * @param string $message
     */
    public function throwsException($message = 'extension apcu is not enabled')
    {
        throw new \LogicException($message);
    }

    /**
     * @param callable $callback
     * @return mixed
     */
    public function apply(Callable $callback)
    {
        if ($this->loaded()) {
            return $callback($this);
        }
        $this->throwsException();
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value = null, $ttl = null)
    {
        return $this->apply(function ($apcu) use ($key, $value, $ttl) {
            $keys = is_array($key) ? array_keys($key) : $key;
            if (empty($ttl)) {
                if ($apcu->exists($keys)) {
                    return apcu_store($key, $value);
                }

                return apcu_add($key, $value);
            }

            if ($apcu->exists($key)) {
                return apcu_store($key, $value, $ttl);
            }

            return apcu_add($key, $value, $ttl);
        });
    }
    /**
     * @inheritDoc
     */
    public function keyInfo(string $key)
    {
        return $this->apply(function () use ($key) {
            return apcu_key_info($key);
        });
    }

    /**
     * @inheritDoc
     */
    public function update($key, $old, $new)
    {
        return $this->apply(function ($apcu) use ($key, $old, $new){
            if ($apcu->exists($key)) {
                return apcu_cas($key, $old, $new);
            }

            $this->throwsException('update key doesnot exists:' . $key);
        });
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        return $this->apply(function () {
            return apcu_clear_cache();
        });
    }

    public function decrease($key = '', $step = 1, $ttl = 0)
    {
        return $this->apply(function ($apcu) use ($key, $step, $ttl){
            return apcu_dec($key, $step);
        });
    }

    /**
     * @inheritDoc
     */
    public function delete($keys)
    {
        return $this->apply(function () use ($keys) {
            return apcu_delete($keys);
        });
    }

    /**
     * @inheritDoc
     */
    public function exists($keys)
    {
        return $this->apply(function () use ($keys) {
            return apcu_exists($keys);
        });
    }

    /**
     * @inheritDoc
     */
    public function get($key)
    {
        return $this->apply(function () use ($key) {
            return apcu_fetch($key);
        });
    }

    /**
     * @inheritDoc
     */
    public function increase($key = '', $step = 1, $ttl = 0)
    {
        return $this->apply(function ($apcu) use ($key, $step, $ttl) {
            if ($apcu->exists()) {
                return apcu_inc($key, $step);
            } else {
                return apcu_inc($key, $step, $success, $ttl);
            }
        });
    }

    public function info()
    {
        return apcu_sma_info();
    }
}
