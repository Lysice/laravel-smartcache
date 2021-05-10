<?php

namespace Lysice\Cache;

use Illuminate\Support\ServiceProvider;
use Lysice\Cache\Commands\RedisPubSubSyncCommand;

/**
 * Class CacheServiceProvider
 * @package Lysice\APCu
 */
class CacheServiceProvider extends ServiceProvider {

    protected $commands = [
        RedisPubSubSyncCommand::class
    ];

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/2cache.php' => config_path('2cache.php'),
            ], 'config');
        }
    }

    public function register()
    {
        $this->app->singleton(CacheManager::class, function () {
            return new CacheManager(
                app()->make(RedisInstance::class)
            );
        });
        $this->app->singleton(APCu::class, function () {
           return new APCu();
        });
        $this->app->singleton(RedisInstance::class, function() {
            return new RedisInstance();
        });
        $this->app->singleton(YacInstance::class, function () {
            return new YacInstance();
        });
        $this->commands($this->commands);
    }
}
