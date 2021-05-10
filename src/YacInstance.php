<?php

namespace Lysice\Cache;

class YacInstance{
    /**
     * @var \Yac
     */
    protected $yac;

    public function __construct()
    {
        $this->yac = new \Yac();
    }

    /**
     * whether the extension is loaded
     * @return bool
     */
    public function loaded()
    {
        return extension_loaded('yac');
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
     * @param string $message
     */
    public function throwsException($message = 'extension yac is not enabled')
    {
        throw new \LogicException($message);
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value = null, $ttl = 0)
    {
        return $this->apply(function ($instance) use ($key, $value, $ttl) {
            if (is_array($key)) {
                return $this->yac->add($key);
            }

            return $this->yac->set($key, $value, $ttl);
        });
    }

    /**
     * @inheritDoc
     */
    public function update($key, $old, $new)
    {
        return $this->set($key, $new);
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        return $this->yac->flush();
    }

    /**
     * @inheritDoc
     */
    public function decrease($key = '', $step = 1, $ttl = 0)
    {
        $value = $this->get($key);
        return $this->yac->set($key, $value ? $value - 1 : -1, $ttl);
    }

    /**
     * @inheritDoc
     */
    public function delete($keys)
    {
        return $this->yac->delete($keys);
    }

    /**
     * @inheritDoc
     */
    public function exists($keys)
    {
        $keys = (array)$keys;
        $res = [];
        foreach ($keys as $key) {
            $r = $this->get($key);
            if ($r) {
                $res[$key] = true;
            }
        }
        return $res;
    }

    /**
     * @inheritDoc
     */
    public function get($key)
    {
        return $this->yac->get($key);
    }

    /**
     * @inheritDoc
     */
    public function increase($key = '', $step = 1, $ttl = 0)
    {
        $value = $this->get($key);
        return $this->set($key, $value ? $value + 1 : 1, $ttl);
    }

    public function info()
    {
        return $this->yac->info();
    }
}
