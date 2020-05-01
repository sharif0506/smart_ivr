<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class CacheController
{
    public function getCacheData($key)
    {
        return Cache::get($key);
    }

    public function setCacheData($key, $value, $duration = LOGIN_DURATION)
    {
        Cache::put($key, $value, $duration);
    }

    public function updateCacheData($key, $value, $duration = LOGIN_DURATION)
    {
        if ($this->hasCacheValue($key)) {
            $this->removeCacheData($key);
        }
        $this->setCacheData($key, $value, $duration);
    }

    public function removeCacheData($key)
    {
        Cache::forget($key);
    }

    public function hasCacheValue($key)
    {
        return Cache::has($key);
    }
}
