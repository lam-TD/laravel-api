<?php

namespace App\Modules\Version;

use Illuminate\Support\Collection;

class VersionStorageData
{
    protected string $path;

    protected Collection $collection;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public static function fromFile(string $path)
    {
        return new self($path);
    }

    public function withCollection($collection)
    {
        $this->collection = $collection;

        return $this;
    }

    public function toStreamDownload($name, ?callable $callback = null)
    {
        return response()->streamDownload(function () use ($callback) {
            // chunk reader file

            if ($callback) {
                $callback($this->collection);
            }
        }, $name);
    }
}
