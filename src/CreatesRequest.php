<?php

namespace Lysice\Cache;

use Appstract\LushHttp\LushFacade as Lush;
use Illuminate\Support\Facades\Crypt as Crypt;

trait CreatesRequest
{
    /**
     * @param $url
     * @param $parameters
     * @return \Appstract\LushHttp\Response\LushResponse
     */
    public function sendRequest($url, $parameters = [])
    {
        return Lush::headers([])
            ->options(['verify_ssl' => config('2cache.verify_ssl', false),'verify_host' => config('2cache.verify_host',2)])
            ->get(config('2cache.url').'/'. config('2cache.prefix') . '/'.$url,
            array_merge(['key' => Crypt::encrypt(config('2cache.key'))], $parameters)
        );
    }
}
