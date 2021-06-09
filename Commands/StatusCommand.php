<?php

namespace Lysice\Cache\Commands;

use Illuminate\Console\Command;
use Lysice\Cache\APCu;
use Lysice\Cache\Constants;
use Lysice\Cache\CreatesRequest;
use Lysice\Cache\YacInstance;

class StatusCommand extends Command
{
    use CreatesRequest;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '2cache:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '2cache memory status';

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
            $result = $this->fpmHandle( Constants::CACHE_TYPE_YAC);
            if (is_object($result)) {
                $result = get_object_vars($result);
                $this->line('laravel-smartcache');
                $this->displayTables('Yac info:', $result);
            }
        } else if ($config['cache_type'] == Constants::CACHE_TYPE_APCU){
            $result = $this->fpmHandle( Constants::CACHE_TYPE_APCU);
            if (is_object($result)) {
                $result = get_object_vars($result);
                if (isset($result['block_lists'][0][0]['size'])) {
                    $result['block_lists-size'] = $result['block_lists'][0][0]['size'];
                }
                if (isset($result['block_lists'][0][0]['offset'])) {
                    $result['block_lists-offset'] = $result['block_lists'][0][0]['offset'];
                }
                unset($result['block_lists']);
                $this->line('laravel-smartcache');
                $this->displayTables('APCu info:', $result);
            }
        }
    }

    public function fpmHandle($mode)
    {
        try {
            $response = $this->sendRequest('status/' . $mode, ['mode' => $mode]);

            if (isset($response->result->message)) {
                $this->warn($response->result->message);
                return 1;
            }
            return $response->result;
        } catch (LushRequestException $e) {
            $this->error($e->getMessage());
            $this->error('Url: '.$e->getRequest()->getUrl());
            return $e->getCode();
        }
    }

    /**
     * Display info tables.
     *
     * @param $data
     */
    protected function displayTables($title, $data)
    {
        $this->line($title);
        $this->table([], $this->parseTable($data));
    }

    /**
     * Make up the table for console display.
     *
     * @param $input
     *
     * @return array
     */
    protected function parseTable($input)
    {
        $input = (array) $input;

        return array_map(function ($key, $value) {
            return [
                'key'       => $key,
                'value'     => $value,
            ];
        }, array_keys($input), $input);
    }
}
