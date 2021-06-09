<?php

namespace Lysice\Cache\Commands;

use Illuminate\Console\Command;
use Lysice\Cache\APCu;
use Lysice\Cache\Constants;
use Lysice\Cache\CreatesRequest;
use Lysice\Cache\YacInstance;

class Clear2CacheCommand extends Command
{
    use CreatesRequest;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '2cache:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'clear 2cache';

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
     *
     * @return mixed
     */
    public function handle()
    {
        $config = config('2cache');
        if($config['cache_type'] == \Lysice\Cache\Constants::CACHE_TYPE_YAC) {
            $this->fpmHandle(\Lysice\Cache\Constants::CACHE_TYPE_YAC, 'Yac cache cleared!');
        } else if ($config['cache_type'] == Constants::CACHE_TYPE_APCU){
            $this->fpmHandle(Constants::CACHE_TYPE_APCU, 'APCu cache cleared!');
        }
    }

    public function fpmHandle($mode, $message)
    {
        try {
            $this->line('clearing cache ...');

            $response = $this->sendRequest('clear/' . $mode, ['mode' => $mode]);

            if (isset($response->result->message)) {
                $this->warn($response->result->message);
                return 1;
            }
            $this->info($message);
        } catch (LushRequestException $e) {
            $this->error($e->getMessage());
            $this->error('Url: '.$e->getRequest()->getUrl());
            return $e->getCode();
        }
    }
}
