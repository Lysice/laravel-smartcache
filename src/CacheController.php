<?php

namespace Lysice\Cache;

use Illuminate\Routing\Controller;

class CacheController extends Controller {
    public function status($mode)
    {
        if ($mode == Constants::CACHE_TYPE_YAC) {
            return response()->json([
                'result' => app(YacInstance::class)->info()
            ]);
        } elseif($mode == Constants::CACHE_TYPE_APCU) {
            return response()->json([
                'result' => true,
                'data' => app(APCu::class)->info()
            ]);
        }
    }

    public function clear($mode)
    {
        if ($mode == Constants::CACHE_TYPE_YAC) {
            $r = app(YacInstance::class)->clear();
            return response()->json([
                'result' => $r ? $r : [
                    'message' => 'clear failed!'
                ]
            ]);
        } elseif ($mode == Constants::CACHE_TYPE_APCU) {
            $r = app(APCu::class)->clear();
            return response()->json([
                'result' => $r ? $r : [
                    'message' => 'clear failed!'
                ]
            ]);
        }
    }
}
