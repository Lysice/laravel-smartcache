<?php

namespace Lysice\Cache;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Class RedisSyncJob
 * @package Lysice\Cache
 */
class RedisSyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var int
     */
    protected $ttl;

    /**
     * RedisSyncJob constructor.
     * @param $key
     * @param $ttl
     * @param $value
     */
    public function __construct($key, $ttl, $value)
    {
        $this->key = $key;
        $this->value = is_array($value) ? json_encode($value) : $value;
        $this->ttl = $ttl;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->log('start queue job');

        app(RedisInstance::class)->set($this->key, $this->value, $this->ttl);
        $this->log('end queue job');
    }

    /**
     * @param $message
     */
    public function log($message)
    {
        $config = config('2cache');
        if ($config['log']) {
            Log::info($message);
        }
    }
}
