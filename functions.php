<?php
if(!function_exists('setCacheValue')) {
    function setCacheValue($value, $compress = false, $compressFunction = \Lysice\Cache\Constants::COMPRESS_FUNC_GZIP, $compressLevel = 9)
    {
        if(is_array($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        if($compress) {
            if($compressFunction == \Lysice\Cache\Constants::COMPRESS_FUNC_ZLIB) {
                $value = gzcompress($value, $compressLevel);
            } else if($compressFunction == \Lysice\Cache\Constants::COMPRESS_FUNC_DEFLATE) {
                $value = gzdeflate($value, $compressLevel);
            } else if($compressFunction == \Lysice\Cache\Constants::COMPRESS_FUNC_GZIP) {
                $value = gzencode($value, $compressLevel);
            } else {
                // default
                $value = gzdeflate($value, $compressLevel);
            }
        }

        return $value;
    }
}

if(!function_exists('getCacheValue')) {
    function getCacheValue($value, $compress = false, $compressFunction =  \Lysice\Cache\Constants::COMPRESS_FUNC_GZIP)
    {
        if($compress) {
            if($compressFunction == \Lysice\Cache\Constants::COMPRESS_FUNC_GZIP) {
                return gzdecode($value, \Lysice\Cache\Constants::COMPRESS_MAX_LENGTH);
            } else if($compressFunction == \Lysice\Cache\Constants::COMPRESS_FUNC_DEFLATE) {
                return gzinflate($value, \Lysice\Cache\Constants::COMPRESS_MAX_LENGTH);
            } else if($compressFunction == \Lysice\Cache\Constants::COMPRESS_FUNC_ZLIB) {
                return gzuncompress($value, \Lysice\Cache\Constants::COMPRESS_MAX_LENGTH);
            } else {
                return gzinflate($value, \Lysice\Cache\Constants::COMPRESS_MAX_LENGTH);
            }
        }

        return $value;
    }
}
