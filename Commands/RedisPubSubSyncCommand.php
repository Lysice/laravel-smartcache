<?php

namespace Lysice\Cache\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Lysice\Cache\RedisInstance;

/**
 * Class RedisPubSubSyncCommand
 * @package Lysice\Cache\Commands
 */
class RedisPubSubSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '2cache:sync';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '2cache redis data sync command';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle()
    {
        // 设置禁止超时
        set_time_limit(0);
        ini_set('default_socket_timeout', -1);
        $config = config('2cache');
        if ($config['pub_connection'] == $config['data_connection']) {
            throw new \Exception('error: redis sub db cannot be equals to data db.');
        }
        try {
            $command = $this;
            $redis = app(RedisInstance::class);
            // 订阅频道 接收消息 并处理
            Redis::connection($config['pub_connection'])->subscribe($config['redis_channel'], function ($message, $channel) use ($command, $redis){
                $command->log('message received' . $message);
                $command->log('set start');
                $data = json_decode($message, true);
                $redis->set($data['k'], json_encode($data['v']), $data['t']);
                $command->log('set start');
            });
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            Log::error($exception->getTraceAsString());
        }
    }

    public function log($message)
    {
        $config = config('2cache');
        if ($config['log']) {
            Log::info($message);
        }
    }
}
