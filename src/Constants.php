<?php

namespace Lysice\Cache;

class Constants
{
    const CACHE_TYPE_APCU = 1;
    const CACHE_TYPE_YAC = 2;

    const SYNC_MODE_PUBSUB = 1;
    const SYNC_MODE_SYNC = 2;
    const SYNC_MODE_JOB = 3;

    const COMPRESS_FUNC_ZLIB = 1;
    const COMPRESS_FUNC_DEFLATE = 2;
    const COMPRESS_FUNC_GZIP = 3;

    const COMPRESS_MAX_LENGTH = 1024 * 1024 * 64;
}
