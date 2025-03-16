<?php

namespace App\Modules\Version;

class VersionCache
{
    public static function store()
    {
        return new self;
    }

    public function get($key)
    {
        return cache()->get($key);
    }

    public function put($key, $value, $minutes = null)
    {
        return cache()->put($key, $value, $minutes);
    }

    public function forget($key)
    {
        return cache()->forget($key);
    }

    public function remember($key, $callback)
    {
        return cache()->remember(key: $key, ttl: null, callback: $callback);
    }
}
