<?php

namespace App\Modules\Version;

use Illuminate\Support\Facades\File;

class VersionCompressorData
{
    public function __construct(protected string $path) {}

    public function path()
    {
        return $this->path;
    }

    public function linkTo(string $link)
    {
        $dir = dirname($link);

        if (! file_exists($dir)) {
            File::makeDirectory($dir, 755, true);
        }
        dd($this->path);
        link($this->path, $link);

        return $link;
    }
}
